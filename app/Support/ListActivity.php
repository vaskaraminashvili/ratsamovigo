<?php

namespace App\Support;

use App\Models\ShoppingList;
use Illuminate\Support\Collection;

class ListActivity
{
    /**
     * @return Collection<int, array{at: mixed, name: string, initials: string, text: string, seed: string}>
     */
    public static function events(ShoppingList $list): Collection
    {
        $events = collect();

        foreach ($list->items as $item) {
            if ($item->author) {
                $events->push([
                    'at' => $item->created_at,
                    'name' => $item->author->name,
                    'initials' => $item->author->initials(),
                    'seed' => (string) $item->author->id,
                    'text' => 'დაამატა '.$item->name,
                ]);
            }

            if ($item->claimer) {
                $events->push([
                    'at' => $item->updated_at,
                    'name' => $item->claimer->name,
                    'initials' => $item->claimer->initials(),
                    'seed' => (string) $item->claimer->id,
                    'text' => 'იღებს '.$item->name,
                ]);
            }

            if ($item->is_checked && $item->checker) {
                $events->push([
                    'at' => $item->checked_at ?? $item->updated_at,
                    'name' => $item->checker->name,
                    'initials' => $item->checker->initials(),
                    'seed' => (string) $item->checker->id,
                    'text' => 'მონიშნა '.$item->name,
                ]);
            }
        }

        foreach ($list->polls as $poll) {
            if ($poll->author) {
                $events->push([
                    'at' => $poll->created_at,
                    'name' => $poll->author->name,
                    'initials' => $poll->author->initials(),
                    'seed' => (string) $poll->author->id,
                    'text' => 'გამოკითხვა: '.$poll->question,
                ]);
            }

            foreach ($poll->votes as $vote) {
                if (! $vote->user) {
                    continue;
                }

                $events->push([
                    'at' => $vote->created_at,
                    'name' => $vote->user->name,
                    'initials' => $vote->user->initials(),
                    'seed' => (string) $vote->user->id,
                    'text' => 'ხმა მისცა: '.$poll->question,
                ]);
            }
        }

        return $events->sortByDesc(fn (array $event) => $event['at']?->getTimestamp() ?? 0)->values();
    }
}
