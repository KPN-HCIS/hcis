<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    function schedule() {
        $link = 'schedule';
        return view('pages.schedules.schedule', [
            'link' => $link,
        ]);
    }
    function form() {
        $link = 'schedule';
        return view('pages.schedules.form', [
            'link' => $link,
        ]);
    }
}
