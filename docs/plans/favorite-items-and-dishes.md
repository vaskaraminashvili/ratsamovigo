# Favorite items and dishes

Save personal staples on the profile, then drop them onto a list later without retyping them.

Two kinds of favorites:

- **Favorite item.** One shopping line, with its own note. Example: wine, note “best bottle is at this address”.
- **Favorite dish.** A named meal and the ingredients that belong to it. Example: pasta, with pasta, sauce, and cheese. Adding the dish creates those lines under one dish group.

Favorites belong to the user. They are not shared. Copying one onto a list creates ordinary list items. After that, the list copy and the favorite are independent: editing one does not change the other.

## What already exists

Lists already support the shape this feature needs.

- A loose item is a `list_items` row with an empty `group_name`.
- A dish is several `list_items` rows that share the same `group_name`. The list screen labels that group “კერძი”.
- Each item already has `name`, `quantity`, `emoji`, and `note` (max 200). The wine-address example fits the existing note.
- `ListItemService::add()` is the only write path for new lines. Quick-add must go through it so list access and photo rules stay in one place.
- Profile today is name and email at `settings/profile`. Favorites get their own settings screen, linked from the profile area.

## User flows

### Keep favorites on the profile

New screen: `settings/favorites` (`profile.favorites`), in the settings nav next to Profile.

Two sections on one page.

**Items**

- Add, edit, delete a favorite item: name, quantity, emoji, note.
- Note is optional and visible on the card, so “best wine is at this address” is readable before it is added to a list.

**Dishes**

- Add, edit, delete a dish: dish name, optional emoji, optional note.
- Under the dish, add, edit, reorder, and delete ingredients. An ingredient has the same fields as a favorite item.
- Empty dish (name only, no ingredients) is allowed. Adding it to a list creates nothing until it has ingredients. The picker explains that.

### Put a favorite on a list

On an open list, editors see “რჩეულებიდან” next to the add control.

Picker has two tabs: items and dishes.

- **Item.** Tap adds one unchecked line. Name, quantity, emoji, and note are copied. It is not placed in a dish.
- **Dish.** Tap opens the ingredient list with every ingredient selected. Uncheck the ones already at home, then confirm. Selected ingredients are added as unchecked lines with `group_name` set to the dish name.
- If that dish name already exists on the list, new ingredients join it. They are not merged with lines that happen to have the same item name.
- A line that is already on the list is still added again. Quantity merge can come later.
- The person adding becomes `user_id` on the new rows. Checked, claimed, pinned, and likes are not copied.
- Viewers without edit access do not see the picker.

Same picker is available while creating a list, after the list exists, so a new list can be filled from favorites in the same step.

### Save from a list back to the profile

On a list the user can edit:

- Item menu: “რჩეულებში შენახვა” stores that line as a favorite item.
- Dish header: “კერძის შენახვა” stores the dish name and its current ingredients.

Saving copies current name, quantity, emoji, and note. It does not keep a link to the list row.

If the user already has a favorite item with the same name, or a favorite dish with the same name, ask before replacing. Replacing updates that favorite. Cancelling leaves it unchanged.

## Data

New tables. Soft deletes, foreign keys, timestamps.

`favorite_items`

| Column | Notes |
| --- | --- |
| `id` | |
| `user_id` | owner, cascade delete, indexed |
| `name` | required, max 120 |
| `quantity` | nullable, max 40 |
| `emoji` | nullable, max 16 |
| `note` | nullable, max 200 |
| `position` | unsigned small int, default 0 |

`favorite_dishes`

| Column | Notes |
| --- | --- |
| `id` | |
| `user_id` | owner, cascade delete, indexed |
| `name` | required, max 120 |
| `emoji` | nullable, max 16 |
| `note` | nullable, max 200 |
| `position` | unsigned small int, default 0 |

Unique on `(user_id, name)` is not required. Two dishes may share a name only through the replace prompt; the database allows duplicates so a failed prompt cannot block a save.

`favorite_dish_items`

| Column | Notes |
| --- | --- |
| `id` | |
| `favorite_dish_id` | cascade delete, indexed |
| `name` | required, max 120 |
| `quantity` | nullable, max 40 |
| `emoji` | nullable, max 16 |
| `note` | nullable, max 200 |
| `position` | unsigned small int, default 0 |

