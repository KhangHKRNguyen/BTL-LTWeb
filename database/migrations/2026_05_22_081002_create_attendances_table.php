<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id('id'); // Mã điểm danh
            $table->date('attendance_date'); // Ngày điểm danh
            $table->string('status'); // Tình trạng điểm danh (Có mặt, Vắng, Muộn)
            $table->foreignId('course_class_id')->constrained('course_classes')->onDelete('cascade'); // Mã lớp học
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Mã người dùng (Học viên)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
