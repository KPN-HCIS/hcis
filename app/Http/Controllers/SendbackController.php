<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\ApprovalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SendbackController extends Controller
{
    public function store(Request $request)

    {
        $checkRequest = ApprovalRequest::with(['approval'])->find($request->request_id);

        $firstApproval = $checkRequest->approval()
        ->orderBy('id') // Urutkan berdasarkan ID (secara default akan urutkan dari yang terkecil)
        ->first();

        if ($checkRequest->employee_id == $request->sendto) {
            $approval = Approval::where('request_id', $request->request_id);
            $approval->delete();
            if ($firstApproval) {
                $sendto = $firstApproval->approver_id;
            }else{
                $sendto = $checkRequest->current_approval_id;
            }
        }else{
            $sendto = $request->sendto;
        }


        $model = ApprovalRequest::find($request->request_id);
        $model->current_approval_id = $sendto;
        $model->sendback_messages = $request->messages;
        $model->updated_by = Auth::user()->id;
        
        $model->save();

        // Kirim respons JSON ke JavaScript
        return redirect()->route('goals');        

    }
}
