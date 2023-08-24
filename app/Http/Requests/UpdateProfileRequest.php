<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'detail.birth_place' => 'required|string',
            'detail.birth_date' => 'required|date',
            'detail.gender' => 'required|string|in:0,1',
            'detail.nik' => 'required|numeric',
            'detail.rt' => 'required|string',
            'detail.rw' => 'required|string',
            'detail.phone_no' => 'required|string',
            'detail.religion' => 'required|string',
        ];
    }
}
