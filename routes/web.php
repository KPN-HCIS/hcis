<?php

use App\Http\Controllers\Admin\GoalController as AdminGoalController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SendbackController as AdminSendbackController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ExportExcelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LayerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SendbackController;
use App\Http\Controllers\SsoController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MyGoalController;
use App\Http\Controllers\TeamGoalController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;
use App\Http\Middleware\EnsureUserHasRole;

Route::get('/', function () {
    return redirect('home');
});

Route::get('/home', function () {
    return view('pages.home');
})->middleware(['auth', 'verified'])->name('home');

Route::get('dbauth', [SsoController::class, 'dbauth']);

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
                
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                    ->name('password.request');
                    
    Route::get('reset-password-email', [PasswordResetLinkController::class, 'selfResetView']);
    
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                    ->name('password.email');
    
    
});


Route::middleware('auth')->group(function () {

    Route::get('reset-self', [PasswordResetLinkController::class, 'selfReset'])
                ->name('password.reset.self');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'task'])->name('tasks');

    // My Goals
    Route::get('/goals', [MyGoalController::class, 'index'])->name('goals');
    Route::get('/goals/detail/{id}', [MyGoalController::class, 'show'])->name('goals.detail');
    Route::get('/goals/form/{id}', [MyGoalController::class, 'create'])->name('goals.form');
    Route::post('/goals/submit', [MyGoalController::class, 'store'])->name('goals.submit');
    Route::get('/goals/edit/{id}', [MyGoalController::class, 'edit'])->name('goals.edit');
    Route::post('/goals/update', [MyGoalController::class, 'update'])->name('goals.update');

    // Team Goals
    Route::get('/team-goals', [TeamGoalController::class, 'index'])->name('team-goals');
    Route::get('/team-goals/detail/{id}', [TeamGoalController::class, 'show'])->name('team-goals.detail');
    Route::get('/team-goals/form/{id}', [TeamGoalController::class, 'create'])->name('team-goals.form');
    Route::post('/team-goals/submit', [TeamGoalController::class, 'store'])->name('team-goals.submit');
    Route::get('/team-goals/edit/{id}', [TeamGoalController::class, 'edit'])->name('team-goals.edit');
    Route::get('/team-goals/approval/{id}', [TeamGoalController::class, 'approval'])->name('team-goals.approval');
    // Route::post('/goals/update', [TeamGoalController::class, 'update'])->name('goals.update');
    Route::get('/get-tooltip-content', [TeamGoalController::class, 'getTooltipContent']);
    Route::get('/units-of-measurement', [TeamGoalController::class, 'unitOfMeasurement']);
    
    // Approval
    Route::post('/approval/goal', [ApprovalController::class, 'store'])->name('approval.goal');

    // Sendback
    Route::post('/sendback/goal', [SendbackController::class, 'store'])->name('sendback.goal');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');

    Route::get('export/employees', [ExportExcelController::class, 'export'])->name('export.employee');
    Route::get('/get-report-content/{reportType}', [ReportController::class, 'getReportContent']);
    Route::get('/export/report-emp', [ExportExcelController::class, 'exportreportemp'])->name('export.reportemp');

    Route::post('/export', [ExportExcelController::class, 'export'])->name('export');
    // Route::get('/export/goals', [ReportController::class, 'exportGoal'])->name('export.goal');
    Route::post('/get-report-content', [ReportController::class, 'getReportContent']);
    
    Route::get('/changes-group-company', [ReportController::class, 'changesGroupCompany']);
    Route::get('/changes-company', [ReportController::class, 'changesCompany']);
    
    // Authentication
    
    Route::get('verify-email', EmailVerificationPromptController::class)
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
    
});

Route::middleware(['auth', 'role:admin|superadmin'])->group(function () {
	//Employee
    Route::get('/employees', [EmployeeController::class, 'employee'])->name('employees');
    Route::get('/employee/filter', 'EmployeeController@filterEmployees')->name('employee.filter');

    // Schedule
    Route::get('/schedules', [ScheduleController::class, 'schedule'])->name('schedules');
    Route::get('/schedules/form', [ScheduleController::class, 'form'])->name('schedules-form');
    Route::post('/save-schedule', [ScheduleController::class, 'save'])->name('save-schedule');
    Route::get('/edit-schedule/{id}', [ScheduleController::class, 'edit'])->name('edit-schedule');
    Route::post('/update-schedule', [ScheduleController::class, 'update'])->name('update-schedule');
    Route::delete('/schedule/{id}', [ScheduleController::class, 'softDelete'])->name('soft-delete-schedule');

    // Assignments
    Route::get('/assignments', [AssignmentController::class, 'assignment'])->name('assignments');

    // layer
    Route::get('/layer', [LayerController::class, 'layer'])->name('layer');

    // Roles
    Route::get('/admin/roles', [RoleController::class, 'index'])->name('roles');
    Route::get('/admin/roles/assign', [RoleController::class, 'assign'])->name('roles.assign');
    Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::get('/admin/roles/manage', [RoleController::class, 'manage'])->name('roles.manage');
    Route::get('/admin/get-permission', [RoleController::class, 'getPermission'])->name('getPermission');

    // Approval-Admin
    Route::post('/admin/approval/goal', [AdminGoalController::class, 'store'])->name('admin.approval.goal');

    Route::get('/admin/approval/goal/{id}', [AdminGoalController::class, 'create'])->name('admin.create.approval.goal');

    // Goals - Admin
    Route::get('/admin/goals', [AdminGoalController::class, 'index'])->name('admin.goals');

    Route::post('/admin/goal-content', [AdminGoalController::class, 'getGoalContent']);

    // Sendback
    Route::post('/admin/sendback/goal', [AdminSendbackController::class, 'store'])->name('admin.sendback.goal');

    // Reports
    Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('admin.reports');

    Route::get('/admin/get-report-content/{reportType}', [AdminReportController::class, 'getReportContent']);

    Route::post('/admin/get-report-content', [AdminReportController::class, 'getReportContent']);
    
    Route::get('/admin/changes-group-company', [AdminReportController::class, 'changesGroupCompany']);
    Route::get('/admin/changes-company', [AdminReportController::class, 'changesCompany']);

});

Route::fallback(function () {
    return view('errors.404');
});

require __DIR__.'/auth.php';
