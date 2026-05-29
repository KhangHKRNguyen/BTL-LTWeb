<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    protected $table = 'submissions';
    use HasFactory;

    protected $fillable = [
        'submission_content',
        'file_path',
        'grade',
        'teacher_comment',
        'status',
        'assignment_id',
        'user_id'
    ];

    protected $casts = [
        'grade' => 'decimal:2',
    ];

    /**
     * Quan hệ N-1: Submission thuộc 1 Assignment
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Quan hệ N-1: Submission thuộc 1 User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Quan hệ N-1: Submission thuộc 1 User
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Quan hệ 1-N: Submission có nhiều StudentAnswers
     */
    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Quan hệ 1-N: Submission có nhiều StudentAnswers
     */
    public function answers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Quan hệ 1-N: Một bài nộp có thể có nhiều phản hồi khiếu nại/trao đổi điểm số
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'submission_id');
    }

    /**
     * Kiểm tra xem bài nộp đã được chấm điểm chưa
     */
    public function isGraded(): bool
    {
        if ($this->grade !== null) {
            return true;
        }

        return str_contains(mb_strtolower((string) $this->status), 'graded')
            || str_contains(mb_strtolower((string) $this->status), 'chấm')
            || str_contains(mb_strtolower((string) $this->status), 'cham');
    }
}