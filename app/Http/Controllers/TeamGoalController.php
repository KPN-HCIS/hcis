<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Models\ApprovalLayer;
use App\Models\ApprovalRequest;
use App\Models\ApprovalSnapshots;
use App\Models\Employee;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use stdClass;

class TeamGoalController extends Controller
{
    function index() {
        
        $user = Auth::user()->employee_id;
        
        // Mengambil data pengajuan berdasarkan employee_id atau manager_id
        $datas = ApprovalRequest::with(['employee', 'goal', 'approval' => function ($query) {
            $query->with('approverName'); // Load nested relationship
        }])->whereHas('approvalLayer', function ($query) use ($user) {
            $query->where('employee_id', $user)->orWhere('approver_id', $user);
        })->where('employee_id', '!=', Auth::user()->employee_id)->get();
        
        $data = [];
        
        foreach ($datas as $request) {
            // Memeriksa status form dan pembuatnya
            if ($request->goal->form_status != 'Draft' || $request->created_by == Auth::user()->id) {
                // Mengambil nilai fullname dari relasi approverName
                if ($request->approval->first()) {
                    $approverName = $request->approval->first();
                    $dataApprover = $approverName->approverName->fullname;
                }else{
                    $dataApprover = '';
                }
        
                // Buat objek untuk menyimpan data request dan approver fullname
                $dataItem = new stdClass();

                $dataItem->request = $request;
                $dataItem->approver_name = $dataApprover;
              

                // Tambahkan objek $dataItem ke dalam array $data
                $data[] = $dataItem;
                
            }
        }
        
        // dd($data);

        $formData = [];
        if($datas->isNotEmpty()){
            $formData = json_decode($datas->first()->goal->form_data, true);
        }

        $path = storage_path('../resources/goal.json');

        // Check if the JSON file exists
        if (!File::exists($path)) {
            // Handle the situation where the JSON file doesn't exist
            abort(500, 'JSON file does not exist.');
        }

        // Read the contents of the JSON file
        $options = json_decode(File::get($path), true);

        $uomOption = $options['UoM'];
        $typeOption = $options['Type'];

        $link = 'goals';
        
        return view('pages.goals.team-goal', compact('data', 'link', 'formData', 'uomOption', 'typeOption'));
       
    }
    function show($id) {
        $data = Goal::find($id);
        
        return view('pages.goals.modal', compact('data')); //modal body hilang ketika modal show bentrok dengan view goal
    }
    
    function create($id) {

        $goal = Goal::where('employee_id', $id)->get(); 
        
        if ($goal->isNotEmpty()) {
            // User ID doesn't match the condition, show error message
            Alert::error('You have already initiated Goals.')->autoClose(2000);
            return redirect()->back(); // Redirect back with error message
        }

        $layer = ApprovalLayer::where('employee_id', $id)->where('layer', 1)->get();  
        if (!$layer->first()) {
            Alert::error("Goal cannot be created", "There's no direct manager assigned in your position!")->showConfirmButton('OK');
            return redirect()->back();
        }
        // dd($layer);
        $path = storage_path('../resources/goal.json');

        // Check if the JSON file exists
        if (!File::exists($path)) {
            // Handle the situation where the JSON file doesn't exist
            abort(500, 'JSON file does not exist.');
        }

        // Read the contents of the JSON file
        $uomOptions = json_decode(File::get($path), true);

        $uomOption = $uomOptions['UoM'];
        
        $link = 'goals';

        return view('pages.goals.form', compact('layer', 'link', 'uomOption'));

    }

    function edit($id) {

        $goals = Goal::with(['approvalRequest'])->where('id', $id)->get();
        $goal =  $goals->first();

        $link = 'goals';

        $path = storage_path('../resources/goal.json');

        // Check if the JSON file exists
        if (!File::exists($path)) {
            // Handle the situation where the JSON file doesn't exist
            abort(500, 'JSON file does not exist.');
        }

        if(!$goal){
            return redirect()->route('goals');
            // $goal = Goal::where('id', $data->goal->id)->get();  
        }else{
            // Read the contents of the JSON file
            $formData = json_decode($goal->form_data, true);

            $formCount = count($formData);

            $options = json_decode(File::get($path), true);
            $uomOption = $options['UoM'];
            $typeOption = $options['Type'];

            $selectedUoM = [];
            $selectedType = [];
            
            foreach ($formData as $index => $row) {
                $selectedUoM[$index] = $row['uom'] ?? '';
                $selectedType[$index] = $row['type'] ?? '';
            }

            $data = json_decode($goal->form_data, true);

            return view('pages.goals.edit', compact('goal', 'formCount', 'link', 'data', 'uomOption', 'selectedUoM', 'typeOption', 'selectedType'));
        }

    }

