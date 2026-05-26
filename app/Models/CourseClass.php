<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseClass extends Model
{
    use HasFactory;

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

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_user')->withTimestamps();
    }

    public function teachers(): BelongsToMany
    {
        return $this->users()->where('role', 'teacher');
    }

    public function students(): BelongsToMany
    {
        return $this->users()->where('role', 'student');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
