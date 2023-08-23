<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\SubmissionTypeEnum;

class DocumentSubmissionRequest extends FormRequest
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
            'type' => 'required|numeric|in:0,1,2,3',
            'document_attachments' => 'required|array',
            'document_attachments.*.file' => 'required|file',
            'document_attachments.*.document_type' => 'required|string',
        ];
    }
}