    function store(Request $request)
    {
        if ($request->submit_type === 'save_draft') {
            // Tangani logika penyimpanan sebagai draft
            $submit_status = 'Draft';
        } else {
            $submit_status = 'Submitted';
        }
        // Inisialisasi array untuk menyimpan pesan validasi kustom
        $customMessages = [];

        $kpis = $request->input('kpi', []);
        $targets = $request->input('target', []);
        $uoms = $request->input('uom', []);
        $weightages = $request->input('weightage', []);
        $types = $request->input('type', []);
        $status = $submit_status;
        $custom_uoms = $request->input('custom_uom', []);
        
        // Menyiapkan aturan validasi
        $rules = [
            'kpi.*' => 'required|string',
            'target.*' => 'required|string',
            'uom.*' => 'required|string',
            'weightage.*' => 'required|integer|min:5|max:100',
            'type.*' => 'required|string',
        ];

        // Pesan validasi kustom
        $customMessages = [
            'weightage.*.integer' => 'Weightage harus berupa angka.',
            'weightage.*.min' => 'Weightage harus lebih besar atau sama dengan :min %.',
            'weightage.*.max' => 'Weightage harus kurang dari atau sama dengan :max %.',
        ];

        // Membuat Validator instance
        if ($request->submit_type === 'submit_form') {
            $validator = Validator::make($request->all(), $rules, $customMessages);
    
            // Jika validasi gagal
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }

        // Inisialisasi array untuk menyimpan data KPI
        
        $kpiData = [];
        // Reset nomor indeks untuk penggunaan berikutnya
        $index = 1;

        // Iterasi melalui input untuk mendapatkan data KPI
        foreach ($kpis as $index => $kpi) {
            // Memastikan ada nilai untuk semua input terkait
            if ($submit_status=='Draft' || isset($targets[$index], $uoms[$index], $weightages[$index], $types[$index])) {
                // Simpan data KPI ke dalam array dengan nomor indeks sebagai kunci
                if($custom_uoms[$index]){
                    $customuom = $custom_uoms[$index];
                }else{
                    $customuom = null;
                }

                $kpiData[$index] = [
                    'kpi' => $kpi,
                    'target' => $targets[$index],
                    'uom' => $uoms[$index],
                    'weightage' => $weightages[$index],
                    'type' => $types[$index],
                    'custom_uom' => $customuom
                ];

                $index++;
            }
        }

        // Simpan data KPI ke dalam file JSON
        $jsonData = json_encode($kpiData);

        $model =  new Goal;
        $model->id = Str::uuid();
        $model->employee_id = $request->employee_id;
        $model->category = $request->category;
        $model->form_data = $jsonData;
        $model->form_status = $status;
        
        $model->save();

        $snapshot =  new ApprovalSnapshots;
        $snapshot->id = Str::uuid();
        $snapshot->form_id = $model->id;
        $snapshot->form_data = $jsonData;
        $snapshot->employee_id = $request->employee_id;
        $snapshot->created_by = Auth::user()->id;
        
        $snapshot->save();

        $approval = new ApprovalRequest();
        $approval->form_id = $model->id;
        $approval->employee_id = $request->employee_id;
        $approval->current_approval_id = $request->approver_id;
        $approval->created_by = Auth::user()->id;
        // Set other attributes as needed
        $approval->save();

        // Beri respon bahwa data berhasil disimpan
        // return response()->json(['message' => 'Data saved successfully'], 200);
            return redirect('team-goals');
    }

