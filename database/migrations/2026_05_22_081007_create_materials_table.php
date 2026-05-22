<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id('id'); // Mã tài liệu
            $table->string('title'); // Tiêu đề tài liệu
            $table->string('file_path'); // Đường dẫn file
            $table->foreignId('course_class_id')->constrained('course_classes')->onDelete('cascade'); // Mã lớp học
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
