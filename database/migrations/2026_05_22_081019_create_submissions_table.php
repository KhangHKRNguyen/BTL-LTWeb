<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id('id'); // Mã bài nộp
            $table->text('submission_content')->nullable(); // Nội dung bài làm
            $table->string('file_path')->nullable(); // Đường dẫn file bài làm
            $table->decimal('grade', 4, 2)->nullable(); // Điểm số (Ví dụ: 9.50)
            $table->text('teacher_comment')->nullable(); // Nhận xét của giáo viên
            $table->string('status')->default('submitted'); // Trạng thái (Đã nộp, Đã chấm...)
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade'); // Mã bài tập
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Mã người dùng (Học viên)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
