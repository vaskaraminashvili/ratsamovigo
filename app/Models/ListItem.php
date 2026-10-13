<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property int $shopping_list_id
 * @property int $user_id
 * @property int|null $checked_by
 * @property int|null $claimed_by
 * @property string $name
 * @property string|null $group_name
 * @property string|null $quantity
 * @property string|null $emoji
 * @property string|null $note
 * @property bool $is_pinned
 * @property bool $is_checked
 * @property Carbon|null $checked_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'shopping_list_id',
    'user_id',
    'checked_by',
    'claimed_by',
    'name',
    'group_name',
    'quantity',
    'emoji',
    'note',
    'is_pinned',
    'is_checked',
    'checked_at',
])]
class ListItem extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function photoUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('photo');

        if ($url === '') {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);

        if (! is_string($path) || $path === '') {
            return $url;
        }

        return $query ? $path.'?'.$query : $path;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_checked' => 'boolean',
            'checked_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ShoppingList, $this>
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class, 'shopping_list_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function claimer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    /**
     * @return HasMany<ItemLike, $this>
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ItemLike::class);
    }
}
