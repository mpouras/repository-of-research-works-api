<?php

namespace App\Rules;

use App\Models\Publication;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SequentialVolumeNumber implements ValidationRule
{
    protected Publication $publication;

    public function __construct(Publication $publication)
    {
        $this->publication = $publication;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $lastVolumeNumber = $this->publication->volumes()->exists()
            ? $this->publication->volumes()->orderBy('number', 'desc')->first()->number
            : 0;

        if ($value <= $lastVolumeNumber) {
            $fail(__("The :attribute must be greater than the last volume number (:last).", [
                'attribute' => $attribute,
                'last' => $lastVolumeNumber,
            ]));
        }
    }
}
