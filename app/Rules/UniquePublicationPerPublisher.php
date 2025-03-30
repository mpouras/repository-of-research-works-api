<?php

namespace App\Rules;

use App\Models\Publication;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class UniquePublicationPerPublisher implements ValidationRule
{
    protected array $publishers;

    public function __construct(array $publishers)
    {
        $this->publishers = $publishers;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $title = $value;

        // Check for duplicates of the title with any publisher in the list
        $exists = Publication::where('title', $title)
            ->whereHas('publishers', fn($query) => $query->whereIn('name', $this->publishers))
            ->exists();

        if ($exists) {
            $fail("The title '{$title}' already exists for this publisher.");
        }
    }
}
