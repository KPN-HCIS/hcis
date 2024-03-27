<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    function approve() {
        return redirect('goals');
    }
}
