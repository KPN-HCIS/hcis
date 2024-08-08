<?php

namespace App\Exports;

use App\Models\BusinessTrip;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BusinessTripExport implements FromCollection, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $businessTrips;

    public function __construct($businessTrips)
    {
        $this->businessTrips = $businessTrips;
    }

    public function collection()
    {
        return $this->businessTrips;
    }

    public function map($businessTrip): array
    {
        return [
            $businessTrip->id, // Assuming id is the "No."
            $businessTrip->jns_dinas,
            $businessTrip->nama,
            $businessTrip->divisi,
            $businessTrip->no_sppd,
            $businessTrip->mulai,
            $businessTrip->kembali,
            $businessTrip->tujuan,
            $businessTrip->pt,
            $businessTrip->uang_muka,
            $businessTrip->realisasi,
            $businessTrip->sisa_kurang,
            $businessTrip->tanggal_permintaan_nomor,
            $businessTrip->tanggal_diterima_hrd,
            $businessTrip->tanggal_diproses_hrd,
            $businessTrip->tanggal_penyerahan_ke,
            $businessTrip->hari_berjalan,
        ];
    }

    public function headings(): array
    {
        return [
            'No.',
            'Jenis',
            'Nama',
            'Departemen',
            'No SPPD',
            'Mulai',
            'Kembali',
            'Tujuan',
            'PT',
            'Uang Muka',
            'Realisasi',
            'Sisa/Kurang',
            'Tanggal permintaan nomor',
            'Tanggal diterima HRD',
            'Tanggal diproses HRD',
            'Tanggal penyerahan ke',
            'Hari Berjalan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells and set title
        $sheet->mergeCells('B2:R2');
        $sheet->setCellValue('B2', 'Data Klaim Biaya CA Head Office');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Merge cells for sub headers
        $sheet->mergeCells('B4:D4');
        $sheet->setCellValue('B4', 'Identitas');
        $sheet->mergeCells('E4:H4');
        $sheet->setCellValue('E4', 'Perjalanan Dinas');
        $sheet->mergeCells('I4:K4');
        $sheet->setCellValue('I4', 'Dana SPPD');
        $sheet->mergeCells('L4:P4');
        $sheet->setCellValue('L4', 'Tanggal');
        $sheet->mergeCells('Q4:Q4');
        $sheet->setCellValue('Q4', 'Hari Berjalan');

        // Set sub headers styles
        $sheet->getStyle('B4:Q4')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Set headers
        $sheet->fromArray($this->headings(), null, 'B5');
        $sheet->getStyle('B5:Q5')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'C6E0B4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Apply border and alignment styles to the entire sheet
        $sheet->getStyle('B6:Q' . ($sheet->getHighestRow() + 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'wrapText' => true,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }
}
