<?php

namespace App\Imports;

use App\Models\HealthCoverage;
use App\Models\HealthPlan;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Exceptions\ImportDataInvalidException;

class ImportHealthCoverage implements ToModel
{
    public function model(array $row)
    {
        $userId = Auth::id();

        // Check if required fields are numeric
        if (!is_numeric($row[1]) || !is_numeric($row[11]) || !is_numeric($row[12]) || !is_numeric($row[13])) {
            throw new ImportDataInvalidException("Invalid data format detected. Import canceled.");
        }

        // Validate and format date
        if (is_numeric($row[7])) {
            $excelDate = intval($row[7]);
            $dateTime = Date::excelToDateTimeObject($excelDate);
            $formattedDate = $dateTime->format('Y-m-d');
        } else {
            $date = \DateTime::createFromFormat('d/m/Y', $row[7]);
            if (!$date) {
                throw new ImportDataInvalidException("Invalid date format detected. Import canceled.");
            }
            $formattedDate = $date->format('Y-m-d');
        }

        $healthCoverage = new HealthCoverage([
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
            'status' => 'Done',
            'submission_type' => 'F',
            'created_by' => $userId,
        ]);
        // dd($healthCoverage);

        // Perform your calculations or validation here
        $this->performCalculations($healthCoverage);

        return $healthCoverage;
    }

    private function performCalculations(HealthCoverage $healthCoverage)
    {
        $healthPlan = HealthPlan::where('employee_id', $healthCoverage->employee_id)
            ->where('medical_type', $healthCoverage->medical_type)
            ->first();

        if ($healthPlan) {
            $healthPlan->balance -= $healthCoverage->balance;
            $healthCoverage->balance_uncoverage = max($healthCoverage->balance_uncoverage, abs($healthPlan->balance));
            $healthPlan->save();
        }

        // Perform your other calculations or validation here
        $this->calculateBalance($healthCoverage);
    }

    private function calculateBalance(HealthCoverage $healthCoverage)
    {
        // Your balance calculation logic here
        // For example:
        // $healthCoverage->balance = $healthCoverage->balance_uncoverage - $healthCoverage->balance_verif;
        // dd($healthCoverage);
        $healthCoverage->save();
    }
}
