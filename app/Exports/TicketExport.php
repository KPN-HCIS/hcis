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
        $ticketData = Tiket::where('approval_status', '!=', 'Draft')
            ->whereBetween('tgl_brkt_tkt', [$this->startDate, $this->endDate])
            ->get();

        $combinedData = [];
        $index = 1; // Inisialisasi index dimulai dari 1

        foreach ($ticketData as $item) {
            $combinedData[] = [
                'number' => $index,
                'User' => 'Nama User',
                'Atasan' => 'Atasan',
                'Status' => $item->approval_status ?? 'Unknown',
                'Dinas' => $item->jns_dinas_tkt,
                'NoSPPD' => $item->no_sppd === "-" ? $item->no_tkt : $item->no_sppd,
                'PT' => 'PT',
                'KodeBook' => $item->booking_code,
                'HargaTiket' => $item->tkt_price,
                'From' => $item->dari_tkt,
                'To' => $item->ke_tkt,
                'Name' => $item->np_tkt,
                'NoTLP' => $item->tlp_tkt,
                'TglBrkt1' => $item->tgl_brkt_tkt . ', ' . $item->jam_brkt_tkt,
                'JenisTKT' => $item->type_tkt,
                'Name2' => $item->type_tkt === 'Round Trip' ? $item->np_tkt : '',
                'NoTLP2' => $item->type_tkt === 'Round Trip' ? $item->tlp_tkt : '',
                'TglBrkt2' => $item->type_tkt === 'Round Trip' ? $item->tgl_plg_tkt . ', ' . $item->jam_plg_tkt : '',
                'ket' => $item->ket_tkt,
            ];
            $index++; // Tambahkan satu ke index setiap iterasi loop
        }

        return collect($combinedData);
    }


    public function headings(): array
    {
        return [
            // Tambahan judul kolom grup dengan kolom kosong di baris pertama
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

                // Menggabungkan kolom pada baris pertama dan kedua untuk kolom tanpa grup
                $sheet->mergeCells('A1:A2'); // No
                $sheet->mergeCells('B1:B2'); // User
                $sheet->mergeCells('C1:C2'); // Atasan
                $sheet->mergeCells('D1:D2'); // Status
                $sheet->mergeCells('E1:E2'); // Dinas/Cuti
                $sheet->mergeCells('F1:F2'); // No SPPD/Ticket
                $sheet->mergeCells('G1:G2'); // PT
                $sheet->mergeCells('H1:H2'); // Booking Code
                $sheet->mergeCells('I1:I2'); // Ticket Price
                $sheet->mergeCells('O1:O2'); // Ticket Type
                $sheet->mergeCells('S1:S2'); // Note

                // Menggabungkan kolom dalam grup
                $sheet->mergeCells('J1:K1'); // Destination (From - To)
                $sheet->mergeCells('L1:N1'); // Detail Keberangkatan (Name, Phone Number, Departure Date)
                $sheet->mergeCells('P1:R1'); // Detail Kepulangan (Name, Phone Number, Departure Date)

                // Mengatur styling untuk dua baris heading
                $sheet->getStyle('A1:S2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'], // Warna teks putih
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '228B22'], // Warna latar belakang hijau tua
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Menambahkan border untuk seluruh data
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

                // Merge cells for rows with the same no_tkt value
                $prevNoTkt = null;
                $startRow = 3; // Start from row 3 (after the header rows)
                for ($row = 3; $row <= $highestRow; $row++) {
                    $noTkt = $sheet->getCell('F' . $row)->getValue();
                    if ($noTkt === $prevNoTkt) {
                        $sheet->mergeCells('A' . $startRow . ':A' . $row);
                        $sheet->mergeCells('B' . $startRow . ':B' . $row);
                        $sheet->mergeCells('C' . $startRow . ':C' . $row);
                        $sheet->mergeCells('D' . $startRow . ':D' . $row);
                        $sheet->mergeCells('E' . $startRow . ':E' . $row);
                        $sheet->mergeCells('F' . $startRow . ':F' . $row);
                        $sheet->mergeCells('G' . $startRow . ':G' . $row);
                        $sheet->mergeCells('H' . $startRow . ':H' . $row);
                        $sheet->mergeCells('I' . $startRow . ':I' . $row);

                        $sheet->getStyle('A' . $startRow . ':I' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    } else {
                        $startRow = $row;
                    }
                    $prevNoTkt = $noTkt;
                }
            },
        ];
    }
}
