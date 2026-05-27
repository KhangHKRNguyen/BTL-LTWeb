<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Các vai trò trong hệ thống
    const ROLE_ADMIN   = 'admin';
    const ROLE_TEACHER = 'teacher';
    const ROLE_STUDENT = 'student';

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ===================== HELPER METHODS =====================

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    // ===================== RELATIONSHIPS =====================

    // Người dùng tham gia nhiều lớp học (Many-to-Many)
    public function courseClasses()
    {
        return $this->belongsToMany(CourseClass::class, 'class_user', 'user_id', 'course_class_id')
                    ->withTimestamps();
    }

    // Học viên có nhiều bài nộp
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    // Người dùng gửi nhiều phản hồi
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    // Học viên có nhiều đơn xin nghỉ
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    // Học viên có nhiều bản điểm danh
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
