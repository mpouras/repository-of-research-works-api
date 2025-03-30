<?php

namespace App\Http\Requests\User;

use App\Rules\LinkedInUrl;
use App\Rules\MinimumUserAge;
use Illuminate\Foundation\Http\FormRequest;
use Rinvex\Country\CountryLoader;

class UpdateUserRequest extends FormRequest
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
    public function rules()
    {
        $countries = collect(CountryLoader::countries())->map(function ($country) {
            return $country['name'];
        })->toArray();

        return [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $this->user()->id,
            'birthday' => ['nullable', 'date'],
            'gender' => 'nullable|in:Male,Female',
            'country' => 'nullable|string|in:' . implode(',', $countries),
            'bio' => 'nullable|string|max:100',
            'linkedin_url' => ['nullable', new LinkedInUrl()],
            'photo' => 'nullable|image|mimes:bmp,png,jpg,jpeg|max:2048',
        ];
    }
}
