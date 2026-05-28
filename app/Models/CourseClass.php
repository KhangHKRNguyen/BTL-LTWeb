<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    protected $fillable = [
        'class_name', 'start_time', 'end_time', 'room',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'class_user', 'course_class_id', 'user_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_user', 'course_class_id', 'user_id')
            ->where('role', 'student');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }
}
