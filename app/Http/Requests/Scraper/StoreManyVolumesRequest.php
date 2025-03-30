<?php

namespace App\Http\Requests\Scraper;

use App\Helpers\EntitiesFind;
use App\Http\Requests\Scraper\Traits\ValidatesManyVolumesTrait;
use App\Rules\MinimumVolumePublishedYear;
use App\Rules\SequentialVolumeNumber;
use Illuminate\Foundation\Http\FormRequest;

class StoreManyVolumesRequest extends FormRequest
{
    use ValidatesManyVolumesTrait, EntitiesFind;
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
        $publication = $this->publication($publicationId);

        return [
            'number' => [
                'required',
                'integer',
                new SequentialVolumeNumber($publication),
            ],
            'year_published' => [
                'required',
                'integer',
                new MinimumVolumePublishedYear($publication),
                'max:' . date('Y'),
            ],
        ];
    }
}