Photos stay off favorites in the first version. List-item photos are a paid entitlement and are stored on the list row. A later version can add a single media file per favorite and copy it only when the list owner may upload item photos.

Models stay thin. Relationships live on the repositories, matching the rest of the app. `User` does not grow query logic.

## Application shape

Follow the existing list stack.

- `FavoriteItemRepository` and `FavoriteDishRepository` contracts, Eloquent implementations, bindings in `RepositoryServiceProvider`.
- `FavoriteService` owns create, update, delete, reorder, and “save this list item / dish”.
- `FavoriteApplyService` owns “copy onto this shopping list”. It calls `ListItemService::add()` once per line inside a transaction. If any add fails, none of that batch is kept.
- Form requests: `StoreFavoriteItemRequest`, `UpdateFavoriteItemRequest`, `StoreFavoriteDishRequest`, `UpdateFavoriteDishRequest`, `StoreFavoriteDishItemRequest`, `ApplyFavoritesRequest`. Validation messages in Georgian, same limits as `StoreListItemRequest`.
- Livewire: `App\Livewire\Settings\Favorites` for the library. List picker methods live on `App\Livewire\Lists\Show`, delegating to `FavoriteApplyService`.
- Copy onto a list requires `ListAccess::requireEdit`. Managing favorites requires the authenticated owner only. Another member cannot read or write someone else’s favorites.

Suggested service methods:

```php
FavoriteService::saveItem(User $user, array $data): FavoriteItem
FavoriteService::saveDish(User $user, array $data): FavoriteDish
FavoriteService::rememberItem(User $user, ListItem $item): FavoriteItem
FavoriteService::rememberDish(User $user, ShoppingList $list, string $groupName): FavoriteDish

FavoriteApplyService::applyItem(User $user, ShoppingList $list, FavoriteItem $item): ListItem
FavoriteApplyService::applyDish(User $user, ShoppingList $list, FavoriteDish $dish, array $ingredientIds): void
```

`applyDish` ignores ingredient ids that do not belong to that dish.

## Interface notes

Georgian copy, same tone as the list screen.

- Settings label: რჩეულები
- Empty items: ჯერ არაფერი გაქვს შენახული. დაამატე ღვინო, რძე, ან სხვა რაც ხშირად გჭირდება.
- Empty dishes: შეინახე კერძი ინგრედიენტებით, რომ სიაში ერთი შეხებით ჩასვა.
- Picker title: რჩეულებიდან
- Dish confirm: დაამატე არჩეული ინგრედიენტები
- Item note stays under the name in the picker, truncated to two lines, full text on the favorites screen.

The picker is a sheet on the list screen, not a new route. Favorites management is a full settings page because editing many ingredients needs more room than a sheet.

## Limits

No plan gate in the first version. `config/plans.php` has `enforce` off, and favorites are private.

Add a later cap only if the library grows without bound: for example 100 items and 40 dishes per user, enforced in `FavoriteService` the same way `EntitlementService` reads `config/plans.php`.

## Out of scope

- Sharing a favorite dish with another user.
- Linking a favorite to a list row so later edits sync.
- Merging quantity when the same item is already on the list.
- Photos on favorites.
- Store addresses as structured places. The address stays inside the item note.
- Suggesting favorites from past lists automatically.

## Build order

1. Migration, models, repositories, provider bindings.
2. `FavoriteService` and feature tests for ownership: a user cannot read or change another user’s favorites.
3. Settings screen: items first, then dishes with ingredients.
4. `FavoriteApplyService` and tests: item copy keeps the note; dish copy sets `group_name`; unchecked ingredients are skipped; a viewer cannot apply; an existing dish group receives the new lines.
5. List picker and “save to favorites” from an item and from a dish header.
6. Browser pass: create a wine item with a note and a pasta dish, add the wine to an open list, add pasta with one ingredient unchecked, confirm the note and the dish group on the list, then open a second list and add the same favorites again.

## Tests

Feature tests in `tests/Feature/FavoriteTest.php`.

- Owner can create an item with a note and a dish with ingredients.
- Another user gets 404 or authorization failure for those records.
- Applying an item copies name, quantity, emoji, and note, and leaves the line unchecked and ungrouped.
- Applying a dish copies only selected ingredients and sets `group_name` to the dish name.
- Applying into a list the user cannot edit is rejected and writes nothing.
- Remembering a list item does not change the list item.
- Deleting a dish deletes its ingredients.
