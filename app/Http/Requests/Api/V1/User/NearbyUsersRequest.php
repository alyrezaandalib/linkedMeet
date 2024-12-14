<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class NearbyUsersRequest extends FormRequest
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
        return [
            'job_title_ids' => ['nullable', 'array'],
            'job_title_ids.*' => ['integer', 'exists:job_titles,id'],
            'industry_ids' => ['nullable', 'array'],
            'industry_ids.*' => ['integer', 'exists:industries,id'],
        ];
    }
}
