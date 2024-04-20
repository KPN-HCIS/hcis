<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

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
        return view('pages.schedules.form', [
            'link' => $link,
        ]);
    }
    function save(Request $req) {
        $link = 'schedule';
        //dd($req);
        //$model = schedule::find($req->id);
        $model = new schedule;

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

        //Session::flash("Pesan", "Update berhasil");
        // $schedules = Schedule::all();
        // return view('pages.schedules.schedule', [
        //     'link' => $link,
        //     'schedules' => $schedules,
        // ])->with('success', 'Data berhasil disimpan.');
        // return redirect()->back()->with('success', 'Data berhasil disimpan.');
        return redirect("schedules")->with('success', 'Data berhasil disimpan.');
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
        //dd($req);
        //$model = schedule::find($req->id);
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

        //Session::flash("Pesan", "Update berhasil");
        return redirect("schedules")->with('success', 'Data berhasil diupdate.');
        // $schedules = Schedule::all();
        // return redirect('schedules', [
        //     'link' => $link,
        //     'schedules' => $schedules,
        // ])->with('success', 'Data berhasil diupdate.');
        // return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }
    public function softDelete(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete(); // Memanggil metode delete() untuk soft delete

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
