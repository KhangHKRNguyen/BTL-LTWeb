<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\LeaveRequestController;
use App\Http\Controllers\Student\StudyController;
use App\Http\Controllers\Student\DoAssignmentController;
use App\Http\Controllers\Student\ResultController;
use App\Http\Controllers\Student\FeedbackController;
use App\Http\Controllers\Teacher\AssignmentController;
use App\Http\Controllers\Teacher\GradeController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\MaterialController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ClassController;
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

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    
    Route::redirect('/', '/student/assignments')->name('dashboard');

    // Xem kết quả và Phản hồi
    Route::get('/results', [ResultController::class, 'index'])->name('results.index');
    Route::get('/results/{id}', [ResultController::class, 'show'])->name('results.show');
    Route::post('/feedback', [ResultController::class, 'storeFeedback'])->name('feedback.store');
    Route::post('/results/{id}/feedback', [ResultController::class, 'storeFeedback'])->name('results.feedback');

    // Leave Requests - Đơn xin nghỉ
    Route::prefix('leave-requests')->name('leave_requests.')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'index'])->name('index');
        Route::get('/create', [LeaveRequestController::class, 'create'])->name('create');
        Route::post('/', [LeaveRequestController::class, 'store'])->name('store');
    });
       Route::get(
            '/submissions/{submission}/view',
            [DoAssignmentController::class, 'viewFile']
        )->name('submissions.view');

    // Study - Tài liệu học tập
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





Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    
    // Điểm danh
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    // Quản lý tài liệu
    Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
    Route::post('/materials', [MaterialController::class, 'store'])->name('materials.store');
    Route::get('/materials/{material}/download', [MaterialController::class, 'download'])->name('materials.download');
    Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');

    // Quản lý bài tập (Giao bài)
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/import-template', [AssignmentController::class, 'template'])->name('assignments.template');
    Route::get('/classes/{courseClass}/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::post('/assignments/parse-import', [AssignmentController::class, 'parseImport'])->name('assignments.parse-import');
    Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
    Route::get('/assignments/{assignment}/export', [AssignmentController::class, 'export'])->name('assignments.export');
    Route::get('/assignments/{assignment}/download-attachment', [AssignmentController::class, 'downloadAttachment'])->name('assignments.download-attachment');

    // Quản lý điểm số và Chấm bài
    Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
    Route::get('/assignments/{assignment}/submissions', [GradeController::class, 'submissions'])->name('grades.submissions');
    Route::get('/submissions/{submission}/grade', [GradeController::class, 'edit'])->name('grades.edit');
    Route::patch('/submissions/{submission}/grade', [GradeController::class, 'update'])->name('grades.update');
    Route::post('/feedback/{feedback}/reply', [GradeController::class, 'replyFeedback'])->name('grades.feedback.reply');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard Admin
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

    // Quản lý thành viên trong lớp
    Route::get('/classes/{class}/members', [ClassController::class, 'members'])->name('classes.members');
    Route::post('/classes/{class}/assign-teacher', [ClassController::class, 'assignTeacher'])->name('classes.assign-teacher');
    Route::post('/classes/{class}/add-students', [ClassController::class, 'addStudents'])->name('classes.add-students');
    Route::delete('/classes/{class}/remove-student/{user}', [ClassController::class, 'removeStudent'])->name('classes.remove-student');
});

Route::get('/captcha-image', function () {
    // 1. Sinh chuỗi ký tự ngẫu nhiên
    $charset = 'aeiouDT';
    $length = 7;
    $captchaString = '';
    for ($i = 0; $i < $length; $i++) {
        $captchaString .= $charset[random_int(0, strlen($charset) - 1)];
    }

    // LƯU Ý: Thay vì dùng $_SESSION, ta dùng helper session() của Laravel
    session(['captcha_code' => $captchaString]);

    // 2. Tạo kích thước khung ảnh
    $width = 220;
    $height = 70;
    $image = imagecreatetruecolor($width, $height);

    // 3. Cấu hình màu sắc cơ bản
    $background = imagecolorallocate($image, 245, 245, 245);
    $border = imagecolorallocate($image, 200, 200, 200);
    $colors = [
        imagecolorallocate($image, 35, 65, 120),
        imagecolorallocate($image, 80, 120, 50),
        imagecolorallocate($image, 150, 50, 100),
        imagecolorallocate($image, 90, 40, 120),
    ];

    imagefilledrectangle($image, 0, 0, $width, $height, $background);
    imagerectangle($image, 0, 0, $width - 1, $height - 1, $border);

    // 4. Vẽ các đường thẳng nhiễu chống bot
    for ($i = 0; $i < 50; $i++) {
        $noiseColor = imagecolorallocate($image, random_int(150, 220), random_int(150, 220), random_int(150, 220));
        imageline($image, random_int(0, $width), random_int(0, $height), random_int(0, $width), random_int(0, $height), $noiseColor);
    }

    // 5. Vẽ chữ lên hình (sử dụng font mặc định imagestring để không lo lỗi đường dẫn file ttf trên local)
    $charSpace = (int)($width / $length);
    for ($i = 0; $i < strlen($captchaString); $i++) {
        $char = $captchaString[$i];
        $x = 16 + $i * $charSpace;
        $color = $colors[array_rand($colors)];
        imagestring($image, 5, $x, (int)($height / 4) + random_int(-5, 5), $char, $color);
    }

    // 6. Dùng bộ đệm (Buffer) để bắt xuất dữ liệu ảnh dưới dạng Response chuẩn của Laravel
    ob_start();
    imagepng($image);
    $imageData = ob_get_clean();
    imagedestroy($image);

    return response($imageData)
        ->header('Content-Type', 'image/png')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
});

require __DIR__.'/auth.php';