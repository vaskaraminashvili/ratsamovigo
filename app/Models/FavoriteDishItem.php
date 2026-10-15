<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $favorite_dish_id
 * @property string $name
 * @property string|null $quantity
 * @property string|null $emoji
 * @property string|null $note
 * @property int $position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'favorite_dish_id',
    'name',
    'quantity',
    'emoji',
    'note',
    'position',
])]
class FavoriteDishItem extends Model
{
    use SoftDeletes;

    /**
     * @return BelongsTo<FavoriteDish, $this>
     */
    public function dish(): BelongsTo
    {
        return $this->belongsTo(FavoriteDish::class, 'favorite_dish_id');
    }
}
