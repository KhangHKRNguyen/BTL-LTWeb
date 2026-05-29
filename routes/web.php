<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\LeaveRequestController;
use App\Http\Controllers\Student\StudyController;
use App\Http\Controllers\Student\DoAssignmentController;
use App\Http\Controllers\Student\ResultController;
use App\Http\Controllers\Student\FeedbackController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\MaterialController;

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

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    
    Route::redirect('/', '/student/assignments')->name('dashboard');

    Route::get('/results', [ResultController::class, 'index'])->name('results.index');
    Route::get('/results/{id}', [ResultController::class, 'show'])->name('results.show');
    Route::post('/feedback', [ResultController::class, 'storeFeedback'])->name('feedback.store');
    Route::post('/results/{id}/feedback', [ResultController::class, 'storeFeedback'])->name('results.feedback');

    Route::prefix('leave-requests')->name('leave_requests.')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'index'])->name('index');
        Route::get('/create', [LeaveRequestController::class, 'create'])->name('create');
        Route::post('/', [LeaveRequestController::class, 'store'])->name('store');
    });

    Route::prefix('study')->name('study.')->group(function () {
        Route::get('/', [StudyController::class, 'index'])->name('index');
        Route::get('/download/{id}', [StudyController::class, 'downloadMaterial'])->name('download');
    });

    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [DoAssignmentController::class, 'index'])->name('index');
        Route::get('/{id}', [DoAssignmentController::class, 'show'])->name('show');
        Route::post('/{id}', [DoAssignmentController::class, 'store'])->name('store');
    });
});

Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
    Route::post('/materials', [MaterialController::class, 'store'])->name('materials.store');
    Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');

    Route::get('/assignments', function() { 
        return 'Màn hình Giao bài tập mới (Giáo viên phụ trách)'; 
    })->name('assignments.index');

    Route::get('/grades', function() { 
        return 'Màn hình Danh sách bài nộp và Chấm điểm (Giáo viên phụ trách)'; 
    })->name('grades.index');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/accounts', function() { 
        return 'Màn hình Quản lý tài khoản người dùng (Admin phụ trách)'; 
    })->name('accounts.index');

    Route::get('/classes', function() { 
        return 'Màn hình Quản lý lớp học và Thành viên (Admin phụ trách)'; 
    })->name('classes.index');
});

require __DIR__.'/auth.php';