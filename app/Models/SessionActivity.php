<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionActivity extends Model
{
    protected $fillable = ['course_session_id', 'type', 'title', 'url', 'file_path', 'order'];

    public function courseSession()
    {
        return $this->belongsTo(CourseSession::class);
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'attendance' => 'wifi',
            'video' => 'video',
            'file' => 'file-text',
            'link' => 'link',
            default => 'circle',
        };
    }
}
