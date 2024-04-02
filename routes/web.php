<?php

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
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LayerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('home');
});

Route::get('/home', function () {
    return view('pages.home');
})->middleware(['auth', 'verified'])->name('home');

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

    // Goals
    Route::get('/goals', [GoalController::class, 'goal'])->name('goals');
    Route::get('/goals/approval/{id}', [GoalController::class, 'approval'])->name('goals-approval');
    Route::post('/goals/approve', [GoalController::class, 'approve'])->name('goals-approve');
    Route::get('/goals/form', [GoalController::class, 'form'])->name('goals-form');
    Route::post('/goals/form', [GoalController::class, 'saveForm'])->name('goals-submit');

    // Reports
    Route::get('/reports', [ReportController::class, 'report'])->name('reports');

    // Schedule
    Route::get('/schedules', [ScheduleController::class, 'schedule'])->name('schedules');
    Route::get('/schedules/form', [ScheduleController::class, 'form'])->name('schedules-form');

    // Assignments
    Route::get('/assignments', [AssignmentController::class, 'assignment'])->name('assignments');

    // Roles
    Route::get('/roles', [RoleController::class, 'role'])->name('roles');

    // Layers
    Route::get('/layers', [LayerController::class, 'layer'])->name('layers');
    
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


require __DIR__.'/auth.php';
