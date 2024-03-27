<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    function role() {
        $link = 'role';
        return view('pages.role', [
            'link' => $link
        ]);
    }
}
