<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    protected $table = 'submissions';

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
     * Quan hệ N-1: Submission thuộc 1 Assignment (Bài tập)
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Quan hệ N-1: Submission thuộc 1 User (Học viên)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ 1-N: Submission có nhiều StudentAnswers (Đáp án học viên)
     */
    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }
}
