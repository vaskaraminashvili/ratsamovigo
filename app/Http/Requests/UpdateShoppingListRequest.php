<?php

namespace App\Http\Requests;

use App\Enums\CheckboxStyle;
use App\Support\ListAppearance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShoppingListRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:80'],
            'emoji' => ['nullable', 'string', 'max:8'],
            'accent_color' => ['required', Rule::in(ListAppearance::ACCENTS)],
            'background_preset' => ['required', Rule::in(array_keys(ListAppearance::PRESETS))],
            'checkbox_style' => ['required', Rule::enum(CheckboxStyle::class)],
            'show_emoji' => ['boolean'],
            'show_quantity' => ['boolean'],
            'show_likes' => ['boolean'],
            'show_author' => ['boolean'],
            'background' => ['nullable', 'image', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'სიის სახელი სავალდებულოა.',
            'background.image' => 'ფონი უნდა იყოს სურათი.',
            'background.max' => 'ფონი არ უნდა აღემატებოდეს 2 მბ-ს.',
        ];
    }
}
