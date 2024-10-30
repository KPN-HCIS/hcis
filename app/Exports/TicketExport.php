<?php

namespace App\Exports;

use App\Models\Tiket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class TicketExport implements FromCollection, WithHeadings, WithStyles, WithEvents
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

        $ticketData = Tiket::all();
        $combinedData = [];

        foreach ($ticketData as $item) {
            $combinedData[] = [
                'number' => count($combinedData) + 1,
                'User' => 'Nama USer',
                'Atasan' => 'Atasan',
                'Status' => $item->approval_status,
                'Dinas' => $item->jns_dinas_tkt,
                'NoSPPD' => $item->no_sppd,
                'PT' => 'PT',
                'KodeBook' => $item->booking_code,
                'HargaTiket' => $item->tkt_price,
                'From' => $item->dari_tkt,
                'To' => $item->ke_tkt,
                'Name' => $item->np_tkt,
                'NoTLP' => $item->tlp_tkt,
                'TglBrkt1' => $item->tgl_brkt_tkt .  ', ' . $item->jam_brkt_tkt,
                'JenisTKT' => $item->type_tkt,
                'Name2' => $item->type_tkt === 'Round Trip' ? $item->np_tkt : '',
                'NoTLP2' => $item->type_tkt === 'Round Trip' ? $item->tlp_tkt : '',
                'TglBrkt2' => $item->type_tkt === 'Round Trip' ? $item->tgl_plg_tkt . ', ' . $item->jam_plg_tkt : '',
                'ket' => $item->ket_tkt,
            ];
        }

        return collect($combinedData);
    }

    public function headings(): array
    {
        return [
            // Tambahan judul kolom grup
            ['No', 'User', 'Atasan', 'Status', 'Dinas/Cuti', 'No SPPD/Ticket', 'PT', 'Booking Code', 'Ticket Price', 'Destination', '', 'Detail Keberangkatan', '', '', 'Ticket Type', 'Detail Kepulangan', '', 'Note'],
            // Header kolom data
            ['', '', '', '', '', '', '', '', '', 'From', 'To', 'Name', 'Phone Number', 'Departure Date', '', 'Name', 'Phone Number', 'Departure Date', '']
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
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Menggabungkan sel untuk judul grup
                $sheet->mergeCells('J1:K1'); // Destination
                $sheet->mergeCells('L1:N1'); // Detail Keberangkatan
                $sheet->mergeCells('P1:R1'); // Detail Kepulangan

                // Menambahkan border dan gaya seperti biasa
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Mengatur lebar kolom otomatis
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                }
            },
        ];
    }
}
