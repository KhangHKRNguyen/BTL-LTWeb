<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnswer extends Model
{
    protected $table = 'student_answers';

    protected $fillable = [
        'selected_option',
        'submission_id',
        'question_id'
    ];

    /**
     * Quan hệ N-1: StudentAnswer thuộc 1 Submission (Bài nộp)
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Quan hệ N-1: StudentAnswer thuộc 1 Question (Câu hỏi)
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
// Đại diện cho bảng chứa kết quả bài làm của sinh viên (student_answers hoặc assignment_submissions).
// Chứa các thuộc tính như: id_bai_tap, id_sinh_vien, dap_an_trac_nghiem (chuỗi json hoặc text), duong_dan_file_tu_luan, thoi_gian_nop, diem_so (nếu có).
// Thực hiện nhiệm vụ lưu kết quả làm bài của sinh viên xuống database khi họ bấm "Nộp bài".