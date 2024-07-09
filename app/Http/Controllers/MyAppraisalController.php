<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Models\Appraisal;
use App\Models\ApprovalLayer;
use App\Models\ApprovalRequest;
use App\Models\ApprovalSnapshots;
use App\Models\Employee;
use App\Models\Goal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use stdClass;

class MyAppraisalController extends Controller
{

    protected $category;
    protected $user;

    public function __construct()
    {
        $this->user = Auth()->user()->employee_id;
        $this->category = 'Appraisals';
    }

    function formatDate($date)
    {
        // Parse the date using Carbon
        $carbonDate = Carbon::parse($date);

        // Check if the date is today
        if ($carbonDate->isToday()) {
            return 'Today ' . $carbonDate->format('ga');
        } else {
            return $carbonDate->format('d M ga');
        }
    }

    public function create(Request $request)
    {
        $step = $request->input('step', 1);

        $goal = Goal::where('employee_id', $request->id)->first();

        if ($goal) {
            $goalData = json_decode($goal->form_data, true);
        } else {
            $goalData = [];
        }

        // Read the content of the JSON files
        $formGroupContent = storage_path('../resources/testFormGroup.json');
        $formContent = storage_path('../resources/testForm.json');

        // Decode the JSON content
        $formGroupData = json_decode(File::get($formGroupContent), true);
        $formDatas = json_decode(File::get($formContent), true);


        $formTypes = $formGroupData['data']['form'] ?? [];

        $filteredFormData = array_filter($formDatas, function($form) use ($formTypes) {
            return in_array($form['name'], $formTypes);
        });


        $parentLink = 'Appraisals';
        $link = 'Initiate Appraisal';

        // Pass the data to the view
        return view('pages/appraisals/create', compact('step', 'parentLink', 'link', 'filteredFormData', 'formGroupData', 'goalData', 'goal'));
    }

    // function store(Request $request)
    // {
    //     $submit_status = 'Submitted';
    //     $period = 2024;
    //     // Inisialisasi array untuk menyimpan pesan validasi kustom

    //     $validated = $request->validate([
    //         // 'formData' => 'required|array',
    //         // 'formData.*.kpi' => 'required|string',
    //         // 'formData.*.target' => 'required|numeric',
    //         // 'formData.*.uom' => 'required|string',
    //         // 'formData.*.type' => 'required|string',
    //         // 'formData.*.weightage' => 'required|numeric',
    //         'formData.*.score' => 'required|numeric|between:1,5',
    //     ]);

    //     $scores = [];
    //     foreach ($validated['formData'] as $data) {
    //         $scores[] = $data['score'];
    //     }
    //     $scoresJson = json_encode($scores);

    //     foreach ($validated['formData'] as $index => $data) {
    //         // Fetch the KPI data from the database
    //         $goal = Goal::where('employee_id', $this->user)->first();
    //         if ($goal) {
    //             // Decode the kpi_data JSON
    //             $goalData = json_decode($goal->form_data, true);
                
    //             // Add scores after Category
    //             $goalData['scores'] = $scoresJson;

    //             // Save the updated KPI model
    //         }
    //     }

    //     $model =  new Appraisal;
    //     $model->id = Str::uuid();
    //     $model->employee_id = $request->employee_id;
    //     $model->category = $this->category;
    //     $model->form_data = json_encode($goalData);
    //     $model->form_status = $submit_status;
    //     $model->period = $period;
    //     $model->created_by = Auth()->user()->id;
        
    //     $model->save();


    //     // Beri respon bahwa data berhasil disimpan
    //     // return response()->json(['message' => 'Data saved successfully'], 200);
    //         return redirect('appraisals')->with('success', 'Data submitted successfully.');
    // }

    public function store(Request $request)
    {
        $submit_status = 'Submitted';
        $period = 2024;
        // Validate the request data
        $validatedData = $request->validate([
            'employee_id' => 'required|string|size:11',
            'formGroupName' => 'required|string|min:5|max:100',
            'formData' => 'required|array',
        ]);

        // Extract formGroupName
        $formGroupName = $validatedData['formGroupName'];
        $formData = $validatedData['formData'];

        // Create the array structure
        $datas = [
            'formGroupName' => $formGroupName,
            'formData' => $formData,
        ];

        // Create a new Appraisal instance and save the data
        $appraisal = new Appraisal;
        $appraisal->id = Str::uuid();
        $appraisal->employee_id = $validatedData['employee_id'];
        $appraisal->category = $this->category;
        $appraisal->form_data = json_encode($datas); // Store the form data as JSON
        $appraisal->form_status = $submit_status;
        $appraisal->period = $period;
        $appraisal->created_by = Auth::user()->id;

        $appraisal->save();

        // Return a response, such as a redirect or a JSON response
        return redirect('appraisals')->with('success', 'Data submitted successfully.');
    }

