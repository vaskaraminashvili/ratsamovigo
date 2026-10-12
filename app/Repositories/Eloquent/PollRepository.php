<?php

namespace App\Repositories\Eloquent;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\ShoppingList;
use App\Models\User;
use App\Repositories\Contracts\PollRepository as PollRepositoryContract;

class PollRepository implements PollRepositoryContract
{
    public function findForList(int $id, ShoppingList $list): Poll
    {
        return Poll::query()
            ->where('shopping_list_id', $list->id)
            ->findOrFail($id);
    }

    public function findOption(int $id, Poll $poll): PollOption
    {
        return PollOption::query()
            ->where('poll_id', $poll->id)
            ->findOrFail($id);
    }

    public function create(ShoppingList $list, User $author, string $question, array $options): Poll
    {
        $poll = Poll::query()->create([
            'shopping_list_id' => $list->id,
            'user_id' => $author->id,
            'question' => $question,
        ]);

        foreach (array_values($options) as $position => $label) {
            $poll->options()->create([
                'label' => $label,
                'position' => $position,
            ]);
        }

        return $poll;
    }

    public function delete(Poll $poll): void
    {
        $poll->delete();
    }

    public function vote(Poll $poll, PollOption $option, User $user): void
    {
        PollVote::query()->updateOrCreate(
            [
                'poll_id' => $poll->id,
                'user_id' => $user->id,
            ],
            [
                'poll_option_id' => $option->id,
            ],
        );
    }

    public function unpinAll(ShoppingList $list): void
    {
        Poll::query()
            ->where('shopping_list_id', $list->id)
            ->where('is_pinned', true)
            ->update(['is_pinned' => false]);
    }
}
