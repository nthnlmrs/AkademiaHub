<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['class_room_id', 'user_id', 'grade_rubric_id', 'type', 'component', 'score', 'max_score'];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function gradeRubric()
    {
        return $this->belongsTo(GradeRubric::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPercentageAttribute(): float
    {
        if ($this->max_score == 0) return 0;
        return round(($this->score / $this->max_score) * 100, 1);
    }
}