    function index(Request $request) {
        $user = Auth::user()->employee_id;
    
        // Retrieve the selected year from the request
        $filterYear = $request->input('filterYear');
        
        // Retrieve approval requests
        $datasQuery = ApprovalRequest::with([
            'employee', 'goal', 'updatedBy', 'adjustedBy', 'initiated', 'manager', 
            'approval' => function ($query) {
                $query->with('approverName'); // Load nested relationship
            }
        ])
        ->whereHas('approvalLayer', function ($query) use ($user) {
            $query->where('employee_id', $user)->orWhere('approver_id', $user);
        })
        ->where('employee_id', $user);
    
        // Apply additional filtering based on the selected year
        if (!empty($filterYear)) {
            $datasQuery->whereYear('created_at', $filterYear);
        }
    
        $datas = $datasQuery->get();
    
        $formattedData = $datas->map(function($item) {
            // Format created_at
            $createdDate = Carbon::parse($item->created_at);
            if ($createdDate->isToday()) {
                $item->formatted_created_at = 'Today ' . $createdDate->format('g:i A');
            } else {
                $item->formatted_created_at = $createdDate->format('d M Y');
            }
    
            // Format updated_at
            $updatedDate = Carbon::parse($item->updated_at);
            if ($updatedDate->isToday()) {
                $item->formatted_updated_at = 'Today ' . $updatedDate->format('g:i A');
            } else {
                $item->formatted_updated_at = $updatedDate->format('d M Y');
            }
    
            // Determine name and approval layer
            if ($item->sendback_to == $item->employee->employee_id) {
                $item->name = $item->employee->fullname . ' (' . $item->employee->employee_id . ')';
                $item->approvalLayer = '';
            } else {
                $item->name = $item->manager->fullname . ' (' . $item->manager->employee_id . ')';
                $item->approvalLayer = ApprovalLayer::where('employee_id', $item->employee_id)
                                                    ->where('approver_id', $item->current_approval_id)
                                                    ->value('layer');
            }
    
            return $item;
        });
    
        if (!empty($datas->first()->updatedBy)) {
            $adjustByManager = ApprovalLayer::where('approver_id', $datas->first()->updatedBy->employee_id)
                                            ->where('employee_id', $datas->first()->employee_id)
                                            ->first();
        } else {
            $adjustByManager = null;
        }
        
        $data = [];
        
        foreach ($formattedData as $request) {
            // Check form status and creator
            if ($request->goal->form_status != 'Draft' || $request->created_by == Auth::user()->id) {
                // Get fullname from approverName relation
                $dataApprover = '';
                if ($request->approval->first()) {
                    $approverName = $request->approval->first();
                    $dataApprover = $approverName->approverName->fullname;
                }
    
                // Create an object to store request data and approver fullname
                $dataItem = new stdClass();
                $dataItem->request = $request;
                $dataItem->approver_name = $dataApprover;
                $dataItem->name = $request->name;  // Add the name
                $dataItem->approvalLayer = $request->approvalLayer;  // Add the approval layer
    
                // Add the data item to the array
                $data[] = $dataItem;
            }
        }
    
        $formData = [];
        if ($datas->isNotEmpty()) {
            $formData = json_decode($datas->first()->goal->form_data, true);
        }
    
        $path = storage_path('../resources/goal.json');
    
        // Check if the JSON file exists
        if (!File::exists($path)) {
            abort(500, 'JSON file does not exist.');
        }
    
        // Read the contents of the JSON file
        $options = json_decode(File::get($path), true);
    
        $uomOption = $options['UoM'];
        $typeOption = $options['Type'];
    
        $parentLink = 'Appraisals';
        $link = 'My Appraisals';
    
        $employee = Employee::where('employee_id', $user)->first();
        $access_menu = json_decode($employee->access_menu, true);
        $goals = $access_menu['goals'] ?? null;
    
        $selectYear = ApprovalRequest::where('employee_id', $user)->select('created_at')->get();
        $selectYear->transform(function ($req) {
            $req->year = Carbon::parse($req->created_at)->format('Y');
            return $req;
        });
    
        return view('pages.appraisals.my-appraisal', compact('data', 'link', 'parentLink', 'formData', 'uomOption', 'typeOption', 'goals', 'selectYear', 'adjustByManager'));
    }


    function show($id) {
        $data = Goal::find($id);
        
        return view('pages.goals.modal', compact('data')); //modal body hilang ketika modal show bentrok dengan view goal
    }
    

    function edit($id) {

        $goals = Goal::with(['approvalRequest'])->where('id', $id)->get();
        $goal =  $goals->first();

        $approvalRequest = ApprovalRequest::where('form_id', $goal->id)->first();

        $parentLink = 'Goals';
        $link = 'Edit';

        $path = storage_path('../resources/goal.json');

        // Check if the JSON file exists
        if (!File::exists($path)) {
            // Handle the situation where the JSON file doesn't exist
            abort(500, 'JSON file does not exist.');
        }

        if(!$goal){
            return redirect()->route('goals');
        }else{
            // Read the contents of the JSON file
            $formData = json_decode($goal->form_data, true);

            $formCount = count($formData);

            $options = json_decode(File::get($path), true);
            $uomOption = $options['UoM'];
            $typeOption = $options['Type'];

            $selectedUoM = [];
            $selectedType = [];
            $weigthage = [];
            $totalWeightages = 0;
            
            foreach ($formData as $index => $row) {
                $selectedUoM[$index] = $row['uom'] ?? '';
                $selectedType[$index] = $row['type'] ?? '';
                $weigthage[$index] = $row['weightage'] ?? '';
                $totalWeightages += (int)$weigthage[$index];
            }


            $data = json_decode($goal->form_data, true);

            return view('pages.goals.edit', compact('goal', 'formCount', 'link', 'data', 'uomOption', 'selectedUoM', 'typeOption', 'selectedType', 'approvalRequest', 'totalWeightages', 'parentLink'));
        }

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

        $approval = ApprovalRequest::where('form_id', $request->id)->first();
        $approval->status = 'Pending';
        $approval->sendback_messages = null;
        $approval->sendback_to = null;
        // Set other attributes as needed
        $approval->save();

        $snapshot =  ApprovalSnapshots::where('form_id', $request->id)->where('employee_id', $request->employee_id)->first();
        $snapshot->form_data = $jsonData;
        $snapshot->updated_by = Auth::user()->id;
        
        $snapshot->save();

        return redirect('goals');
       

    }

}
