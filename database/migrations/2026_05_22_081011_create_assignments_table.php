<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id('id'); // Mã bài tập
            $table->string('title'); // Tên bài tập
            $table->text('content')->nullable(); // Nội dung bài tập
            $table->string('type'); // Loại bài tập (Trắc nghiệm / Tự luận)
            $table->dateTime('open_time')->nullable(); // Thời gian mở
            $table->dateTime('due_time')->nullable(); // Hạn nộp
            $table->foreignId('course_class_id')->constrained('course_classes')->onDelete('cascade'); // Mã lớp học
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
