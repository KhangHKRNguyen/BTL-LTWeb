<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Student\ResultController;
use App\Http\Controllers\Student\FeedbackController;

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
    
    Route::get('/results', [ResultController::class, 'index'])->name('results.index');
    Route::get('/results/{id}', [ResultController::class, 'show'])->name('results.show');
    Route::post('/feedback', [ResultController::class, 'storeFeedback'])->name('feedback.store');
    Route::post('/results/{id}/feedback', [ResultController::class, 'storeFeedback'])->name('results.feedback');

    Route::get('/study', function() { 
        return 'Màn hình Lớp học & Tài liệu (Thành viên khác đang code...)'; 
    })->name('study.index');

    Route::get('/assignments', function() { 
        return 'Màn hình Danh sách bài tập cần làm (Thành viên khác đang code...)'; 
    })->name('assignments.index');

    Route::get('/leave-requests', function() { 
        return 'Màn hình Đơn xin nghỉ học (Thành viên khác đang code...)'; 
    })->name('leave_requests.index');
});


Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    
    Route::get('/attendance', function() { 
        return '⏱Màn hình Điểm danh lớp học (Giáo viên phụ trách)'; 
    })->name('attendance.index');

    Route::get('/materials', function() { 
        return 'Màn hình Quản lý & Upload tài liệu (Giáo viên phụ trách)'; 
    })->name('materials.index');

    Route::get('/assignments', function() { 
        return 'Màn hình Giao bài tập mới (Giáo viên phụ trách)'; 
    })->name('assignments.index');

    Route::get('/grades', function() { 
        return 'Màn hình Danh sách bài nộp & Chấm điểm (Giáo viên phụ trách)'; 
    })->name('grades.index');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/accounts', function() { 
        return 'Màn hình Quản lý tài khoản người dùng (Admin phụ trách)'; 
    })->name('accounts.index');

    Route::get('/classes', function() { 
        return 'Màn hình Quản lý lớp học & Thành viên (Admin phụ trách)'; 
    })->name('classes.index');
});

require __DIR__.'/auth.php';