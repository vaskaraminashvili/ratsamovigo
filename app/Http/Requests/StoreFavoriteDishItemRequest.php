<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFavoriteDishItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'quantity' => ['nullable', 'string', 'max:40'],
            'emoji' => ['nullable', 'string', 'max:8'],
            'note' => ['nullable', 'string', 'max:200'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'ინგრედიენტის სახელი სავალდებულოა.',
            'name.max' => 'სახელი ძალიან გრძელია.',
            'quantity.max' => 'რაოდენობა ძალიან გრძელია.',
            'emoji.max' => 'ემოჯი ძალიან გრძელია.',
            'note.max' => 'შენიშვნა ძალიან გრძელია.',
        ];
    }
}
