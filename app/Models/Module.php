<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['course_session_id', 'title', 'file_path', 'file_name', 'file_type', 'file_size', 'uploaded_by'];

    public function courseSession()
    {
        return $this->belongsTo(CourseSession::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getFileIconAttribute(): string
    {
        return match (strtolower($this->file_type)) {
            'pdf' => '📄',
            'ppt', 'pptx' => '📊',
            'doc', 'docx' => '📝',
            'xls', 'xlsx' => '📈',
            'zip', 'rar' => '📦',
            'jpg', 'jpeg', 'png', 'gif' => '🖼️',
            default => '📎',
        };
    }
}
