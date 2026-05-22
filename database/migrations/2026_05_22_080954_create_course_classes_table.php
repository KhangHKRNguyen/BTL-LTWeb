<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_classes', function (Blueprint $table) {
            $table->id('id'); // Mã lớp học
            $table->string('class_name'); // Tên lớp học
            $table->dateTime('start_time')->nullable(); // Thời gian bắt đầu
            $table->dateTime('end_time')->nullable(); // Thời gian kết thúc
            $table->string('room')->nullable(); // Phòng học
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_classes');
    }
};
