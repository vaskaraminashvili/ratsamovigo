<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyFavoritesRequest extends FormRequest
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
            'favorite_item_id' => ['nullable', 'integer'],
            'favorite_dish_id' => ['nullable', 'integer'],
            'ingredient_ids' => ['nullable', 'array'],
            'ingredient_ids.*' => ['integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'favorite_item_id.integer' => 'რჩეული ნივთი არასწორია.',
            'favorite_dish_id.integer' => 'რჩეული კერძი არასწორია.',
            'ingredient_ids.array' => 'ინგრედიენტები არასწორია.',
            'ingredient_ids.*.integer' => 'ინგრედიენტი არასწორია.',
        ];
    }
}
