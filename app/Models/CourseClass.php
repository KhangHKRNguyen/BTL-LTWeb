<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseClass extends Model
{
    protected $table = 'course_classes';

    protected $fillable = [
        'class_name',
        'start_time',
        'end_time',
        'room'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Quan hệ 1-N: CourseClass có nhiều Materials (Tài liệu)
     */
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Quan hệ 1-N: CourseClass có nhiều Assignments (Bài tập)
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Quan hệ 1-N: CourseClass có nhiều LeaveRequests (Đơn xin nghỉ)
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Quan hệ 1-N: CourseClass có nhiều Attendances (Điểm danh - Code của Linh)
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Quan hệ N-N: CourseClass có nhiều Users (học viên, giáo viên)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_user', 'course_class_id', 'user_id');
    }

    /**
     * Quan hệ N-N: Lọc riêng các thành viên là Học viên trong lớp (Code của Linh)
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_user', 'course_class_id', 'user_id')
            ->where('role', 'student');
    }
}