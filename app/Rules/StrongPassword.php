<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates password strength with specific Indonesian error messages per criterion.
 * Pass the user's email to also block passwords that match it.
 */
class StrongPassword implements ValidationRule
{
    public function __construct(private readonly ?string $email = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('Password harus berupa teks.');
            return;
        }

        if (strlen($value) < 8) {
            $fail('Password minimal 8 karakter.');
        }

        if (! preg_match('/[A-Z]/', $value)) {
            $fail('Password harus mengandung huruf besar (A-Z).');
        }

        if (! preg_match('/[a-z]/', $value)) {
            $fail('Password harus mengandung huruf kecil (a-z).');
        }

        if (! preg_match('/[0-9]/', $value)) {
            $fail('Password harus mengandung angka (0-9).');
        }

        if (! preg_match('/[@#!%$&*^()_\-+=\[\]{};\':"\\|,.<>\/?`~]/', $value)) {
            $fail('Password harus mengandung simbol seperti @, #, !, atau %.');
        }

        if ($this->email !== null && strtolower($value) === strtolower($this->email)) {
            $fail('Password tidak boleh sama dengan email.');
        }
    }
}
