<?php

namespace App\Models;

use App\Enums\BoughtBehavior;
use App\Enums\CheckboxStyle;
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
 * @property int $user_id
 * @property string $name
 * @property string|null $emoji
 * @property string $accent_color
 * @property string $background_preset
 * @property CheckboxStyle $checkbox_style
 * @property BoughtBehavior $bought_behavior
 * @property bool $show_emoji
 * @property bool $show_quantity
 * @property bool $show_likes
 * @property bool $show_author
 * @property bool $fetch_best_price
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
    'checkbox_style',
    'bought_behavior',
    'show_emoji',
    'show_quantity',
    'show_likes',
    'show_author',
    'fetch_best_price',
])]
class ShoppingList extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'bought_behavior' => 'strike',
        'fetch_best_price' => false,
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('background')->singleFile();
    }

    public function backgroundUrl(): ?string
    {
        return $this->sameOriginMediaUrl('background');
    }

    private function sameOriginMediaUrl(string $collection): ?string
    {
        $url = $this->getFirstMediaUrl($collection);

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
            'checkbox_style' => CheckboxStyle::class,
            'bought_behavior' => BoughtBehavior::class,
            'show_emoji' => 'boolean',
            'show_quantity' => 'boolean',
            'show_likes' => 'boolean',
            'show_author' => 'boolean',
            'fetch_best_price' => 'boolean',
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
