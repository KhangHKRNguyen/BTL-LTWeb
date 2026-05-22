<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gọi file Seeder chứa bộ dữ liệu 11 bảng chạy ngầm
        $this->call([
            UserAndClassSeeder::class,
        ]);
    }
}
