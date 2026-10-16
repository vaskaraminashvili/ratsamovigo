# Product search and bought-item behavior

Two changes on an existing list.

- While typing a new item, after a few characters the app asks tarifebi.ge for grocery matches and shows the best price. The person can tap a match, or ignore the matches and save the typed text.
- Each list chooses what a bought line does: stay visible with a strikethrough, or leave the screen until the list is used again. A standing evening list keeps the same lines, so eggs bought tonight can be bought again tomorrow.

## What already exists

- New lines are created only by `ListItemService::add()`. The create sheet on `App\Livewire\Lists\Show` submits `itemName`, quantity, unit, note, and an optional photo.
- A checked line stays on the list. `resources/views/livewire/lists/partials/item.blade.php` draws it with `line-through`. `ListItemService::clearChecked()` soft-deletes every checked line. The list menu labels that action „მონიშნულის გასუფთავება“.
- List appearance (`show_quantity`, checkbox style, and the rest) is stored on `shopping_lists` and edited from the „სიის სახე“ sheet by the list owner.

## Product suggestions while adding

Search runs on the create sheet, on the item name and on an ingredient name. It does not run while editing a line that is already on the list.

After 3 characters, wait briefly, then search. Fewer than 3 characters shows nothing. The person can still press „დამატება“ at any time.

The query goes to our app, and the app calls:

`https://tarifebi.ge/api/products.php?store_type=grocery&q={text}&limit=6&group=1&sort=relevance&page=1`

`group=1` already returns one row per product, and that row is the cheapest store. Use the row itself. Do not open `other_stores`.

Show up to 6 rows under the name field:

- product name
- size, from `unit` when it is present (for example `500 მლ`)
- price in lari: `sale_price` when it is set, otherwise `original_price`
- store name (კარფური, სპარი, …)

### Choosing a row, or not

Tap a row and the line is added immediately through `ListItemService::add()`. The sheet closes. The saved line keeps:

- name from the product
- quantity from `unit` when `unit` is present; the quantity chips on the form are left unused for that tap
- the price, store name, store slug, size, external product id, and the winning store’s `product_url`

Ignore the rows and press „დამატება“, and the line is the typed text only. Price, store, and size stay empty. That is the same item the app saves today.

A failed or slow search does not block typing or saving. Empty results show no rows. The submit button still adds the typed text.

Prices are a snapshot from the moment of the tap. Opening the list later does not ask tarifebi again.

On the list row, a catalog line shows the price, store, and size under the name, for example `1.39 ₾ · კარფური · 500 მლ`. A plain line does not.

## Bought lines

New list setting, default `strike`, so current lists keep today’s look.

| Value | What the person sees |
| --- | --- |
| `strike` | Checked lines stay, crossed out. „მონიშნულის გასუფთავება“ still deletes them. |
| `hide` | Checked lines leave the list body. They are not deleted. |

`hide` is the standing list. The same eggs line remains in the database after it is checked. Tonight it is off the screen. Tomorrow it can be checked again.

In `hide`:

- The list body shows unchecked lines only. A dish section shows only its unchecked ingredients. A dish whose ingredients are all checked is omitted.
- Near the list menu, show how many lines are hidden, with a way to reveal them. Revealing is for a mistaken check: the person can uncheck one line and it returns to the list.
- Replace „მონიშნულის გასუფთავება“ with an action that unchecks every checked line and leaves the rows in place. Label: „თავიდან“. After that, eggs is visible and unchecked, ready for the next evening.
- Counts at the top use the visible unchecked lines. Hidden lines are not shown as still to buy.

Switching a list from `hide` back to `strike` shows the checked lines with a strikethrough. Switching the other way hides the lines that are already checked. Neither switch deletes rows.

The setting lives on „სიის სახე“, next to the other list switches. Only the owner can change it. Members who can edit still check lines; they follow the list’s choice.

## Data

No product table. Search reads tarifebi at request time. A short cache (about 10 minutes, keyed by the trimmed query) avoids repeating the same lookup while someone types. Cache stores the six display rows, not the raw payload’s store list.

`shopping_lists`

| Column | Notes |
| --- | --- |
| `bought_behavior` | string, 16, default `strike`. Allowed values: `strike`, `hide`. |