    function update(Request $request) {

        if ($request->submit_type === 'save_draft') {
            // Tangani logika penyimpanan sebagai draft
            $submit_status = 'Draft';
        } else {
            $submit_status = 'Submitted';
        }
        // Inisialisasi array untuk menyimpan pesan validasi kustom
        $customMessages = [];

        $kpis = $request->input('kpi', []);
        $targets = $request->input('target', []);
        $uoms = $request->input('uom', []);
        $weightages = $request->input('weightage', []);
        $types = $request->input('type', []);
        $status = $submit_status;
        $custom_uoms = $request->input('custom_uom', []);

        // Menyiapkan aturan validasi
        $rules = [
            'kpi.*' => 'required|string',
            'target.*' => 'required|string',
            'uom.*' => 'required|string',
            'weightage.*' => 'required|integer|min:5|max:100',
            'type.*' => 'required|string',
        ];

        // Pesan validasi kustom
        $customMessages = [
            'weightage.*.integer' => 'Weightage harus berupa angka.',
            'weightage.*.min' => 'Weightage harus lebih besar atau sama dengan :min %.',
            'weightage.*.max' => 'Weightage harus kurang dari atau sama dengan :max %.',
        ];

        // Membuat Validator instance
        if ($request->submit_type === 'submit_form') {
            $validator = Validator::make($request->all(), $rules, $customMessages);
    
            // Jika validasi gagal
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }

        $kpiData = [];
        // Reset nomor indeks untuk penggunaan berikutnya
        $index = 1;

        // Iterasi melalui input untuk mendapatkan data KPI
        foreach ($kpis as $index => $kpi) {
            // Memastikan ada nilai untuk semua input terkait
            if ($submit_status=='Draft' || isset($targets[$index], $uoms[$index], $weightages[$index], $types[$index])) {
                // Simpan data KPI ke dalam array dengan nomor indeks sebagai kunci
                if($custom_uoms[$index]){
                    $customuom = $custom_uoms[$index];
                }else{
                    $customuom = null;
                }

                $kpiData[$index] = [
                    'kpi' => $kpi,
                    'target' => $targets[$index],
                    'uom' => $uoms[$index],
                    'weightage' => $weightages[$index],
                    'type' => $types[$index],
                    'custom_uom' => $customuom
                ];

                $index++;
            }
        }

        // Simpan data KPI ke dalam file JSON
        $jsonData = json_encode($kpiData);
        
        $goal = Goal::find($request->id);
        $goal->form_data = $jsonData;
        $goal->form_status = $status;
        
        $goal->save();

        $snapshot =  ApprovalSnapshots::where('form_id', $request->id)->where('employee_id', $request->employee_id)->first();
        $snapshot->form_data = $jsonData;
        $snapshot->updated_by = Auth::user()->id;
        
        $snapshot->save();

            return redirect('team-goals');

    }

    public function getTooltipContent()
    {
        $approvalRequest = ApprovalRequest::with(['manager'])->first();
        
        if ($approvalRequest) {
            $name = $approvalRequest->manager->fullname.' ('.$approvalRequest->manager->employee_id.')';
            $approvalLayer = ApprovalLayer::where('employee_id', $approvalRequest->employee_id)->where('approver_id', $approvalRequest->current_approval_id)->value('layer');
            return response()->json(['name' => $name, 'layer' => $approvalLayer]);
        }

    }

    public function unitOfMeasurement()
    {
        $uom = file_get_contents(storage_path('../resources/goal.json'));
        // dd($uom);
        return response()->json(json_decode($uom, true));
    }

    public function sendback(Request $request, ApprovalRequest $approval)
    {
        $sendbackTo = $request->input('sendback_to');

        if ($sendbackTo === 'creator') {
            // Kirim kembali ke pembuat form (creator)
            $creator = $approval->user; // Pembuat form
            $previousApprovers = $creator->creatorApproverLayer->flatMap(function ($layer) {
                return $layer->previousApprovers;
            });
        } elseif ($sendbackTo === 'previous_approver') {
            // Kirim kembali ke atasan sebelumnya
            $previousApprovers = $approval->user->previousApprovers;
        }

        // Lakukan sesuatu dengan daftar previous_approvers, seperti menampilkannya di view
        return view('approval.sendback', compact('previousApprovers'));
    }

}
