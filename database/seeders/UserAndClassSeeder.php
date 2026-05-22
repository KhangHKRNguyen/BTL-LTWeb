<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAndClassSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ĐỔ DỮ LIỆU BẢNG: users
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Nguyễn Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'status' => 'active',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'Trần Giang Viên',
                'email' => 'giangvien@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'teacher',
                'status' => 'active',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 3,
                'name' => 'Lê Học Viên A',
                'email' => 'hocviena@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'student',
                'status' => 'active',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 4,
                'name' => 'Phạm Học Viên B',
                'email' => 'hocvienb@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'student',
                'status' => 'active',
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);

        // 2. ĐỔ DỮ LIỆU BẢNG: course_classes
        DB::table('course_classes')->insert([
            [
                'id' => 1,
                'class_name' => 'Lập trình Web nâng cao - Nhóm 01',
                'start_time' => now(),
                'end_time' => now()->addMonths(3),
                'room' => 'Phòng 402-A2',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 2,
                'class_name' => 'Cơ sở dữ liệu phân tán - Nhóm 02',
                'start_time' => now(),
                'end_time' => now()->addMonths(3),
                'room' => 'Phòng 301-B1',
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);

        // 3. ĐỔ DỮ LIỆU BẢNG TRUNG GIAN: class_user (Gán người dùng vào lớp)
        DB::table('class_user')->insert([
            ['course_class_id' => 1, 'user_id' => 2, 'created_at' => now(), 'updated_at' => now()], // Giáo viên dạy lớp 1
            ['course_class_id' => 1, 'user_id' => 3, 'created_at' => now(), 'updated_at' => now()], // Học viên A học lớp 1
            ['course_class_id' => 1, 'user_id' => 4, 'created_at' => now(), 'updated_at' => now()], // Học viên B học lớp 1
            ['course_class_id' => 2, 'user_id' => 2, 'created_at' => now(), 'updated_at' => now()], // Giáo viên dạy lớp 2
            ['course_class_id' => 2, 'user_id' => 3, 'created_at' => now(), 'updated_at' => now()], // Học viên A học lớp 2
        ]);

        // 4. ĐỔ DỮ LIỆU BẢNG: leave_requests (Đơn xin nghỉ học)
        DB::table('leave_requests')->insert([
            [
                'id' => 1,
                'request_date' => now()->format('Y-m-d'),
                'reason' => 'Em bị ốm phải đi khám bệnh, xin phép thầy cho em nghỉ ạ.',
                'course_class_id' => 1,
                'user_id' => 3, // Học viên A
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);

        // 5. ĐỔ DỮ LIỆU BẢNG: attendances (Điểm danh)
        DB::table('attendances')->insert([
            ['id' => 1, 'attendance_date' => now()->subDays(1)->format('Y-m-d'), 'status' => 'Có mặt', 'course_class_id' => 1, 'user_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'attendance_date' => now()->subDays(1)->format('Y-m-d'), 'status' => 'Muộn', 'course_class_id' => 1, 'user_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 6. ĐỔ DỮ LIỆU BẢNG: materials (Tài liệu học tập)
        DB::table('materials')->insert([
            [
                'id' => 1,
                'title' => 'Slide Chương 1: Tổng quan về Laravel Framework',
                'file_path' => 'uploads/materials/slide1.pdf',
                'course_class_id' => 1,
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);

        // 7. ĐỔ DỮ LIỆU BẢNG: assignments (Bài tập)
        DB::table('assignments')->insert([
            [
                'id' => 1,
                'title' => 'Bài trắc nghiệm kiểm tra kiến thức PHP Cơ bản',
                'content' => 'Làm bài trắc nghiệm nhanh gồm 2 câu hỏi sau.',
                'type' => 'Trắc nghiệm',
                'open_time' => now(),
                'due_time' => now()->addDays(7),
                'course_class_id' => 1,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 2,
                'title' => 'Bài tập lớn tự luận: Thiết kế hệ thống LMS',
                'content' => 'Nộp file báo cáo PDF tiến độ dự án tuần này.',
                'type' => 'Tự luận',
                'open_time' => now(),
                'due_time' => now()->addDays(14),
                'course_class_id' => 1,
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);

        // 8. ĐỔ DỮ LIỆU BẢNG: questions (Câu hỏi cho bài tập 1)
        DB::table('questions')->insert([
            [
                'id' => 1,
                'question_text' => 'Laravel được viết bằng ngôn ngữ lập trình nào?',
                'option_a' => 'Java', 'option_b' => 'Python', 'option_c' => 'PHP', 'option_d' => 'Javascript',
                'correct_option' => 'C',
                'type' => 'Single Choice',
                'assignment_id' => 1,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 2,
                'question_text' => 'Lệnh CLI nào dùng để chạy Migration trong Laravel?',
                'option_a' => 'php artisan migrate', 'option_b' => 'php artisan serve', 'option_c' => 'php artisan make:model', 'option_d' => 'composer install',
                'correct_option' => 'A',
                'type' => 'Single Choice',
                'assignment_id' => 1,
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);

        // 9. ĐỔ DỮ LIỆU BẢNG: submissions (Bài nộp của Học viên A cho 2 bài tập)
        DB::table('submissions')->insert([
            [
                'id' => 1,
                'submission_content' => 'Đã hoàn thành bài thi trắc nghiệm hệ thống',
                'file_path' => null,
                'grade' => 10.00,
                'teacher_comment' => 'Làm bài rất tốt, đúng hết các câu hỏi.',
                'status' => 'Đã chấm',
                'assignment_id' => 1,
                'user_id' => 3,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 2,
                'submission_content' => 'Em xin gửi thầy file báo cáo tiến độ chương 1 và chương 2 của nhóm ạ.',
                'file_path' => 'uploads/submissions/btl_nhom1.pdf',
                'grade' => null,
                'teacher_comment' => null,
                'status' => 'Đã nộp',
                'assignment_id' => 2,
                'user_id' => 3,
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);

        // 10. ĐỔ DỮ LIỆU BẢNG: student_answers (Đáp án học viên chọn cho bài trắc nghiệm)
        DB::table('student_answers')->insert([
            ['id' => 1, 'selected_option' => 'C', 'submission_id' => 1, 'question_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'selected_option' => 'A', 'submission_id' => 1, 'question_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 11. ĐỔ DỮ LIỆU BẢNG: feedback (Học viên phản hồi thắc mắc về điểm)
        DB::table('feedback')->insert([
            [
                'id' => 1,
                'feedback_content' => 'Thầy ơi bài trắc nghiệm hệ thống chấm em 10 điểm nhưng sao trong danh sách lớp hiển thị có 9 điểm ạ?',
                'old_grade' => 9.00,
                'new_grade' => 10.00,
                'submission_id' => 1,
                'user_id' => 3,
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);
    }
}