<?php

namespace App\Http\Requests\Scraper;


use App\Http\Requests\Scraper\Traits\ValidatesManyPublicationsTrait;
use App\Rules\UniquePublicationPerPublisher;
use Illuminate\Foundation\Http\FormRequest;

class StoreManyPublicationsRequest extends FormRequest
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
        $publishers = collect($this->all())->pluck('publishers')->flatten()->unique()->toArray();

        return [
            'publishers' => 'required|array',
            'publishers.*' => 'required|string',
            'scraper' => 'required|in:mdpi,acm,ieee,springer',
            'type' => 'required|in:Journal,Magazine,Book',
            'title' => [
                'required',
                'string',
                new UniquePublicationPerPublisher($publishers),
            ],
            'issn' => [
                'nullable',
                'regex:/^\d{4}-\d{3}[\dxX]$/'
            ],
            'description' => 'nullable|string',
            'link' => 'required|url',
            'year_published' => 'nullable|integer|min:1900|max:' . date('Y'),
        ];
    }
}
