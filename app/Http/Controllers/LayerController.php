<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalLayer;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ApprovalLayerImport;
use Illuminate\Support\Facades\Log;
use App\Models\ApprovalLayerBackup;
use Illuminate\Support\Facades\Auth;

class LayerController extends Controller
{
    function layer() {
        $link = 'approval_layers';
        //$approvalLayers = ApprovalLayer::with('view_employee')->get();
        $approvalLayers = DB::table('approval_layers as al')
        ->select('al.employee_id', 'emp.fullname', 'emp.job_level', 'emp.contribution_level_code', 'emp.group_company', 'emp.office_area')
        ->selectRaw("GROUP_CONCAT(al.layer ORDER BY al.layer ASC SEPARATOR '|') AS layers")
        ->selectRaw("GROUP_CONCAT(al.approver_id ORDER BY al.layer ASC SEPARATOR '|') AS approver_ids")
        ->selectRaw("GROUP_CONCAT(emp1.fullname ORDER BY al.layer ASC SEPARATOR '|') AS approver_names")
        ->selectRaw("GROUP_CONCAT(emp1.job_level ORDER BY al.layer ASC SEPARATOR '|') AS approver_job_levels")
        ->leftJoin('employees as emp', 'emp.employee_id', '=', 'al.employee_id')
        ->leftJoin('employees as emp1', 'emp1.employee_id', '=', 'al.approver_id')
        ->groupBy('al.employee_id', 'emp.fullname', 'emp.job_level', 'emp.contribution_level_code', 'emp.group_company', 'emp.office_area')
        ->get();

        $employees = Employee::select('employee_id', 'fullname')
        ->where('job_level', '>', '4A')
        ->orderBy('fullname', 'asc')
        ->get();

    $employeeCount = $approvalLayers->unique('employee_id')->count();
        return view('pages.layers.layer', [
            'link' => $link,
            'approvalLayers' => $approvalLayers,
            'employeeCount' => $employeeCount,
            'employees' => $employees,
        ]);
    }

    function updatelayer(Request $req) {
        $employeeId = $req->input('employee_id');
        $nikApps = $req->input('nik_app');
        $jumlahNikApp = count($nikApps);
        $userId = Auth::id();

        $approvalLayersToDelete = ApprovalLayer::where('employee_id', $employeeId)->get();

        foreach ($approvalLayersToDelete as $layer) {
            ApprovalLayerBackup::create([
                'employee_id' => $layer->employee_id,
                'approver_id' => $layer->approver_id,
                'layer' => $layer->layer,
                'updated_by' => $layer->updated_by,
                'created_at' => $layer->created_at,
                'updated_at' => $layer->updated_at,
            ]);
        }

        ApprovalLayer::where('employee_id', $employeeId)->delete();

        //$cek="";
        $layer=1;
        for ($jml=0; $jml < $jumlahNikApp; $jml++) {
            $approverId = $nikApps[$jml];

            if($approverId<>''){
                ApprovalLayer::create([
                    'employee_id' => $employeeId,
                    'approver_id' => $approverId,
                    'layer' => $layer,
                    'updated_by' => $userId
                ]);    
            }
            //$cek=$cek.''.$approverId.'-'.$layer.'||';
            $layer++;
        }

        Alert::success('Success');
        return redirect()->intended(route('layer', absolute: false));
        //dd($cek);
    }

    public function importLayer(Request $request)
    {
        $request->validate([
            'excelFile' => 'required|mimes:xlsx,xls,csv'
        ]);
        
        // Muat file Excel ke dalam array
        $rows = Excel::toArray([], $request->file('excelFile'));
        $data = $rows[0]; // Ambil sheet pertama
        $employeeIds = [];

        // Mulai dari indeks 1 untuk mengabaikan header
        for ($i = 1; $i < count($data); $i++) {
            $employeeIds[] = $data[$i][0];
        }

        $employeeIds = array_unique($employeeIds);

        // Ambil employee_ids dari data
        //$employeeIds = array_unique(array_column($data, 'employee_id'));

        if (!empty($employeeIds)) {
            // Backup data sebelum menghapus
            $approvalLayersToDelete = ApprovalLayer::whereIn('employee_id', $employeeIds)->get();

            foreach ($approvalLayersToDelete as $layer) {
                ApprovalLayerBackup::create([
                    'employee_id' => $layer->employee_id,
                    'approver_id' => $layer->approver_id,
                    'layer' => $layer->layer,
                    'updated_by' => $layer->updated_by,
                    'created_at' => $layer->created_at,
                    'updated_at' => $layer->updated_at,
                ]);
            }
            //dd($employeeIds);
            // Hapus data lama
            ApprovalLayer::whereIn('employee_id', $employeeIds)->delete();
        }
        $userId = Auth::id();
        //dd($userId);
        // Import data baru
        //Excel::import(new ApprovalLayerImport, $request->file('excelFile'));
        Excel::import(new ApprovalLayerImport($userId), $request->file('excelFile'));

        return back()->with('success', 'Data imported successfully.');
    }
}