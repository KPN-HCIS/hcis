<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    
    protected $link;

    function __construct()
    {
        $this->link = 'Role';
    }

    function index() {
        return view('pages.roles.app', [
            'link' => $this->link
        ]);
    }
    function assign() {
        $roles = Role::all();
        
        $link = $this->link;
        $active = 'assign';

        return view('pages.roles.assign', compact('roles', 'link', 'active'));
    }
    function create() {        
        $link = $this->link;
        $active = 'create';

        return view('pages.roles.create', compact('link', 'active'));
    }
    function manage() {
        $roles = Role::all();
        
        $link = $this->link;
        $active = 'manage';

        return view('pages.roles.manage', compact('roles', 'link', 'active'));
    }
    function getPermission(Request $request) {

        $roleId = $request->input('roleId');

        $roles = Role::with(['modelHasRole'])->find($roleId);
        
        $link = $this->link;
        $active = 'manage';
        dd($roles);
        return view('pages.roles.assignform', compact('roles', 'link', 'active'));
    }
}
