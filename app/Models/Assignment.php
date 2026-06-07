<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Assignment extends Model
{
    use HasFactory;

    protected $table = 'assignments';

    protected $fillable = [
        'title',
        'content',
        'type',
        'open_time',
        'due_time',
        'file_path',
        'course_class_id',
        'is_visible',
    ];

    /**
     * Cấu hình casts theo format mới của Laravel 11
     */
    protected function casts(): array
    {
        return [
            'open_time' => 'datetime',
            'due_time' => 'datetime',
            'is_visible' => 'boolean',
        ];
    }

    /**
     * Quan hệ 1-N: Assignment có nhiều Questions
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Quan hệ 1-N: Assignment có nhiều Submissions
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Quan hệ N-1: Assignment thuộc 1 CourseClass
     */
    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    /**
     * Kiểm tra bài tập có phải trắc nghiệm không
     */
    public function isquiz(): bool
    {
        $type = $this->normalizedType();

        return str_contains($type, 'Trắc nghiệm')
            || str_contains($type, 'trac_nghiem')
            || (str_contains($type, 'tr') && str_contains($type, 'nghi'));
    }

    /**
     * Kiểm tra bài tập có phải tự luận không
     */
    public function isEssay(): bool
    {
        $type = $this->normalizedType();

        return str_contains($type, 'Tự luận')
            || str_contains($type, 'tu_luan')
            || str_contains($type, 'luan');
    }

    /**
     * Trả về nhãn hiển thị trực quan của loại bài tập
     */
    public function typeLabel(): string
    {
        return $this->isquiz() ? 'Trắc nghiệm' : 'Tự luận';
    }

    /**
     * Hàm chuẩn hóa chuỗi để phục vụ kiểm tra loại bài tập
     */
    private function normalizedType(): string
    {
        $type = Str::lower(Str::ascii(trim((string) $this->type)));

        return trim((string) preg_replace('/[^a-z0-9]+/', '_', $type), '_');
    }
}