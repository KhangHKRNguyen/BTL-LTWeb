<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
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
            'end_time'   => 'datetime',
        ];
    }

    // ===================== RELATIONSHIPS =====================

    // Lớp có nhiều thành viên (Many-to-Many)
    public function users()
    {
        return $this->belongsToMany(User::class, 'class_user', 'course_class_id', 'user_id')
                    ->withTimestamps();
    }

    // Lớp có 1 giáo viên phụ trách
    public function teacher()
    {
        return $this->belongsToMany(User::class, 'class_user', 'course_class_id', 'user_id')
                    ->where('role', 'teacher')
                    ->limit(1);
    }

    // Lớp có nhiều học viên
    public function students()
    {
        return $this->belongsToMany(User::class, 'class_user', 'course_class_id', 'user_id')
                    ->where('users.role', 'student');
    }

    // Lớp có nhiều bài tập
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    // Lớp có nhiều tài liệu
    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    // Lớp có nhiều điểm danh
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Lớp có nhiều đơn xin nghỉ
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    // ===================== HELPER METHODS =====================

    // Kiểm tra lớp có thể xóa không (chưa có học viên và bài tập)
    public function isDeletable(): bool
    {
        $hasStudents    = $this->students()->exists();
        $hasAssignments = $this->assignments()->exists();
        return !$hasStudents && !$hasAssignments;
    }
}
