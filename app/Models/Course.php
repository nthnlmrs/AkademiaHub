<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['code', 'name', 'credits', 'description'];

    public function classRooms()
    {
        return $this->hasMany(ClassRoom::class);
    }

    public function courseSessions()
    {
        return $this->hasMany(CourseSession::class)->orderBy('session_number');
    }

    public function rubrics()
    {
        return $this->hasMany(GradeRubric::class);
    }
}
