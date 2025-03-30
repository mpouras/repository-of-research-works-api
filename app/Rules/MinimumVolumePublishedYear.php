<?php

namespace App\Rules;

use App\Models\Publication;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MinimumVolumePublishedYear implements ValidationRule
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
        $lastVolumeYear = $this->publication->volumes()->exists()
            ? $this->publication->volumes()->orderBy('year_published', 'desc')->first()->year_published
            : $this->publication->year_published;

        if ($value < $lastVolumeYear) {
            $fail(__("The next ':attribute' available is :year.", [
                'attribute' => $attribute,
                'year' => $lastVolumeYear,
            ]));
        }
    }
}
