<?php

namespace App\Exports;

use App\Models\Dependents;
use App\Models\Employee;
use App\Models\MasterDisease;
use App\Models\HealthPlan;
use App\Models\HealthCoverage;
use App\Models\MasterMedical;
use App\Models\MasterBusinessUnit;
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
    protected $startDate;
    protected $endDate;
    protected $unit;

    public function __construct($stat, $customSearch, $startDate, $endDate, $unit)
    {
        $this->stat = $stat;
        $this->customSearch = $customSearch;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->unit = $unit;
    }

    public function collection()
    {

        $currentYear = date('Y');
        $userRole = auth()->user()->roles->first();
        $roleRestriction = json_decode($userRole->restriction, true);

        $restrictedWorkAreas = $roleRestriction['work_area_code'] ?? [];
        $restrictedGroupCompanies = $roleRestriction['group_company'] ?? [];

        // Query Employee dengan filter work_area_code jika ada restriction
        $employeeQuery = Employee::with(['employee', 'statusReqEmployee', 'statusSettEmployee']);

        if (!empty($restrictedWorkAreas)) {
            $employeeQuery->whereIn('work_area_code', $restrictedWorkAreas);
        }
        if (!empty($restrictedGroupCompanies)) {
            $employeeQuery->whereIn('group_company', $restrictedGroupCompanies);
        }

        // Filter tambahan
        if (!empty($this->stat)) {
            $employeeQuery->where('group_company', $this->stat);
        }
        if (!empty($this->customSearch)) {
            $employeeQuery->where('fullname', 'like', '%' . $this->customSearch . '%');
        }
        if (!empty($this->startDate) && !empty($this->endDate)) {
            $employeeQuery->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        if (!empty($this->unit)) {
            $employeeQuery->where('work_area_code', $this->unit);
        }

        $employees = $employeeQuery->orderBy('created_at', 'desc')->get();

        // Query HealthCoverage berdasarkan Employee ID
        $employeeIds = $employees->pluck('employee_id');
        $healthCoverages = HealthCoverage::whereIn('employee_id', $employeeIds)
            ->where('status', '!=', 'Draft')
            ->orderBy('created_at', 'desc')
            ->get();

        $medical_plans = HealthPlan::where('period', $currentYear)->get();
        $balances = [];
        foreach ($medical_plans as $plan) {
            $balances[$plan->employee_id][$plan->medical_type] = $plan->balance;
        }

        // Gabungkan Data
        $combinedData = [];
        foreach ($employees as $employee) {
            $employeeCoverages = $healthCoverages->where('employee_id', $employee->employee_id);

            foreach ($employeeCoverages as $coverage) {
                if ($coverage->status == 'Done') {
                    $combinedData[] = [
                        'number' => count($combinedData) + 1,
                        'NIK' => $employee->kk,
                        'Transaction Date' => \Carbon\Carbon::parse($coverage->date)->format('d-F-Y'),
                        'Submission Date' => \Carbon\Carbon::parse($coverage->created_at)->format('d-F-Y'),
                        'Name' => $employee->fullname,
                        'Patient Name' => $coverage->patient_name,
                        'Group' => $employee->designation_name,
                        'Disease' => $coverage->disease,
                        'Status' => $coverage->status,
                        'Reimburse' => '',
                        'NoRek' => $employee->bank_name . ' - ' . $employee->bank_account_number,
                        'NoInvoice' => $coverage->no_invoice,
                        'MedicalType' => $coverage->medical_type,
                        'Amount' => $coverage->balance,
                        'AmountUncoverage' => $coverage->balance_uncoverage,
                        'AmountVerify' => $coverage->balance_verif,
                        'PT' => $employee->company_name,
                        'CostCenter' => $employee->contribution_level_code,
                        'JobLevel' => $employee->job_level,
                        'GroupCompany' => $employee->group_company,
                    ];
                }
            }
        }

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
            'Amount',
            'Amount Uncoverage',
            'Amount Verify',
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
