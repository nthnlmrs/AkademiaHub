<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $fillable = ['course_id', 'name', 'type', 'mode', 'room'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'class_room_user')->withTimestamps();
    }

    public function lecturers()
    {
        return $this->users()->where('role', 'lecturer');
    }

    public function students()
    {
        return $this->users()->where('role', 'student');
    }

    public function teachingAssistants()
    {
        return $this->users()->where('role', 'student')->where('student_type', 'teaching_assistant');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function courseSessions()
    {
        return $this->hasMany(CourseSession::class)->orderBy('session_number');
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class)->whereNull('parent_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function syllabus()
    {
        return $this->hasOne(Syllabus::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->course->name . ' - Class ' . $this->name . ' (' . $this->type . ')';
    }
}
