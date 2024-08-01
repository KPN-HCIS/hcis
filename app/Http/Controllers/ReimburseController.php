<?php

namespace App\Http\Controllers;

use App\Models\ca_transaction;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Location;
use App\Models\Employee;
use App\Models\ListPerdiem;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use App\Models\CATransaction;
use App\Http\Controllers\Log;

class ReimburseController extends Controller
{
    function reimbursements()
    {

        $userId = Auth::id();

        return view('hcis.reimbursements.dash', [
            'userId' => $userId,
        ]);
    }
    public function cashadvanced()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';
        $transactions = CATransaction::with('employee')->get();

        return view('hcis.reimbursements.cashadv.cashadv', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'transactions' => $transactions,
        ]);
    }
    function cashadvancedCreate()
    {

        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = ca_transaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();


        return view('hcis.reimbursements.cashadv.formCashadv', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
        ]);
    }
    public function cashadvancedSubmit(Request $req)
    {
        function getRomanMonth($month)
        {
            $romanMonths = [
                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V',
                6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X',
                11 => 'XI', 12 => 'XII'
            ];
            return $romanMonths[$month];
        }

        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';

        $currentYear = date('Y');
        $currentMonth = date('n');
        $romanMonth = getRomanMonth($currentMonth);

        // Ambil nomor urut terakhir dari tahun berjalan menggunakan Eloquent
        $lastTransaction = CATransaction::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->orderBy('no_ca', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/(\d{3})\/CA-ACC\/' . $romanMonth . '\/\d{4}/', $lastTransaction->no_ca, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoCa = "$newNumber/CA-ACC/$romanMonth/$currentYear";

        $model = new CATransaction;
        $model->id = Str::uuid();
        $model->type_ca         = $req->ca_type;
        $model->no_ca           = $newNoCa;
        $model->no_sppd         = $req->bisnis_numb;
        $model->user_id         = $userId;
        $model->unit            = $req->unit;
        $model->contribution_level_code   = $req->companyFilter;
        $model->destination     = $req->locationFilter;
        $model->others_location = $req->others_location;
        $model->ca_needs        = $req->ca_needs;
        $model->start_date      = $req->start_date;
        $model->end_date        = $req->end_date;
        $model->date_required   = $req->ca_required;
        $model->total_days      = $req->totaldays;
        if ($req->ca_type == 'dns' || $req->ca_type == 'ndns') {
            $detail_ca = [
                'allowance' => $req->allowance,
                'transport' => $req->transport,
                'accommodation' => $req->accommodation,
                'other' => $req->other,
            ];
            $detail_ca_json = json_encode($detail_ca);
            $model->detail_ca = $detail_ca_json;
        } else if ($req->ca_type == 'entr') {
            $detail_ca = [
                'enter_type_1' => $req->enter_type_1,
                'enter_fee_1' => $req->enter_fee_1,
                'nominal_1' => $req->nominal_1,
                'enter_type_2' => $req->enter_type_2,
                'enter_fee_2' => $req->enter_fee_2,
                'nominal_2' => $req->nominal_2,
                'enter_type_3' => $req->enter_type_3,
                'enter_fee_3' => $req->enter_fee_3,
                'nominal_3' => $req->nominal_3,
                'enter_type_4' => $req->enter_type_4,
                'enter_fee_4' => $req->enter_fee_4,
                'nominal_4' => $req->nominal_4,
                'enter_type_5' => $req->enter_type_5,
                'enter_fee_5' => $req->enter_fee_5,
                'nominal_5' => $req->nominal_5,
                'rname_1' => $req->rname_1,
                'rposition_1' => $req->rposition_1,
                'rcompany_1' => $req->rcompany_1,
                'rpurpose_1' => $req->rpurpose_1,
                'rname_2' => $req->rname_2,
                'rposition_2' => $req->rposition_2,
                'rcompany_2' => $req->rcompany_2,
                'rpurpose_2' => $req->rpurpose_2,
                'rname_3' => $req->rname_3,
                'rposition_3' => $req->rposition_3,
                'rcompany_3' => $req->rcompany_3,
                'rpurpose_3' => $req->rpurpose_3,
                'rname_4' => $req->rname_4,
                'rposition_4' => $req->rposition_4,
                'rcompany_4' => $req->rcompany_4,
                'rpurpose_4' => $req->rpurpose_4,
                'rname_5' => $req->rname_5,
                'rposition_5' => $req->rposition_5,
                'rcompany_5' => $req->rcompany_5,
                'rpurpose_5' => $req->rpurpose_5,
            ];
            $detail_ca_json = json_encode($detail_ca);
            $model->detail_ca = $detail_ca_json;
        }
        $model->total_ca        = str_replace('.', '', $req->totalca);
        $model->total_real      = "0";
        $model->total_cost      = str_replace('.', '', $req->totalca);
        $model->approval_status = "Pending";
        $model->created_by          = $userId;
        $model->save();

        Alert::success('Success');
        return redirect()->intended(route('cashadvanced', absolute: false));
    }
    function cashadvancedEdit($key)
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = CATransaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();
        $transactions = CATransaction::find($key);

        return view('hcis.reimbursements.cashadv.editCashadv', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
        ]);
    }
    function cashadvancedUpdate(Request $req, $key)
    {
        $userId = Auth::id();
        $model = ca_transaction::find($key);
        $model->type_ca         = $req->ca_type;
        $model->no_ca           = $req->no_ca;
        $model->no_sppd         = $req->bisnis_numb;
        // $model->user_id         = $req->id;
        $model->unit            = $req->unit;
        $model->contribution_level_code   = $req->companyFilter;
        $model->destination     = $req->locationFilter;
        $model->others_location = $req->others_location;
        $model->ca_needs        = $req->ca_needs;
        $model->start_date      = $req->start_date;
        $model->end_date        = $req->end_date;
        $model->date_required   = $req->ca_required;
        $model->total_days      = $req->totaldays;
        if ($req->ca_type == 'dns' || $req->ca_type == 'ndns') {
            $detail_ca = [
                'allowance' => $req->allowance,
                'transport' => $req->transport,
                'accommodation' => $req->accommodation,
                'other' => $req->other,
            ];
            $detail_ca_json = json_encode($detail_ca);
            $model->detail_ca = $detail_ca_json;
        } else if ($req->ca_type == 'entr') {
            $detail_ca = [
                'enter_type_1' => $req->enter_type_1,
                'enter_fee_1' => $req->enter_fee_1,
                'nominal_1' => $req->nominal_1,
                'enter_type_2' => $req->enter_type_2,
                'enter_fee_2' => $req->enter_fee_2,
                'nominal_2' => $req->nominal_2,
                'enter_type_3' => $req->enter_type_3,
                'enter_fee_3' => $req->enter_fee_3,
                'nominal_3' => $req->nominal_3,
                'enter_type_4' => $req->enter_type_4,
                'enter_fee_4' => $req->enter_fee_4,
                'nominal_4' => $req->nominal_4,
                'enter_type_5' => $req->enter_type_5,
                'enter_fee_5' => $req->enter_fee_5,
                'nominal_5' => $req->nominal_5,
                'rname_1' => $req->rname_1,
                'rposition_1' => $req->rposition_1,
                'rcompany_1' => $req->rcompany_1,
                'rpurpose_1' => $req->rpurpose_1,
                'rname_2' => $req->rname_2,
                'rposition_2' => $req->rposition_2,
                'rcompany_2' => $req->rcompany_2,
                'rpurpose_2' => $req->rpurpose_2,
                'rname_3' => $req->rname_3,
                'rposition_3' => $req->rposition_3,
                'rcompany_3' => $req->rcompany_3,
                'rpurpose_3' => $req->rpurpose_3,
                'rname_4' => $req->rname_4,
                'rposition_4' => $req->rposition_4,
                'rcompany_4' => $req->rcompany_4,
                'rpurpose_4' => $req->rpurpose_4,
                'rname_5' => $req->rname_5,
                'rposition_5' => $req->rposition_5,
                'rcompany_5' => $req->rcompany_5,
                'rpurpose_5' => $req->rpurpose_5,
            ];
            $detail_ca_json = json_encode($detail_ca);
            $model->detail_ca = $detail_ca_json;
        }
        $model->total_ca        = str_replace('.', '', $req->totalca);
        $model->total_real      = "0";
        $model->total_cost      = str_replace('.', '', $req->totalca);
        $model->approval_status = "Pending";
        $model->created_by      = $userId;
        $model->save();

        Alert::success('Success Update');
        return redirect()->intended(route('cashadvanced', absolute: false));
    }
    function cashadvancedDelete($id)
    {
        $model = ca_transaction::find($id);
        $model->delete();
        return redirect()->intended(route('cashadvanced', absolute: false));
    }
}
