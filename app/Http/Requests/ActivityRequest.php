<?php

namespace App\Http\Requests;

use App\Enums\ActivityPriority;
use App\Enums\ActivityType;
use App\Support\Owned;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityRequest extends FormRequest
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
            'type' => ['required', Rule::enum(ActivityType::class)],
            'subject' => ['required', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'due_time' => ['nullable', 'date_format:H:i'],
            'duration_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'priority' => ['nullable', Rule::enum(ActivityPriority::class)],
            'outcome' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:5000'],
            'person_id' => ['nullable', Owned::exists('people')],
            'deal_id' => ['nullable', Owned::exists('deals')],
            'organization_id' => ['nullable', Owned::exists('organizations')],
            'lead_id' => ['nullable', Owned::exists('leads')],
            'done' => ['sometimes', 'boolean'],
        ];
    }
}
