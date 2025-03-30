<?php

namespace App\Http\Requests\Scraper;

use App\Helpers\EntitiesFind;
use App\Http\Requests\Scraper\Traits\ValidatesManyIssuesTrait;
use App\Rules\UniqueIssueNumberPerVolume;
use Illuminate\Foundation\Http\FormRequest;

class StoreManyIssuesRequest extends FormRequest
{
    use ValidatesManyIssuesTrait, EntitiesFind;
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
                'required',
                'string',
                new UniqueIssueNumberPerVolume($volumeId),
            ],
            'month_published' => 'required|integer|between:1,12',
        ];
    }
}
