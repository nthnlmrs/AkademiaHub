<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
    protected $table = 'syllabi';

    protected $fillable = ['class_room_id', 'content', 'file_path', 'file_name'];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }
}