`list_items` — all nullable, set only when the line was tapped from search

| Column | Notes |
| --- | --- |
| `catalog_product_id` | unsigned big integer, tarifebi id, not a foreign key |
| `catalog_store_name` | string, max 80 |
| `catalog_store_slug` | string, max 40 |
| `catalog_price` | decimal 8,2, the effective price |
| `catalog_unit` | string, max 40 |
| `catalog_url` | string, max 255, winning store link |

Soft deletes, foreign keys, and timestamps stay as they are. Plain text lines leave the catalog columns null.

## Application shape

Follow the existing list stack.

- `ProductSearch` is an HTTP client, not a repository. It accepts the query, skips calls under 3 characters, times out, and returns a small list of suggestions: name, unit, price, store name, store slug, product id, url. `ListItemRepository` stays the only writer of `list_items`.
- `ListItemService::add()` accepts the optional catalog fields and stores them in the same create call. A suggestion tap and a plain submit both go through `add()`.
- `ShoppingListService` saves `bought_behavior` with the other appearance fields.
- `ListItemService::reopenChecked()` unchecks every checked line on that list. It does not delete. Used only when the list is `hide`.
- `ListItemService::clearChecked()` stays the delete path for `strike`.
- Livewire: `App\Livewire\Lists\Show` debounces the name (`wire:model.live`), asks `ProductSearch`, and either calls `add()` with catalog fields or with the typed name. The appearance sheet writes `bought_behavior`.
- Search and both bought actions require `ListAccess::requireEdit`. Changing the setting requires the list owner, same as the rest of „სიის სახე“.

## Interface notes

Georgian, same tone as the list screen.

- Suggestion price line: `1.39 ₾ · კარფური`
- Size, when present, on the same line or directly under the name
- No matches: show nothing; the add button is the way out
- Appearance labels: „ნაყიდი იხაზება“ and „ნაყიდი იმალება“
- Hidden count: „დამალულია 3“
- Reset for the next evening: „თავიდან“

Catalog price and store use the existing item row. They do not replace the name.

## Limits

One grocery search, Tbilisi results as the API returns them, six rows, best store only. No plan gate. `config/plans.php` has `enforce` off, and the calls are reads.

If tarifebi is unavailable, adding text still works.

## Out of scope

- A local product database, and any work on where tarifebi prices come from.
- Refreshing a saved price later, or picking a store other than the cheapest.
- Search while editing an existing line, on the favorites screen, or inside a poll.
- City or store-type filters.
- Downloading product photos into our media library.
- A separate list type. Any list can choose `hide`; the evening list is that setting plus „თავიდან“.

## Build order

1. Migration for `bought_behavior` and the catalog columns on `list_items`.
2. `ProductSearch` with a fake HTTP test: three-character minimum, sale price preferred over original price, store and unit mapped, timeout or non-success returns an empty list.
3. `ListItemService::add()` stores catalog fields when they are passed, and stores nulls for a plain name.
4. Create sheet: debounced suggestions, tap adds the catalog line, submit without a tap adds the typed text.
5. `bought_behavior` on the appearance sheet. `hide` filters the list body, reveals hidden lines, and „თავიდან“ unchecks instead of deleting. `strike` keeps strikethrough and delete.
6. Browser pass: type ნაბეღლავი, tap the 0.5 ლ row, confirm price, size, and store on the line; add „კვერცხი“ with no tap and confirm it has no price; set the list to hide, check both lines, confirm the body is empty of them, run „თავიდან“, and confirm both lines are back and unchecked.

## Tests

Feature tests in `tests/Feature/ProductSearchTest.php` and `tests/Feature/BoughtBehaviorTest.php`.

- A query shorter than 3 characters does not call the HTTP client.
- A successful payload becomes one suggestion with sale price, store name, and unit.
- Adding from a suggestion writes the catalog columns. Adding typed text leaves them null.
- A viewer cannot search or add.
- `hide` keeps a checked row and omits it from the visible item set.
- „თავიდან“ sets `is_checked` false and does not soft-delete.
- `strike` still soft-deletes on clear.
- Changing `bought_behavior` is rejected for a member who is not the owner.
