<?php

namespace App\Repositories\Contracts;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\ShoppingList;
use App\Models\User;

interface PollRepository
{
    public function findForList(int $id, ShoppingList $list): Poll;

    public function findOption(int $id, Poll $poll): PollOption;

    /**
     * @param  list<string>  $options
     */
    public function create(ShoppingList $list, User $author, string $question, array $options): Poll;

    public function delete(Poll $poll): void;

    public function vote(Poll $poll, PollOption $option, User $user): void;

    public function unpinAll(ShoppingList $list): void;
}
