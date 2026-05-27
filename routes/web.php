<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Student\ResultController;
use App\Http\Controllers\Student\FeedbackController;
use Illuminate\Support\Facades\Route;

// Trang chủ — điều hướng theo role sau khi đăng nhập
Route::get('/', function () {
    if (auth()->check()) {
        return match(auth()->user()->role) {
            'admin'   => redirect()->route('admin.accounts.index'),
            'teacher' => redirect()->route('teacher.attendance.index'),
            'student' => redirect()->route('student.results.index'),
            default   => redirect('/dashboard'),
        };
    }
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->check()) {
        return match(auth()->user()->role) {
            'admin'   => redirect()->route('admin.accounts.index'),
            'teacher' => redirect()->route('teacher.attendance.index'),
            'student' => redirect()->route('student.results.index'),
            default   => view('dashboard'),
        };
    }
    return redirect()->route('login');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile (dùng chung cho tất cả)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==================== STUDENT ROUTES ====================
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

// ==================== TEACHER ROUTES ====================
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/attendance', function() {
        return 'Màn hình Điểm danh lớp học (Giáo viên phụ trách)';
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

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', function() {
        return view('admin.dashboard');
    })->name('dashboard');

    // Quản lý tài khoản
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::get('/accounts/{user}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
    Route::put('/accounts/{user}', [AccountController::class, 'update'])->name('accounts.update');
    Route::delete('/accounts/{user}', [AccountController::class, 'destroy'])->name('accounts.destroy');
    Route::patch('/accounts/{user}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');

    // Quản lý lớp học
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{class}/edit', [ClassController::class, 'edit'])->name('classes.edit');
    Route::put('/classes/{class}', [ClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');

    // Quản lý thành viên lớp
    Route::get('/classes/{class}/members', [ClassController::class, 'members'])->name('classes.members');
    Route::post('/classes/{class}/assign-teacher', [ClassController::class, 'assignTeacher'])->name('classes.assign-teacher');
    Route::post('/classes/{class}/add-students', [ClassController::class, 'addStudents'])->name('classes.add-students');
    Route::delete('/classes/{class}/remove-student/{user}', [ClassController::class, 'removeStudent'])->name('classes.remove-student');
});

require __DIR__.'/auth.php';
