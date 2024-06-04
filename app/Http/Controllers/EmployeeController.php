<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\ApprovalLayer;
use App\Models\Location;
use Illuminate\Support\Facades\Http;

class EmployeeController extends Controller
{
    function employee() {
        $link = 'employee';

        $employees = employee::all();
        $locations = Location::orderBy('area')->get();

        return view('pages.employees.employee', [
            'link' => $link,
            'employees' => $employees,
            'locations' => $locations,
        ]);        
    }
    public function fetchAndStoreEmployees()
    {
        // URL API
        $url = 'https://kpncorporation.darwinbox.com/masterapi/employee';

        // Data untuk request
        $data = [
            "api_key" => "46313f36ab8a8bc5aad64ff1c80c769a07716d9af8f07850f6ad2465a0c991b4d42e84414b5775ba151e7f2833223bfb1e0ecf49b89c7d6d0f6a6d39231666f8",
            "datasetKey" => "11f8ded39d3f22e7c71900d90605c7bf8ef211ac94956b07cc2f8c340d61a4528342feb5afc4b9a9db0b980427007db0dd61db52ae857699d80d9c79a28078cc"
        ];

        // Header
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ZGFyd2luYm94c3R1ZGlvOkRCc3R1ZGlvMTIzNDUh'
        ];

        // Request ke API menggunakan Laravel Http Client
        $response = Http::withHeaders($headers)->post($url, $data);

        // Parse response
        $employees = $response->json('employee_data');
        $number_data =0;

        // Simpan data ke database
        foreach ($employees as $employee) {
            User::updateOrCreate(
                ['employee_id' => $employee['employee_id']],
                [
                    'id' => $employee['user_unique_id'],
                    'employee_id' => $employee['employee_id'],
                    'name' => $employee['full_name'],
                    'email' => $employee['company_email_id']
                ]
            );

            Employee::updateOrCreate(
                ['employee_id' => $employee['employee_id']], // Kondisi untuk update
                [
                    'id' => $employee['user_unique_id'],
                    'employee_id' => $employee['employee_id'],
                    'fullname' => $employee['full_name'],
                    'gender' => $employee['gender'],
                    'email' => $employee['company_email_id'],
                    'group_company' => $employee['group_company'],
                    'designation' => $employee['designation'],
                    'job_level' => $employee['job_level'],
                    'company_name' => $employee['contribution_level'],
                    'contribution_level_code' => $employee['contribution_level_code'],
                    'work_area_code' => $employee['work_area_code'],
                    'office_area' => $employee['office_area'],
                    'manager_l1_id' => $employee['direct_manager_employee_id'],
                    'manager_l2_id' => $employee['l2_manager_employee_id'],
                    'employee_type' => $employee['employee_type'],
                    'unit' => $employee['unit'],
                    'date_of_joining' => $employee['date_of_joining'],
                    'users_id' => $employee['user_unique_id']
                ]
            );

            $approvalLayerExists = ApprovalLayer::where('employee_id', $employee['employee_id'])->exists();

            // If not exists, insert two records
            if (!$approvalLayerExists) {
                ApprovalLayer::create([
                    'employee_id' => $employee['employee_id'],
                    'approver_id' => $employee['direct_manager_employee_id'],
                    'layer' => '1'
                ]);

                ApprovalLayer::create([
                    'employee_id' => $employee['employee_id'],
                    'approver_id' => $employee['l2_manager_employee_id'],
                    'layer' => '2'
                ]);
            }

            $number_data++;
        }

        return response()->json(['message' => $number_data.' Employees data successfully saved']);
    }
}
