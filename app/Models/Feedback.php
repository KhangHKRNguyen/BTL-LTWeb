<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    // Chỉ định chính xác tên bảng trong Seeder
    protected $table = 'feedback';

    protected $fillable = [
        'feedback_content',
        'old_grade',
        'new_grade',
        'submission_id',
        'user_id'
    ];

    /**
     * Phản hồi này thuộc về một bài nộp nào
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    /**
     * Phản hồi này do ai viết (Học viên hoặc Giáo viên)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}