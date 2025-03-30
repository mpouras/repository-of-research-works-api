<?php

namespace App\Http\Requests\Scraper;

use App\Http\Requests\Scraper\Traits\ValidatesManyPublicationsTrait;
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
        $publishers = collect($this->all())->pluck('publishers')->flatten()->unique()->toArray();

        return [
            'id' => 'required|exists:publications,id',
            'scraper' => 'nullable|in:mdpi,acm,ieee',
            'publishers' => 'nullable|array',
            'publishers.*' => 'nullable|string',
            'type' => 'nullable|in:Journal,Magazine,Book',
            'title' => [
                'nullable',
                'string',
                new UniquePublicationPerPublisher($publishers),
            ],
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'year_published' => 'nullable|integer|min:1900|max:' . date('Y'),
        ];
    }
}
