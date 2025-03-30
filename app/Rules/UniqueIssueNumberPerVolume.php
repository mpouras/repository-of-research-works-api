<?php

namespace App\Rules;

use App\Models\Issue;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueIssueNumberPerVolume implements ValidationRule
{
    protected int $volumeId;

    public function __construct(int $volumeId)
    {
        $this->volumeId = $volumeId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = Issue::where('volume_id', $this->volumeId)->where('name', $value)->exists();

        if ($exists) {
            $fail("The {$attribute} must be unique within the selected volume.");
        }
    }
}
