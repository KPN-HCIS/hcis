<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LayerController extends Controller
{
    function layer() {
        return view('pages.layer');
    }
}
