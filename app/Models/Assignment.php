<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $table = 'assignments';

    protected $fillable = [
        'title',
        'content',
        'type',
        'open_time',
        'due_time',
        'course_class_id'
    ];

    protected $casts = [
        'open_time' => 'datetime',
        'due_time' => 'datetime',
    ];

    /**
     * Quan hệ 1-N: Assignment có nhiều Questions (Câu hỏi)
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Quan hệ 1-N: Assignment có nhiều Submissions (Bài nộp)
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Quan hệ N-1: Assignment thuộc 1 CourseClass (Lớp học)
     */
    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }
}
