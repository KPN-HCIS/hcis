<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    
    protected $link;

    function __construct()
    {
        $this->link = 'Role';
    }

    function index() {

        $link = $this->link;
        $active = '';

        return view('pages.roles.app', compact('link', 'active'));
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

        // $roles = Role::with(['modelHasRole'])->where('id', $roleId)->get();
        $roles = ModelHasRole::with(['role'])->whereHas('role', function ($query) use ($roleId) {
            $query->where('id', $roleId);
        })->get();
        $users = User::select('id', 'name')->get();
        
        $link = $this->link;
        $active = 'manage';
        // dd($roles);
        return view('pages.roles.assignform', compact('roles', 'link', 'active', 'users', 'roleId'));
    }

    public function assignUser(Request $request)
    {
        $roleId = $request->input('role_id');
        $selectedUserIds = $request->input('users_id', []);

        // Retrieve the previously saved user IDs for the given role
        $previouslySavedUserIds = ModelHasRole::where('role_id', $roleId)->pluck('model_id')->toArray();

        // Determine the user IDs that need to be deleted
        $userIdsToDelete = array_diff($previouslySavedUserIds, $selectedUserIds);

        // Perform deletion for the user IDs that need to be removed
        if (!empty($userIdsToDelete)) {
            ModelHasRole::where('role_id', $roleId)
                        ->whereIn('model_id', $userIdsToDelete)
                        ->delete();
        }

        // Now, you can loop through the selected user IDs and save them as needed
        foreach ($selectedUserIds as $userId) {
            // Save the user ID or perform any other action here
            // Check if the user ID is already associated with the role
            if (!in_array($userId, $previouslySavedUserIds)) {
                // If not associated, save the association
                ModelHasRole::create([
                    'role_id' => $roleId,
                    'model_type' => 'App\Models\User',
                    'model_id' => $userId,
                ]);
            }
        }

        // Optionally, you can redirect back to the form or another page after saving
        return redirect()->back()->with('success', 'Users saved successfully!');
    }
}
