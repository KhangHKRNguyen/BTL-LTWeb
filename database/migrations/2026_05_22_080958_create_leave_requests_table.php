<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id('id'); // Mã đơn
            $table->date('request_date'); // Ngày xin nghỉ
            $table->text('reason'); // Lý do vắng
            $table->foreignId('course_class_id')->constrained('course_classes')->onDelete('cascade'); // Mã lớp học
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Mã người dùng (Học viên)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
