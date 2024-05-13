<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ModelHasRole;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleHasPermission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

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
        $permissions = Permission::all();

        $link = $this->link;
        $active = 'create';

        return view('pages.roles.create', compact('link', 'active', 'permissions'));
    }
    function manage() {
        $roles = Role::all();
        
        $link = $this->link;
        $active = 'manage';

        return view('pages.roles.manage', compact('roles', 'link', 'active'));
    }
    function getAssignment(Request $request) {

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
    function getPermission(Request $request) {

        $roleId = $request->input('roleId');

        $roles = Role::with(['permissions'])->where('id', $roleId)->get();
        // $permissions = Permission::all();

        $permissions = Permission::leftJoin('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('permissions.id', 'permissions.name', 'role_has_permissions.permission_id as role_permission_id')->orderBy('permissions.id')
            ->get();

        $link = $this->link;
        $active = 'create';
        return view('pages.roles.manageform', compact('link', 'active', 'roles', 'permissions', 'roleId'));
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

    public function store(Request $request): RedirectResponse

    {
        $roleName = $request->roleName;
        $guardName = 'web';

        $existingRole = Role::where('name', $roleName)->first();

        if ($existingRole) {
            // Role with the same name already exists, handle accordingly (e.g., show error message)
            return redirect()->back()->with('error', 'Role with the same name already exists.');
        }

        $permissions = [
            'adminMenu' => 9, // 9 = adminmenu
            'goalView' => $request->input('goalView', false), // Use false as default value if not set
            'goalApproval' => $request->input('goalApproval', false),
            'goalSendback' => $request->input('goalSendback', false),
            'reportView' => $request->input('reportView', false),
            'settingView' => $request->input('settingView', false),
            'scheduleView' => $request->input('scheduleView', false),
            'layerView' => $request->input('layerView', false),
            'roleView' => $request->input('roleView', false),
        ];

        // Build permission_id string
        $permission_id = '';

        $role = new Role;
        $role->name = $roleName;
        $role->guard_name = $guardName;
        $role->save();

        // Loop through permissions and create new permission records
        foreach ($permissions as $key) {
            if ($key) {
                // Create a new permission record
                $rolepermission = new RoleHasPermission;
                $rolepermission->role_id = $role->id;
                $rolepermission->permission_id = $key;
                $rolepermission->save();
            }
        }

        return redirect()->route('roles')->with('success', 'Role created successfully!');
    }

    public function update(Request $request): RedirectResponse

    {
        $roleId = $request->roleId;

        $deleteRolePermission = RoleHasPermission::where('role_id', $roleId)->delete();

        if (!$deleteRolePermission) {
            return redirect()->route('roles')->with('error', 'Failed to update permissions!');
        }

        $permissions = [
            'adminMenu' => 9, // 9 = adminmenu
            'goalView' => $request->input('goalView', false), // Use false as default value if not set
            'goalApproval' => $request->input('goalApproval', false),
            'goalSendback' => $request->input('goalSendback', false),
            'reportView' => $request->input('reportView', false),
            'settingView' => $request->input('settingView', false),
            'scheduleView' => $request->input('scheduleView', false),
            'layerView' => $request->input('layerView', false),
            'roleView' => $request->input('roleView', false),
        ];

        // Build permission_id string
        $permission_id = '';

        // Loop through permissions and create new permission records
        foreach ($permissions as $key) {
            if ($key) {
                // Create a new permission record
                $rolepermission = new RoleHasPermission;
                $rolepermission->role_id = $roleId;
                $rolepermission->permission_id = $key;
                $rolepermission->save();
            }
        }

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles')->with('success', 'Role updates successfully!');
    }
}
