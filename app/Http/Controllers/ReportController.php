<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    function report() {
        $link = 'reports';
        return view('pages.report', [
            'link' => $link
        ]);
    }
}
