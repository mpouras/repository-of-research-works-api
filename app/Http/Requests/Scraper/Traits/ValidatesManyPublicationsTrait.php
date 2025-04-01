<?php

namespace App\Http\Requests\Scraper\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidatesManyPublicationsTrait
{
    protected array $validPublications = [];
    protected array $skippedPublications = [];

    public function validateResolved(): void
    {
        $rules = $this->rules();

        foreach ($this->all() as $publication) {
            $validator = Validator::make($publication, $rules);

            if ($validator->fails()) {
                $this->skippedPublications[] = $publication['title'] ?? $publication['id'];
            } else {
                $this->validPublications[] = $publication;
            }
        }
    }

    public function validPublications(): array
    {
        return $this->validPublications;
    }

    public function skippedPublications(): array
    {
        return $this->skippedPublications;
    }
}
