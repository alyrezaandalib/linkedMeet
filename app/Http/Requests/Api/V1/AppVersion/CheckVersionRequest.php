<?php

namespace App\Http\Requests\Api\V1\AppVersion;

use Illuminate\Foundation\Http\FormRequest;

class CheckVersionRequest extends FormRequest
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
            "platform" => "required|in:android,ios,web",
            "current_version" => "required|regex:/^\d+\.\d+\.\d+$/",
        ];
    }

    public function messages(): array
    {
        return [
            'current_version.regex' => 'The version must follow the format x.y.z (e.g. 1.2.4).',
        ];
    }
}
