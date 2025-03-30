<?php

namespace App\Http\Requests;

use App\Helpers\EntitiesFind;
use App\Rules\UniqueArticlePerIssue;
use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
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
        $publicationId = $this->route('publicationId');
        $volumeNumber = $this->route('volumeNumber');
        $issueName = $this->route('issueName');

        $issueId = $this->issue($publicationId, $volumeNumber, $issueName)->id;

        return [
            'title' => [
                'required',
                'string',
                new UniqueArticlePerIssue($issueId)
            ],
            'description' => 'nullable|string',
            'published_date' => 'required|date',
            'link' => 'required|url',
            'doi' => 'required|url',
            'pdf_link' => 'nullable|url',
            'authors' => 'nullable|array',
            'authors.*.name' => 'required|string',
            'authors.*.university' => 'nullable|string',
            'authors.*.profile_link' => 'required|url',
            'authors.*.orcid_link' => 'nullable|url',
            'keywords' => 'nullable|array',
            'keywords.*.name' => 'required|string',
        ];
    }
}
