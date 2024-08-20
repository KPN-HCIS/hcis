<?php

namespace App\Exports;

use App\Models\BusinessTrip;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Log;

class BusinessTripExport implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings, WithStyles
{
    use Exportable;

    protected $businessTrips;

    public function __construct($businessTrips)
    {
        $this->businessTrips = $businessTrips;
        Log::info('BusinessTripExport received ' . $this->businessTrips->count() . ' records.');
    }

    public function collection()
    {
        return $this->businessTrips;
    }

    public function map($businessTrip): array
    {
        return [
            $businessTrip->jns_dinas,
            $businessTrip->nama,
            $businessTrip->divisi,
            $businessTrip->no_sppd,
            $businessTrip->mulai,
            $businessTrip->kembali,
            $businessTrip->tujuan,
            $businessTrip->bb_perusahaan,
            // $businessTrip->uang_muka,
            // $businessTrip->realisasi,
            // $businessTrip->sisa_kurang,
            $businessTrip->created_at,
            // $businessTrip->tanggal_diterima_hrd,
            // $businessTrip->tanggal_diproses_hrd,
            // $businessTrip->tanggal_penyerahan_ke,
            // $businessTrip->hari_berjalan,
        ];
    }

    public function headings(): array
    {
        return [
            'Jenis Perjalanan',
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
        // Apply styles to headers at A1:P1
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC', // Light green color
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'], // Black border color
                ],
            ],
        ]);

        // Set column widths
        foreach (range('B', 'Q') as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(20);
        }
    }


}
