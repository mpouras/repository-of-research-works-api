<?php

namespace App\Rules;

use App\Models\Article;
use App\Models\Issue;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueArticlePerIssue implements ValidationRule
{
    protected $issueId;

    public function __construct(int $issueId)
    {
        $this->issueId = $issueId;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $article = Article::where('issue_id', $this->issueId)->where('title', $value)->exists();

        if ($article) {
            $fail("The title has already been taken in this issue.");
        }
    }
}
