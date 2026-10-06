<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConversationRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'professional_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', User::ROLE_PROFESSIONAL),
            ],
            'assunto' => ['required', 'string', 'max:120'],
            'corpo' => ['required', 'string', 'max:2000'],
        ];
    }
}
