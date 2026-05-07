<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSession extends Model
{
    protected $fillable = ['course_id', 'session_number', 'title', 'description'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function activities()
    {
        return $this->hasMany(SessionActivity::class)->orderBy('order');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'course_session_id');
    }
}
