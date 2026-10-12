<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['list_item_id', 'user_id'])]
class ItemLike extends Model
{
    /**
     * @return BelongsTo<ListItem, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(ListItem::class, 'list_item_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
