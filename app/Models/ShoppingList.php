<?php

namespace App\Models;

use App\Enums\CheckboxStyle;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $emoji
 * @property string $accent_color
 * @property string $background_preset
 * @property string|null $background_path
 * @property CheckboxStyle $checkbox_style
 * @property bool $show_emoji
 * @property bool $show_quantity
 * @property bool $show_likes
 * @property bool $show_author
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'user_id',
    'name',
    'emoji',
    'accent_color',
    'background_preset',
    'background_path',
    'checkbox_style',
    'show_emoji',
    'show_quantity',
    'show_likes',
    'show_author',
])]
class ShoppingList extends Model
{
    use SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'checkbox_style' => CheckboxStyle::class,
            'show_emoji' => 'boolean',
            'show_quantity' => 'boolean',
            'show_likes' => 'boolean',
            'show_author' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany<ListMember, $this>
     */
    public function members(): HasMany
    {
        return $this->hasMany(ListMember::class);
    }

    /**
     * @return HasMany<ShareLink, $this>
     */
    public function shareLinks(): HasMany
    {
        return $this->hasMany(ShareLink::class);
    }

    /**
     * @return HasMany<ListItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(ListItem::class);
    }

    /**
     * @return HasMany<Poll, $this>
     */
    public function polls(): HasMany
    {
        return $this->hasMany(Poll::class);
    }
}
