<?php

namespace App\Imports;

use App\Models\HealthCoverage;
use App\Models\HealthPlan;
use App\Models\MasterMedical;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Exceptions\ImportDataInvalidException;
use App\Mail\MedicalNotification;

class ImportHealthCoverage implements ToModel
{
    public function generateNoMedic()
    {
        $currentYear = date('y');
        // Fetch the last no_medic number
        $lastCoverage = HealthCoverage::withTrashed() // Include soft-deleted records
            ->orderBy('no_medic', 'desc')
            ->first();

        // Determine the next no_medic number
        if ($lastCoverage && substr($lastCoverage->no_medic, 2, 2) == $currentYear) {
            // Extract the last 6 digits (the sequence part) and increment it by 1
            $lastNumber = (int) substr($lastCoverage->no_medic, 4); // Extract the last 6 digits
            $nextNumber = $lastNumber + 1;
        } else {
            // If no records for this year or no records at all, start from 000001
            $nextNumber = 1;
        }

        // Format the next number as a 9-digit number starting with '6'
        $newNoMedic = 'MD' . $currentYear . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return $newNoMedic;
    }

    public function model(array $row)
    {
        if ($row[0] == 'No' && $row[1] == 'Employee ID' && $row[2] == 'No Invoice') {
            return null;
        }

        $userId = Auth::id();
        $employeeId = Employee::where('id', $userId)->first();
        $newNoMedic = $this->generateNoMedic(); // Call the generateNoMedic() function
        $MedicType = MasterMedical::get();
        $isValidData = false;
        $expectedTypes = [];

        foreach ($MedicType as $type) {
            $expectedTypes[] = $type->name;
            if ($type->name == $row[9]) {
                $isValidData = true;
                break;
            }
        }

        if (!$isValidData) {
            $expectedTypesString = implode(", ", $expectedTypes);
            throw new ImportDataInvalidException("Value '{$row[9]}' does not match any expected Type Value ({$expectedTypesString}). Import canceled.");
        }

        if (!is_numeric($row[1])) {
            throw new ImportDataInvalidException("Invalid data format detected in column 1. Import canceled.");
        }
        if (!is_numeric($row[10])) {
            throw new ImportDataInvalidException("Invalid data format detected in column 10. Import canceled.");
        }

        if (is_numeric($row[6])) {
            $excelDate = intval($row[6]);
            $dateTime = Date::excelToDateTimeObject($excelDate);
            $formattedDate = $dateTime->format('Y-m-d');
        } else {
            $date = \DateTime::createFromFormat('d/m/Y', $row[6]);
            if (!$date) {
                throw new ImportDataInvalidException("Invalid date format detected. Import canceled.");
            }
            $formattedDate = $date->format('Y-m-d');
        }

        $healthCoverage = new HealthCoverage([
            'usage_id' => Str::uuid(),
            'employee_id' => $row[1],
            'no_medic' => $newNoMedic,
            'no_invoice' => $row[2],
            'hospital_name' => $row[3],
            'patient_name' => $row[4],
            'disease' => $row[5],
            'date' => $formattedDate,
            'coverage_detail' => $row[7],
            'period' => $row[8],
            'medical_type' => $row[9],
            'balance' => $row[10],
            'balance_uncoverage' => '0',
            'balance_verif' => $row[10],
            'status' => 'Done',
            'submission_type' => 'F',
            'created_by' => $userId,
            'verif_by' => $employeeId->employee_id,
            'approved_by' => $employeeId->employee_id,
            'created_at' => date('Y-m-d H:i:s'),
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
        $MDCNotificationLayer = Employee::where('employee_id', $healthCoverage->employee_id)->pluck('email')->first();
        if ($MDCNotificationLayer) {
            // Kirim email ke pengguna transaksi (employee pada layer terakhir)
            Mail::to($MDCNotificationLayer)->send(new MedicalNotification($healthCoverage));
        }

        $healthCoverage->save();
    }
}
