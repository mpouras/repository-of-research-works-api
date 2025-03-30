<?php

namespace App\Http\Requests;

use App\Helpers\EntitiesFind;
use App\Rules\UniqueIssueNumberPerVolume;
use Illuminate\Foundation\Http\FormRequest;

class UpdateIssueRequest extends FormRequest
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

        $volumeId = $this->volume($publicationId, $volumeNumber)->id;

        return [
            'name' => [
                'nullable',
                'string',
                new UniqueIssueNumberPerVolume($volumeId),
            ],
            'month_published' => 'nullable|integer|between:1,12',
        ];
    }
}
