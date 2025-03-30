<?php

namespace App\Http\Requests\Scraper\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidatesManyIssuesTrait
{
    protected array $validIssues = [];
    protected array $skippedIssues = [];

    public function validateResolved(): void
    {
        $rules = $this->rules();

        foreach ($this->all() as $issue) {
            $validator = Validator::make($issue, $rules);

            if ($validator->fails()) {
                $this->skippedIssues[] = $issue['name'];
            } else {
                $this->validIssues[] = $issue;
            }
        }
    }

    public function validIssues(): array
    {
        return $this->validIssues;
    }

    public function skippedIssues(): array
    {
        return $this->skippedIssues;
    }
}
