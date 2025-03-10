<?php

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LeavesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [LeavesController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::put('/leave/approve/{id}', [LeavesController::class, 'approve'])->middleware(['auth', 'verified'])->name('leave.approve');
Route::put('/leave/reject/{id}', [LeavesController::class, 'reject'])->middleware(['auth', 'verified'])->name('leave.reject');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::post('/leaves_request', [HistoryController::class, 'store'])->name('leaves.store');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//admin
Route::middleware(['role:HRD'])->group(function () {
    Route::get('/employee', [UserController::class, 'index'])->name('employee');
    Route::post('/employee_create', [UserController::class, 'store'])->name('employee.store');
    Route::put('/employee_update/{id}', [UserController::class, 'update'])->name('employee.update')->middleware('auth');
});

require __DIR__ . '/auth.php';
