<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeRubric extends Model
{
    protected $fillable = ['course_id', 'name', 'weight'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
