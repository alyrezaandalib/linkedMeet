<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckUserNotSoftDeleted implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::withTrashed()->where('email', $value)->first();

        if ($user && $user->trashed()) {
            $fail('Your account has been deleted, and creating a new account with this email is not possible.');
        }
    }
}
