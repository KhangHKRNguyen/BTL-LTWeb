<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'attendance_date', 'status', 'course_class_id', 'user_id',
    ];

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
