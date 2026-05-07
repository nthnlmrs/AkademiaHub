<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    protected $fillable = ['class_room_id', 'course_session_id', 'user_id', 'parent_id', 'title', 'body'];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function courseSession()
    {
        return $this->belongsTo(CourseSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumPost::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function allReplies()
    {
        return $this->replies()->with('allReplies');
    }
}
