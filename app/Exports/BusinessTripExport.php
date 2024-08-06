<?php

namespace App\Exports;

use App\Models\BusinessTrip;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BusinessTripExport implements FromCollection, WithMapping, WithHeadings, WithStyles, ShouldAutoSize, WithDrawings
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        return BusinessTrip::where('id', $this->id)->get();
    }

    public function styles(Worksheet $sheet)
    {
        // Set company name
        $sheet->mergeCells('B1:J1');
        $sheet->setCellValue('B1', 'KPNCORP');
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(20);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);

        // Set "Business Trip" title
        $sheet->mergeCells('A3:J3');
        $sheet->setCellValue('A3', 'Business Trip');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set header style
        $sheet->getStyle('A4:J4')->getFont()->setBold(true);
        $sheet->getStyle('A4:J4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                          ->getStartColor()->setARGB('FFFF00');

        // Set borders for all cells
        $sheet->getStyle('A1:J' . ($sheet->getHighestRow()))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Set text wrap and vertical alignment for all cells
        $sheet->getStyle('A1:J' . ($sheet->getHighestRow()))->getAlignment()
              ->setWrapText(true)
              ->setVertical(Alignment::VERTICAL_CENTER);

        return [
            4 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFFF00']]
            ],
        ];
    }

    public function map($businessTrip): array
    {
        return [
            $businessTrip->nama,
            $businessTrip->divisi,
            $businessTrip->no_sppd,
            $businessTrip->mulai,
            $businessTrip->kembali,
            $businessTrip->ca,
            $businessTrip->tiket,
            $businessTrip->hotel,
            $businessTrip->taksi,
            $businessTrip->status,
        ];
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Divisi',
            'No SPPD',
            'Mulai',
            'Kembali',
            'CA',
            'Tiket',
            'Hotel',
            'Taksi',
            'Status',
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('img/logos/logokpn.png'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);

        return $drawing;
    }
}
