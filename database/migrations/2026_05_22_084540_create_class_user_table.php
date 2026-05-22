<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_user', function (Blueprint $table) {
            $table->id();
            // Khóa ngoại liên kết tới bảng course_classes
            $table->foreignId('course_class_id')->constrained('course_classes')->onDelete('cascade');
            // Khóa ngoại liên kết tới bảng users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_user');
    }
};
