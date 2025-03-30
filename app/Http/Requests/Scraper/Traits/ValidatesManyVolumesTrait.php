<?php

namespace App\Http\Requests\Scraper\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidatesManyVolumesTrait
{
    protected array $validVolumes = [];
    protected array $skippedVolumes = [];

    public function validateResolved(): void
    {
        $rules = $this->rules();

        foreach ($this->all() as $volume) {
            $validator = Validator::make($volume, $rules);

            if ($validator->fails()) {
                $this->skippedVolumes[] = $volume['number'];
            } else {
                $this->validVolumes[] = $volume;
            }
        }
    }

    public function validVolumes(): array
    {
        return $this->validVolumes;
    }

    public function skippedVolumes(): array
    {
        return $this->skippedVolumes;
    }
}
