<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'title', 'file_path', 'course_class_id',
    ];

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }
}
