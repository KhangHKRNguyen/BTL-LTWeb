<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\LeaveRequestController;
use App\Http\Controllers\Student\StudyController;
use App\Http\Controllers\Student\DoAssignmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes cho Student (Học viên)
Route::middleware('auth')->prefix('student')->name('student.')->group(function () {
    // Dashboard - Trang chủ học viên
   // Tự động nhảy thẳng sang trang danh sách bài tập khi vào đường dẫn /student
Route::redirect('/', '/student/assignments')->name('dashboard');

    // Leave Requests - Báo nghỉ
    Route::prefix('leave-requests')->name('leave_requests.')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'index'])->name('index');
        Route::get('/create', [LeaveRequestController::class, 'create'])->name('create');
        Route::post('/', [LeaveRequestController::class, 'store'])->name('store');
    });

    // Study - Học tập (Tải tài liệu)
    Route::prefix('study')->name('study.')->group(function () {
        Route::get('/', [StudyController::class, 'index'])->name('index');
        Route::get('/download/{id}', [StudyController::class, 'downloadMaterial'])->name('download');
    });

    // Assignments - Làm bài tập
    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [DoAssignmentController::class, 'index'])->name('index');
        Route::get('/{id}', [DoAssignmentController::class, 'show'])->name('show');
        Route::post('/{id}', [DoAssignmentController::class, 'store'])->name('store');
    });
});

require __DIR__.'/auth.php';
