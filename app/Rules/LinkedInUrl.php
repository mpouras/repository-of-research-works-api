<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LinkedInUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== null) {
            if (!filter_var($value, FILTER_VALIDATE_URL) || !preg_match('/^(https?:\/\/)?(www\.)?(linkedin\.com\/in\/.*)$/', $value)) {
                $fail('The ' . $attribute . ' must be a valid LinkedIn URL (https://www.linkedin.com/in/...).');
            }
        }
    }
}
