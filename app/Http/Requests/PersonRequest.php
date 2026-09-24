<?php

namespace App\Http\Requests;

use App\Support\Owned;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'organization_id' => ['nullable', Owned::exists('organizations')],
            'organization_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'email_label' => ['nullable', 'in:Work,Home,Other'],
            'phone' => ['nullable', 'string', 'max:50'],
            'phone_label' => ['nullable', 'in:Work,Home,Mobile,Other'],
            'job_title' => ['nullable', 'string', 'max:255'],
        ];
    }
}
