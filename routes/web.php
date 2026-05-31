<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Teacher\AssignmentController;
use App\Http\Controllers\Teacher\GradeController;
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

    Route::prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/import-template', [AssignmentController::class, 'template'])->name('assignments.template');
        Route::get('/classes/{courseClass}/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments/import-preview', [AssignmentController::class, 'previewImport'])->name('assignments.import-preview');
        Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');

        Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
        Route::get('/assignments/{assignment}/submissions', [GradeController::class, 'submissions'])->name('grades.submissions');
        Route::get('/submissions/{submission}/grade', [GradeController::class, 'edit'])->name('grades.edit');
        Route::patch('/submissions/{submission}/grade', [GradeController::class, 'update'])->name('grades.update');
    });
});

require __DIR__.'/auth.php';
