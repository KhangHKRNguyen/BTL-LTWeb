<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id('id'); // Mã phản hồi
            $table->text('feedback_content'); // Nội dung phản hồi
            $table->decimal('old_grade', 4, 2)->nullable(); // Điểm số cũ
            $table->decimal('new_grade', 4, 2)->nullable(); // Điểm số mới
            $table->foreignId('submission_id')->constrained('submissions')->onDelete('cascade'); // Mã bài nộp
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Mã người dùng (Người gửi)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
