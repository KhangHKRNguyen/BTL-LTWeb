<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    // 1. Khai báo các thuộc tính được phép ghi dữ liệu
    protected $fillable = [
        'submission_content',
        'file_path',
        'grade',
        'teacher_comment',
        'status',
        'assignment_id',
        'user_id'
    ];

    // 2. Ép kiểu dữ liệu
    protected $casts = [
        'grade' => 'float',
    ];

    /**
     * Mối quan hệ: Một bài nộp (Submission) sẽ thuộc về một Bài tập (Assignment)
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    /**
     * Mối quan hệ: Một bài nộp (Submission) phải thuộc về một Người dùng/Học viên (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function feedbacks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Feedback::class, 'submission_id');
    }
}