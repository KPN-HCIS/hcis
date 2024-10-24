<?php

namespace App\Exports;

use App\Models\Dependents;
use App\Models\Employee;
use App\Models\MasterDisease;
use App\Models\HealthPlan;
use App\Models\HealthCoverage;
use App\Models\MasterMedical;
use App\Models\Company;
use App\Models\Location;
use FontLib\Table\Type\fpgm;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class MedicalExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $stat;
    protected $customSearch;

    public function __construct($stat, $customSearch)
    {
        $this->stat = $stat;
        $this->customSearch = $customSearch;
    }

    public function collection()
    {
        $currentYear = date('Y');
        $medicalGroup = [];

        $healthCoverageQuery = HealthCoverage::query();
        if (!empty($this->start_date)) {
            $healthCoverageQuery->whereBetween('created_at', [$this->start_date, $this->end_date]);
        }

        $master_medical = MasterMedical::all();

        $medicalGroup = $healthCoverageQuery->get()->groupBy('employee_id');

        $query = Employee::with(['employee', 'statusReqEmployee', 'statusSettEmployee']);

        if (!empty($this->stat)) {
            $query->where('group_company', $this->stat);
        }
        if (!empty($this->customSearch)) {
            $query->where('fullname', 'like', '%' . $this->customSearch . '%');
        }

        $med_employee = $query->orderBy('created_at', 'desc')->get();

        $medical_plans = HealthPlan::where('period', $currentYear)->get();

        $balances = [];
        foreach ($medical_plans as $plan) {
            $balances[$plan->employee_id][$plan->medical_type] = $plan->balance;
        }

        foreach ($med_employee as $transaction) {
            $transaction->ReqName = $transaction->statusReqEmployee ? $transaction->statusReqEmployee->fullname : '';
            $transaction->settName = $transaction->statusSettEmployee ? $transaction->statusSettEmployee->fullname : '';

            $employeeMedicalPlan = $medical_plans->where('employee_id', $transaction->employee_id)->first();
            $transaction->period = $employeeMedicalPlan ? $employeeMedicalPlan->period : '-';

            if (isset($medicalGroup[$transaction->employee_id])) {
                $transaction->medical_coverage = $medicalGroup[$transaction->employee_id];
            }
        }

        // Buat array untuk menyimpan hasil gabungan dari kedua kode
        $combinedData = [];

        // Ambil semua employee_id dari kode pertama
        foreach ($med_employee as $transaction) {
            // Jalankan kode kedua untuk setiap employee_id
            $medicalGroup = HealthCoverage::where('employee_id', $transaction->employee_id)
                ->where('status', '!=', 'Draft')
                ->orderBy('created_at', 'desc')
                ->get();

            // Loop hasil dari kode kedua dan tambahkan ke hasil gabungan
            foreach ($medicalGroup as $item) {
                if ($item->status == 'Done') {
                    $combinedData[] = [
                        'number' => count($combinedData) + 1,
                        'NIK' => $transaction->kk,
                        'Transaction Date' => \Carbon\Carbon::parse($item->date)->format('d-F-Y'),
                        'Submission Date' => \Carbon\Carbon::parse($item->created_at)->format('d-F-Y'),
                        'Name' => $transaction->fullname,
                        'Patient Name' => $item->patient_name,
                        'Gorup' => $transaction->designation_name,
                        'Disease' => $item->disease,
                        'Status' => $item->status,
                        'Reimburse',
                        'NoRek' => $transaction->bank_name .  ' - ' . $transaction->bank_account_number,
                        'NoInvoice' => $item->no_invoice,
                        'MedicalType' => $item->medical_type,
                        'Balance' => $item->balance,
                        'BalanceUncoverage' => $item->balance_uncoverage,
                        'BalanceVerify' => $item->balance_verif,
                        'PT' => $transaction->company_name,
                        'CostCenter' => $transaction->contribution_level_code,
                        'JobLevel' => $transaction->job_level,
                        'GroupCompany' => $transaction->group_company,
                    ];
                }
            }
        }

        // Return hasil gabungan dari kedua kode
        return collect($combinedData);
    }

    public function headings(): array
    {
        // Base headings
        $headings = [
            'No',
            'NIK',
            'Tanggal Transaksi',
            'Tanggal Pengajuan',
            'Employee Name',
            'Patient Name',
            'Div',
            'Desease',
            'Status',
            'Type',
            'Account Detail',
            'No Invoice',
            'Medical Type',
            'Balance',
            'Balance Uncoverage',
            'Balance Verify',
            'PT',
            'Cost Center',
            'Job Level',
            'Group Company',
        ];

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'argb' => 'FFFFFFFF', // Warna putih
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => '228B22', // Warna kuning
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Center horizontal
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,   // Center vertical
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow(); // Get highest row number
                $highestColumn = $sheet->getHighestColumn(); // Get highest column letter

                // Apply border to the entire data range
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Adjust column widths automatically
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // Get highest column index
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col); // Convert to letter
                    $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                }

                $sheet->getStyle('B1:B' . $highestRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            },
        ];
    }
}
