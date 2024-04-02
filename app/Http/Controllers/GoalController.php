<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GoalController extends Controller
{
    function goal() {
        $link = 'goals';
        
        return view('pages.goals.goal', [
            'link' => $link
        ]);
    }
    function approval($id) {
        $link = 'goals';

        return view('pages.goals.approval', [
            'link' => $link,
            'id' => $id,
        ]);
    }
    function form() {
        $link = 'goals';

        return view('pages.goals.form', [
            'link' => $link,
        ]);
    }

    public function saveForm(Request $request)
    {
        if ($request->has('save_draft')) {
            // Tangani logika penyimpanan sebagai draft
            $submit_status = '';
        } 
        $submit_status = '';
        // Inisialisasi array untuk menyimpan pesan validasi kustom
        $customMessages = [];

        $kpis = $request->input('kpi', []);
        $targets = $request->input('target', []);
        $uoms = $request->input('uom', []);
        $weightages = $request->input('weightage', []);
        $types = $request->input('type', []);
        $status = $submit_status;

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
        $validator = Validator::make($request->all(), $rules, $customMessages);

        // Jika validasi gagal
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Inisialisasi array untuk menyimpan data KPI
        
        $kpiData = [];
        // Reset nomor indeks untuk penggunaan berikutnya
        $index = 1;

        // Iterasi melalui input untuk mendapatkan data KPI
        foreach ($kpis as $index => $kpi) {
            // Memastikan ada nilai untuk semua input terkait
            if (isset($targets[$index], $uoms[$index], $weightages[$index], $types[$index])) {
                // Simpan data KPI ke dalam array dengan nomor indeks sebagai kunci
                $kpiData[$index] = [
                    'kpi' => $kpi,
                    'target' => $targets[$index],
                    'uom' => $uoms[$index],
                    'weightage' => $weightages[$index],
                    'type' => $types[$index]
                ];

                $index++;
            }
        }

        // Simpan data KPI ke dalam file JSON
        $jsonData = json_encode($kpiData);

        $model =  new Goal;
        $model->users_id = $request->id;
        $model->form_data = $jsonData;
        $model->form_status = $status;
        
        $model->save();

        // Beri respon bahwa data berhasil disimpan
        // return response()->json(['message' => 'Data saved successfully'], 200);
        return redirect('goals');
    }

    function approve() {
        return redirect('goals');
    }
}
