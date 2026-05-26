<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    // 1. Khai báo các thuộc tính được phép ghi dữ liệu
    protected $fillable = [
        'title',          // Tiêu đề bài tập
        'description',    // Nội dung/yêu cầu bài tập
        'due_date',       // Hạn nộp bài
        'file_path',      // File đính kèm đề bài (nếu có)
        'class_id',       // Mã lớp học (Khóa ngoại)
    ];

    // 2. Tự động ép kiểu ngày tháng để khi dùng ở View chỉ việc định dạng ->format('d/m/Y')
    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * Mối quan hệ: Một Bài tập (Assignment) phải thuộc về một Lớp học (CourseClass)
     */
    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'course_class_id');
    }

    /**
     * Mối quan hệ: Một Bài tập (Assignment) có thể có nhiều Bài nộp (Submission) của nhiều học viên
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'assignment_id');
    }
}