<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $table = 'leave_requests';

    protected $fillable = [
        'request_date',
        'reason',
        'course_class_id',
        'user_id'
    ];

    protected $casts = [
        'request_date' => 'date',
    ];

    /**
     * Quan hệ N-1: LeaveRequest thuộc 1 User (Học viên)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ N-1: LeaveRequest thuộc 1 CourseClass (Lớp học)
     */
    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }
}
// Đại diện cho bảng chứa thông tin đơn xin nghỉ học (leave_requests).
// Chứa các thuộc tính như: id_sinh_vien, ngay_nghi, ly_do, trang_thai_duyet, ngay_tao.
// Thực hiện nhiệm vụ insert dữ liệu đơn xin nghỉ mới vào database và truy vấn lịch sử xin nghỉ của sinh viên.