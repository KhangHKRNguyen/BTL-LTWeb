<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('assignments', 'file_path')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->string('file_path')->nullable()->after('due_time');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('assignments', 'file_path')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->dropColumn('file_path');
            });
        }
    }
};
