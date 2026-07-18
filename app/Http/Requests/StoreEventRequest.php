<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'id' => 'required|string|max:255',
            'account_id' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'occurred_at' => 'required|date',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'id.required' => 'Event ID is required.',
            'account_id.required' => 'Account ID is required.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'occurred_at.required' => 'Occurred At is required.',
            'occurred_at.date' => 'Occurred At must be a valid date.',
        ];
    }
}