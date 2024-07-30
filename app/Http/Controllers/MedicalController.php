<?php

namespace App\Http\Controllers;

use App\Models\DataKeluarga;
use Illuminate\Http\Request;

class MedicalController extends Controller
{
    // function medical() {
    //     return view('pages.medical.medical');
    // }
    public function medical()
    {
        $keluarga = DataKeluarga::orderBy('umur', 'desc')->paginate(5);
        return view('pages.medical.medical', ['keluarga' => $keluarga]);
    }

}
