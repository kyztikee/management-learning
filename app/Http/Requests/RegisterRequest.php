<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'detail.birth_place' => 'required|string',
            'detail.birth_date' => 'required|string',
            'detail.gender' => 'required|string|in:0,1',
            'detail.nik' => 'required|numeric',
            'detail.rt' => 'required|string|in:001,002,003,004,005,006,007,008,009,010,011,012',
            'detail.rw' => 'required|string|in:010,011,012',
            'detail.phone_no' => 'required|string',
            'detail.religion' => 'required|string',
        ];
    }
}
