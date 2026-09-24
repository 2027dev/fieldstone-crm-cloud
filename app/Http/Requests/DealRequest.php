<?php

namespace App\Http\Requests;

use App\Enums\DealStage;
use App\Support\Owned;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DealRequest extends FormRequest
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
            'value' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'currency' => ['nullable', 'in:USD,EUR,GBP'],
            'stage' => ['required', Rule::enum(DealStage::class)],
            'person_id' => ['nullable', Owned::exists('people')],
            'organization_id' => ['nullable', Owned::exists('organizations')],
            'expected_close_date' => ['nullable', 'date'],
        ];
    }
}
