<?php

namespace App\Http\Requests;

use App\Enums\LeadLabel;
use App\Enums\LeadSource;
use App\Support\Owned;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'person_id' => ['nullable', Owned::exists('people')],
            'organization_id' => ['nullable', Owned::exists('organizations')],
            'value' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'currency' => ['nullable', 'in:USD,EUR,GBP'],
            'source' => ['nullable', Rule::enum(LeadSource::class)],
            'label' => ['nullable', Rule::enum(LeadLabel::class)],
            'message' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
