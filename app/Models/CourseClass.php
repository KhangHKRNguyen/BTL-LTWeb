<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseClass extends Model
{
    use HasFactory;

    protected $table = 'course_classes';

    protected $fillable = [
        'class_name',
        'start_time',
        'end_time',
        'room',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    /**
     * Quan hệ 1-N: CourseClass có nhiều Materials
     */
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Quan hệ 1-N: CourseClass có nhiều Assignments
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Quan hệ 1-N: CourseClass có nhiều LeaveRequests
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Quan hệ 1-N: CourseClass có nhiều Attendances
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Quan hệ N-N: CourseClass có nhiều Users
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_user', 'course_class_id', 'user_id')->withTimestamps();
    }

    /**
     * Quan hệ N-N: Lọc riêng các thành viên là Giáo viên trong lớp
     */
    public function teachers(): BelongsToMany
    {
        return $this->users()->where('role', 'teacher');
    }

    /**
     * Quan hệ N-N: Lọc riêng các thành viên là Học viên trong lớp
     */
    public function students(): BelongsToMany
    {
        return $this->users()->where('role', 'student');
    }
}