<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserHasRole implements ValidationRule
{
    protected $role;

    public function __construct(string $role)
    {
        $this->role = $role;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::find($value);
        if (!$user || $user->role !== $this->role) {
            $fail("The selected user for {$attribute} must have the role {$this->role}.");
        }
    }
}
