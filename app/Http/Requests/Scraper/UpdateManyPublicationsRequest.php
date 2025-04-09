<?php

namespace App\Http\Requests\Scraper;

use App\Http\Requests\Scraper\Traits\ValidatesManyPublicationsTrait;
use App\Rules\MinimumVolumePublishedYear;
use App\Rules\SequentialVolumeNumber;
use App\Rules\UniquePublicationPerPublisher;
use Illuminate\Foundation\Http\FormRequest;

class UpdateManyPublicationsRequest extends FormRequest
{
    use ValidatesManyPublicationsTrait;
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
        $publishers = collect($this->input())->pluck('publishers')->flatten(1)->pluck('name')->toArray();

        return [
            'id' => 'required|exists:publications,id',
            'publishers' => 'nullable|array',
            'publishers.*.name' => 'required|string',
            'publishers.*.scraper' => 'nullable|in:mdpi,acm,ieee,springer',
            'type' => 'nullable|in:Journal,Magazine,Book',
            'title' => [
                'nullable',
                'string',
                new UniquePublicationPerPublisher($publishers),
            ],
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'year_published' => 'nullable|integer|max:' . date('Y'),
            'volumes' => 'nullable|array',
            'volumes.*.number' => 'required|integer',
            'volumes.*.year_published' => 'nullable|integer|max:' . date('Y'),
            'volumes.*.issues' => 'nullable|array',
            'volumes.*.issues.*.name' => 'required|string',
            'volumes.*.issues.*.month_published' => 'nullable|integer|between:1,12',
            'volumes.*.issues.*.articles' => 'nullable|array',
            'volumes.*.issues.*.articles.*.title' => 'required|string',
            'volumes.*.issues.*.articles.*.description' => 'nullable|string',
            'volumes.*.issues.*.articles.*.published_date' => 'nullable|date',
            'volumes.*.issues.*.articles.*.link' => 'required|url',
            'volumes.*.issues.*.articles.*.doi' => 'required|url',
            'volumes.*.issues.*.articles.*.pdf_link' => 'nullable|url',
            'volumes.*.issues.*.articles.*.authors' => 'nullable|array',
            'volumes.*.issues.*.articles.*.authors.*.name' => 'required|string',
            'volumes.*.issues.*.articles.*.authors.*.university' => 'nullable|string',
            'volumes.*.issues.*.articles.*.authors.*.profile_link' => 'required|url',
            'volumes.*.issues.*.articles.*.authors.*.orcid_link' => 'nullable|url',
            'volumes.*.issues.*.articles.*.keywords' => 'nullable|array',
            'volumes.*.issues.*.articles.*.keywords.*.name' => 'required|string',
        ];
    }
}
