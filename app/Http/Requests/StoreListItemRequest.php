<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListItemRequest extends FormRequest
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
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'ნივთის სახელი სავალდებულოა.',
            'photo.image' => 'ფაილი უნდა იყოს სურათი.',
            'photo.max' => 'ფოტო არ უნდა აღემატებოდეს 2 მბ-ს.',
        ];
    }
}
