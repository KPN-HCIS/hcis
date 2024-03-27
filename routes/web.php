<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
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

Route::middleware('auth')->group(function () {
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
    
});

Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

//Auth
Route::get('/reset', 'App\Http\Controllers\AuthController@login')->name('reset');
Route::post('/auth', 'App\Http\Controllers\AuthController@auth')->name('auth');


require __DIR__.'/auth.php';
