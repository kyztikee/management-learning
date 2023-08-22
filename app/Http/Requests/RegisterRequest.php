<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\UserRoleEnum;

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
            'detail.birth_date' => 'required|date',
            'detail.gender' => 'required|string|in:0,1',
            'detail.nik' => 'required|numeric',
            'detail.rt' => 'required|string|exists:staff,section_no',
            'detail.rw' => 'required|string|exists:staff,section_no',
            'detail.phone_no' => 'required|string',
            'detail.religion' => 'required|string',
        ];
    }
}
