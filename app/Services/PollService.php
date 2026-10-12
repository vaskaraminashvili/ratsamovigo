<?php

namespace App\Services;

use App\Models\ListItem;
use App\Models\Poll;
use App\Models\ShoppingList;
use App\Models\User;
use App\Repositories\Contracts\ListItemRepository;
use App\Repositories\Contracts\PollRepository;
use Illuminate\Validation\ValidationException;

class PollService
{
    public function __construct(
        private PollRepository $polls,
        private ListItemRepository $items,
        private ListAccess $access,
    ) {}

    /**
     * @param  list<string>  $options
     */
    public function create(User $user, ShoppingList $list, string $question, array $options): Poll
    {
        $this->access->requireEdit($user, $list);

        $options = collect($options)
            ->map(fn (string $option) => trim($option))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (count($options) < 2) {
            throw ValidationException::withMessages([
                'options' => 'გამოკითხვას სჭირდება მინიმუმ 2 ვარიანტი.',
            ]);
        }

        return $this->polls->create($list, $user, trim($question), $options);
    }

    public function vote(User $user, ShoppingList $list, int $pollId, int $optionId): void
    {
        $this->access->role($user, $list);

        $poll = $this->polls->findForList($pollId, $list);
        $option = $this->polls->findOption($optionId, $poll);
        $this->polls->vote($poll, $option, $user);
    }

    public function promoteWinner(User $user, ShoppingList $list, int $pollId): ListItem
    {
        $this->access->requireEdit($user, $list);

        $poll = $this->polls->findForList($pollId, $list);
        $poll->load('options.votes');

        $winner = $poll->options->sortByDesc(fn ($option) => $option->votes->count())->first();

        if (! $winner || $winner->votes->count() === 0) {
            throw ValidationException::withMessages([
                'poll' => 'ჯერ არავის დაუფიქსირებია ხმა.',
            ]);
        }

        $item = $this->items->create([
            'shopping_list_id' => $list->id,
            'user_id' => $user->id,
            'name' => $winner->label,
        ]);

        $this->polls->delete($poll);

        return $item;
    }

    public function delete(User $user, ShoppingList $list, int $pollId): void
    {
        $this->access->requireEdit($user, $list);
        $this->polls->delete($this->polls->findForList($pollId, $list));
    }

    public function togglePin(User $user, ShoppingList $list, int $pollId): void
    {
        $this->access->requireEdit($user, $list);

        $poll = $this->polls->findForList($pollId, $list);
        $pinned = ! $poll->is_pinned;

        if ($pinned) {
            $this->items->unpinAll($list);
        }

        $poll->update(['is_pinned' => $pinned]);
    }
}
