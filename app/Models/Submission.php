<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_content',
        'file_path',
        'grade',
        'teacher_comment',
        'status',
        'assignment_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'grade' => 'decimal:2',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }

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
