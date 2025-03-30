<?php

namespace App\Http\Requests;

use App\Helpers\EntitiesFind;
use App\Models\Article;
use App\Rules\UniqueArticlePerIssue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    use EntitiesFind;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $articleId = $this->route('id');
        $article = Article::findOrFail($articleId);
        $issueId = $article->id;

        return [
            'title' => [
                'nullable',
                'string',
                Rule::unique('articles')->where('issue_id', $issueId)->ignore($articleId)
            ],
            'description' => 'nullable|string',
            'published_date' => 'nullable|date',
            'link' => 'nullable|url',
            'doi' => 'nullable|url',
            'pdf_link' => 'nullable|url',
            'authors' => 'nullable|array',
            'authors.*.name' => 'required|string',
            'authors.*.university' => 'nullable|string',
            'authors.*.profile_link' => 'nullable|url',
            'authors.*.orcid_link' => 'nullable|url',
            'keywords' => 'nullable|array',
            'keywords.*.name' => 'required|string',
        ];
    }
}
