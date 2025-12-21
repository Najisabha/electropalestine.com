<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string'],
            'wallet_type' => ['nullable', 'string', 'max:50'],
            'wallet_phone' => ['nullable', 'string', 'max:50'],
            'agent_name' => ['nullable', 'string', 'max:255'],
            'agent_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}

