<?php

namespace App\Exports;

use App\Models\CATransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CashAdvancedExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return CATransaction::select('no_ca', 'user_id', 'contribution_level_code', 'start_date', 'end_date', 'total_ca', 'total_real', 'total_cost', 'approval_status')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Cash Advance No',
            'Employee Name',
            'Company',
            'Start Date',
            'End Date',
            'Total CA',
            'Total Settlement',
            'Balance',
            'Status',
        ];
    }
}
