<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    function index() {
        return redirect('home');
    }
    function home() {
        $link = 'home';
        // $data = Employee::orderBy("name")->get();
        // $data = Employee::orderBy('name')->paginate(10);
        // $data = Employee::withTrashed()->orderBy('name')->paginate(10); // mengambil semua data berikut yg di delete
        // $data = Employee::onlyTrashed()->orderBy('name')->paginate(10); // hanya mengambil yg di delete
        return view('pages.home', [
            'link' => $link
        ]);
    }

}
