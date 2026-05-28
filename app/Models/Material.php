<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    protected $table = 'materials';

    protected $fillable = [
        'title',
        'file_path',
        'course_class_id'
    ];

    /**
     * Quan hệ N-1: Material thuộc 1 CourseClass (Lớp học)
     */
    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }
}
