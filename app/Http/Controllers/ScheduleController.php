<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Location;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class ScheduleController extends Controller
{
    function schedule() {
        $link = 'schedule';
        $schedules = Schedule::all();
        return view('pages.schedules.schedule', [
            'link' => $link,
            'schedules' => $schedules,
        ]);
    }
    function form() {
        $link = 'schedule';
        $locations = Location::orderBy('area')->get();
        $companies = Company::orderBy('contribution_level_code')->get();
        
        return view('pages.schedules.form', [
            'link' => $link,
            'locations' => $locations,
            'companies' => $companies,
        ]);
    }
    function save(Request $req) {
        $link = 'schedule';
        //dd($req);
        //$model = schedule::find($req->id);
        $model = new schedule;

        $model->schedule_name       = $req->schedule_name;
        $model->event_type          = $req->event_type;
        //$model->employee_type       = $req->employee_type;
        $model->employee_type = $req->input('employee_type') ? implode(',', $req->input('employee_type')) : '';
        $model->bisnis_unit = $req->input('bisnis_unit') ? implode(',', $req->input('bisnis_unit')) : '';
        $model->company_filter = $req->input('company_filter') ? implode(',', $req->input('company_filter')) : '';
        $model->location_filter = $req->input('location_filter') ? implode(',', $req->input('location_filter')) : '';
        $model->last_join_date      = $req->last_join_date;
        $model->start_date          = $req->start_date;
        $model->end_date            = $req->end_date;
        $model->checkbox_reminder   = isset($req->checkbox_reminder) ? $req->checkbox_reminder : 0;

        if ($req->checkbox_reminder == 1) {
            $model->messages = $req->messages;
            $model->repeat_days = $req->repeat_days_selected;
        } else {
            $model->messages = null;
            $model->repeat_days = null;
        }

        $model->save();

        $today = now();
        if($model->start_date <= $today){
            $query = Employee::query();

            if ($model->location_filter) {
                $query->whereIn('work_area_code', explode(',', $model->location_filter));
            }

            if ($model->company_filter) {
                $query->whereIn('contribution_level_code', explode(',', $model->company_filter));
            }

            if ($model->bisnis_unit) {
                $query->whereIn('group_company', explode(',', $model->bisnis_unit));
            }

            if ($model->employee_type) {
                $query->whereIn('employee_type', explode(',', $model->employee_type));
            }

            $employeesToUpdate = $query->where('date_of_joining', '<=', $model->last_join_date)->get();
            //dd($employeesToUpdate);

            //dd($query->toSql()); // Menampilkan SQL yang dihasilkan oleh kueri
            //dd($query->getBindings()); // Menampilkan nilai yang diikat ke kueri

            // Mengupdate setiap karyawan yang ditemukan
            foreach ($employeesToUpdate as $employee) {
                // Mendapatkan nilai JSON yang ada dalam access_menu
                $accessMenuJson = json_decode($employee->access_menu, true);

                // Memeriksa apakah access_menu kosong
                if (empty($accessMenuJson) || $accessMenuJson===null) {
                    // Jika kosong, atur access_menu menjadi {{ goals:1 }}
                    $accessMenuJson = ['goals' => 1];
                } else {
                    // Jika tidak kosong, perbarui nilai khusus dalam objek JSON
                    $accessMenuJson['goals'] = 1;
                }

                // Mengonversi kembali objek JSON ke format string
                $updatedAccessMenu = json_encode($accessMenuJson);
                
                // Mengisi access_menu dengan nilai yang telah diperbarui
                $employee->access_menu = $updatedAccessMenu;
                $employee->save();
            }
        }
        
        Alert::success('Success');
        return redirect()->intended(route('schedules', absolute: false));
    }
    function edit($id)
    {
        $link = 'schedule';
        $model = Schedule::find($id);
 
        if(!$model)
            return redirect("schedules");

            return view('pages.schedules.edit', [
                'link' => $link,
                'model' => $model,
            ]);
    }
    function update(Request $req) {
        $link = 'schedule';
        $model = Schedule::find($req->id_schedule);

        $model->schedule_name       = $req->schedule_name;
        $model->employee_type       = $req->employee_type;
        $model->start_date          = $req->start_date;
        $model->end_date            = $req->end_date;
        $model->checkbox_reminder   = isset($req->checkbox_reminder) ? $req->checkbox_reminder : 0;

        if ($req->checkbox_reminder == 1) {
            $model->messages = $req->messages;
            $model->repeat_days = $req->repeat_days_selected;
        } else {
            $model->messages = null;
            $model->repeat_days = null;
        }

        $model->save();

        return redirect("schedules")->with('success', 'Data berhasil diupdate.');
    }
    public function softDelete(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete(); // Memanggil metode delete() untuk soft delete

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
