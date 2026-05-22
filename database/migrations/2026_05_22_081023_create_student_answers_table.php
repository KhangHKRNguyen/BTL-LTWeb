<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id('id'); // Mã đáp án
            $table->string('selected_option'); // Đáp án chọn
            $table->foreignId('submission_id')->constrained('submissions')->onDelete('cascade'); // Mã bài nộp
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade'); // Mã câu hỏi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
