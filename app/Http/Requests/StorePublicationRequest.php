<?php

namespace App\Http\Requests;

use App\Rules\UniquePublicationPerPublisher;
use Illuminate\Foundation\Http\FormRequest;

class StorePublicationRequest extends FormRequest
{
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
        $publishers = collect($this->input('publishers', []))->pluck('name')->toArray();

        return [
            'publishers' => 'required|array',
            'publishers.*.name' => 'required|string',
            'publishers.*.scraper' => 'nullable|string',
            'type' => 'required|in:Journal,Magazine,Book',
            'title' => [
                'required',
                'string',
                new UniquePublicationPerPublisher([$publishers]),
            ],
            'issn' => [
                'nullable',
                'regex:/^\d{4}-\d{3}[\dxX]$/'
            ],
            'description' => 'nullable|string',
            'link' => 'required|url',
            'year_published' => 'nullable|integer|between:1900,' . date('Y'),
        ];
    }
}
