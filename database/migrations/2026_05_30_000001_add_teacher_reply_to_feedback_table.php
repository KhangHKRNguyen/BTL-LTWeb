<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->text('teacher_reply')->nullable()->after('new_grade');
            $table->timestamp('teacher_replied_at')->nullable()->after('teacher_reply');
        });
    }

    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropColumn(['teacher_reply', 'teacher_replied_at']);
        });
    }
};
