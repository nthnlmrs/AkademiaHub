<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserHasStudentType implements ValidationRule
{
    protected $studentType;

    public function __construct(string $studentType)
    {
        $this->studentType = $studentType;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::find($value);
        if (!$user || $user->role !== 'student' || $user->student_type !== $this->studentType) {
            $fail("The selected user for {$attribute} must be a student of type {$this->studentType}.");
        }
    }
}
