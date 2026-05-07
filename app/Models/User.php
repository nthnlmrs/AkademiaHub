<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_type',
        'nim_nip',
        'ta_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAdminLab(): bool
    {
        return $this->role === 'admin_lab';
    }

    public function isLecturer(): bool
    {
        return $this->role === 'lecturer';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isTeachingAssistant(): bool
    {
        return $this->role === 'student' && $this->student_type === 'teaching_assistant';
    }

    public function isRegularStudent(): bool
    {
        return $this->role === 'student' && $this->student_type === 'regular';
    }

    public function classRooms()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_room_user')->withTimestamps();
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get all schedules for the user (via enrolled classes).
     */
    public function getSchedules()
    {
        $classRoomIds = $this->classRooms()->pluck('class_rooms.id');
        return Schedule::whereIn('class_room_id', $classRoomIds);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Admin',
            'admin_lab' => 'Admin Laboratory',
            'lecturer' => 'Lecturer',
            'student' => $this->student_type === 'teaching_assistant' ? 'Teaching Assistant' : 'Student',
            default => 'Unknown',
        };
    }
}
