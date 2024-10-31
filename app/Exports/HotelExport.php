<?php

namespace App\Exports;

use App\Models\Hotel;
use App\Models\HotelApproval;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class HotelExport implements FromCollection, WithHeadings, WithStyles, WithEvents
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
        $query = Employee::with(['employee', 'statusReqEmployee', 'statusSettEmployee', 'manager']);
        $htl_employee = $query->orderBy('created_at', 'desc')->get();

        $hotelData = Hotel::where('approval_status', '!=', 'Draft')
            ->whereBetween('tgl_masuk_htl', [$this->startDate, $this->endDate])
            ->get();

        foreach ($htl_employee as $transaction) {
            $transaction->ReqName = $transaction->statusReqEmployee ? $transaction->statusReqEmployee->fullname : '';
            $transaction->settName = $transaction->statusSettEmployee ? $transaction->statusSettEmployee->fullname : '';
            $transaction->ManagerName = $transaction->manager ? $transaction->manager->fullname : '';
        }

        $combinedData = [];
        $index = 1;

        foreach ($hotelData as $item) {
            $approval = HotelApproval::where('employee_id', $transaction->manager_l1_id)->first();
            $manager = Employee::where('employee_id', $transaction->manager_l1_id)->first();
            // dd($manager->fullname);
            $combinedData[] = [
                'number' => $index,
                'User' => $transaction->fullname,
                'Atasan' => $manager ? $manager->fullname : 'Unknown',
                'Status' => $item->approval_status ?? 'Unknown',
                'NoSPPD' => $item->no_sppd === "-" ? $item->no_htl : $item->no_sppd,
                'PT' => $transaction->company_name . ', ' . $transaction->contribution_level_code,
                'KodeBook' => $item->booking_code,
                'HargaTiket' => $item->booking_price,
                'HotelName' => $item->nama_htl,
                'HotelLocation' => $item->lokasi_htl,
                'RoomCount' => $item->jmlkmr_htl,
                'BedType' => $item->bed_htl,
                'CheckIn' => \Carbon\Carbon::parse($item->tgl_masuk_htl)->format('d-F-Y'),
                'CheckOut' => \Carbon\Carbon::parse($item->tgl_keluar_htl)->format('d-F-Y'),
                'TotalDays' => $item->total_hari,
            ];
            $index++; // Tambahkan satu ke index setiap iterasi loop
        }

        return collect($combinedData);
    }

    public function headings(): array
    {
        return [
            // Tambahan judul kolom grup dengan kolom kosong di baris pertama
            ['No', 'User', 'Atasan', 'Status', 'No SPPD/Hotel', 'PT', 'Booking Code', 'Ticket Price', 'Hotel Detail', '', '', ''],
            // Header kolom data
            ['', '', '', '', '', '', '', '', 'Hotel Name', 'Hotel Location', 'Rooms', 'Bed', 'Check-in Date', 'Check-out Date', 'Total Days']
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

                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:C2');
                $sheet->mergeCells('D1:D2');
                $sheet->mergeCells('E1:E2');
                $sheet->mergeCells('F1:F2');
                $sheet->mergeCells('G1:G2');
                $sheet->mergeCells('H1:H2');
                $sheet->mergeCells('I1:O1');

                $sheet->getStyle('A1:O2')->applyFromArray([
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

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn(); // Get highest column letter
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // Get highest column index
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col); // Convert to letter
                    $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                }

                $prevNoHtl = null;
                $startRow = 3; // Starting from row 3 (after the headers)
                for ($row = 3; $row <= $highestRow; $row++) {
                    $noHtl = $sheet->getCell('E' . $row)->getValue();
                    if ($noHtl === $prevNoHtl) {
                        $sheet->mergeCells('A' . $startRow . ':A' . $row);
                        $sheet->mergeCells('B' . $startRow . ':B' . $row);
                        $sheet->mergeCells('C' . $startRow . ':C' . $row);
                        $sheet->mergeCells('D' . $startRow . ':D' . $row);
                        $sheet->mergeCells('E' . $startRow . ':E' . $row);
                        $sheet->mergeCells('F' . $startRow . ':F' . $row);
                        $sheet->mergeCells('G' . $startRow . ':G' . $row);
                        $sheet->mergeCells('H' . $startRow . ':H' . $row);

                        $sheet->getStyle('A' . $startRow . ':H' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    } else {
                        $startRow = $row;
                    }
                    $prevNoHtl = $noHtl;
                }
            },
        ];
    }
}
