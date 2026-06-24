<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'department_id' => ['nullable', 'exists:departments,id'],
            'document_category_id' => ['nullable', 'exists:document_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'content' => ['nullable', 'required_if:source_type,content', 'string'],
            'source_type' => ['required', Rule::in(['upload', 'external', 'content'])],
            'document_type' => ['required', Rule::in(['policy', 'procedure', 'manual', 'form', 'other'])],
            'file' => ['nullable', 'file', 'max:20480'],
            'external_url' => ['nullable', 'required_if:source_type,external', 'url', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'visibility' => ['required', Rule::in(['department', 'shared', 'public'])],
            'version' => ['required', 'string', 'max:20'],
            'requires_read_confirmation' => ['boolean'],
            'is_featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:published_at'],
            'visible_department_ids' => ['array'],
            'visible_department_ids.*' => ['integer', 'exists:departments,id'],
        ];
    }
}
