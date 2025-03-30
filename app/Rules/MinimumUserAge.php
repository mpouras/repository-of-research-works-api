<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MinimumUserAge implements ValidationRule
{
    protected $minimumDate;

    public function __construct(int $years = 15)
    {
        $this->minimumDate = Carbon::now()->subYears($years);
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        try {
            $date = Carbon::parse($value);
        } catch (\Exception $e) {
            $fail("The $attribute must be a valid date.");
            return;
        }

        if ($date->isAfter($this->minimumDate)) {
            $fail("You must be born on or before " . $this->minimumDate->toDateString() . ".");
        }
    }
}
