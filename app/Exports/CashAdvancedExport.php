<?php

namespace App\Exports;

use App\Models\CATransaction;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class CashAdvancedExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    
    public function collection()
    {
        $query = CATransaction::select(
             
            'ca_transactions.type_ca', 
            'ca_transactions.unit', 
            DB::raw("DATE_FORMAT(ca_transactions.created_at, '%d-%M-%Y') as formatted_created_at"),
            DB::raw("DATE_FORMAT(ca_transactions.date_required, '%d-%M-%Y') as formatted_date_required"),
            DB::raw("DATE_FORMAT(ca_transactions.start_date, '%d-%M-%Y') as formatted_start_date"),
            DB::raw("DATE_FORMAT(ca_transactions.end_date, '%d-%M-%Y') as formatted_end_date"),
            DB::raw("DATE_FORMAT(ca_transactions.declare_estimate, '%d-%M-%Y') as formatted_declare_estimate"),
            'ca_transactions.contribution_level_code', 
            'employees.employee_id', 
            'employees.fullname', 
            'employees.manager_l1_id', 
            'employees.manager_l2_id', 
            'ca_transactions.no_ca', 
            'ca_transactions.no_sppd',
            'ca_transactions.total_ca', 
            'ca_transactions.total_real', 
            'ca_transactions.total_cost', 
            'ca_transactions.approval_status',
            'ca_transactions.approval_sett',
            'ca_transactions.approval_extend',
            DB::raw("DATEDIFF(CURDATE(), ca_transactions.declare_estimate) as days_difference"),
        )
        ->leftJoin('employees', 'ca_transactions.user_id', '=', 'employees.id'); // lakukan left join dengan tabel employee

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('ca_transactions.start_date', [$this->startDate, $this->endDate]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Type_CA',
            'Unit',
            'Submitted Date',
            'Paid Date',
            'Start Date',
            'End Date',
            'Est. Settlement Date',
            'Company',		
            'Employee ID',
            'Employee Name',
            'Dept Head',
            'Div Head',
            'Doc No',
            'Assignment',
            
            'Total CA',
            'Total Settlement',
            'Balance',
            'Request Status',
            'Settlement Status',
            'Extend Status',
            'Days',
        ];
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
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow(); // Dapatkan nomor baris tertinggi
                $highestColumn = $sheet->getHighestColumn(); // Dapatkan kolom tertinggi

                // Terapkan border untuk seluruh area data
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Mengatur lebar kolom otomatis
                foreach (range('A', $highestColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
