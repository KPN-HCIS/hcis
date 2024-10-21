<?php

namespace App\Imports;

use App\Models\HealthCoverage;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportHealthCoverage implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $userId = Auth::id();

        if (!is_numeric($row[1])) {
            // Jika tidak valid, skip row ini
            return null;
        }

        if (is_numeric($row[7])) {
            $excelDate = intval($row[7]);
            $dateTime = Date::excelToDateTimeObject($excelDate);
            $formattedDate = $dateTime->format('Y-m-d');
        } else {
            // Jika text biasa
            $date = \DateTime::createFromFormat('d/m/Y', $row[7]);
            $formattedDate = $date ? $date->format('Y-m-d') : null;
        }

        return new HealthCoverage([
            'usage_id' => Str::uuid(),
            'employee_id' => $row[1],
            'no_medic' => $row[2],
            'no_invoice' => $row[3],
            'hospital_name' => $row[4],
            'patient_name' => $row[5],
            'disease' => $row[6],
            'date' => $formattedDate,
            'coverage_detail' => $row[8],
            'period' => $row[9],
            'medical_type' => $row[10],
            'balance' => $row[11],
            'balance_uncoverage' => $row[12],
            'balance_verif' => $row[13],
            'status' => $row[14],
            'created_by' => $userId,
        ]);
    }
}
