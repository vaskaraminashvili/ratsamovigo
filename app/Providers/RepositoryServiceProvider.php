<?php

namespace App\Providers;

use App\Contracts\DealOfferProvider;
use App\Repositories\Contracts\FavoriteDishRepository;
use App\Repositories\Contracts\FavoriteItemRepository;
use App\Repositories\Contracts\ListItemRepository;
use App\Repositories\Contracts\PollRepository;
use App\Repositories\Contracts\ShoppingListRepository;
use App\Repositories\Eloquent\FavoriteDishRepository as EloquentFavoriteDishRepository;
use App\Repositories\Eloquent\FavoriteItemRepository as EloquentFavoriteItemRepository;
use App\Repositories\Eloquent\ListItemRepository as EloquentListItemRepository;
use App\Repositories\Eloquent\PollRepository as EloquentPollRepository;
use App\Repositories\Eloquent\ShoppingListRepository as EloquentShoppingListRepository;
use App\Services\NullDealOfferProvider;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ShoppingListRepository::class, EloquentShoppingListRepository::class);
        $this->app->bind(ListItemRepository::class, EloquentListItemRepository::class);
        $this->app->bind(PollRepository::class, EloquentPollRepository::class);
        $this->app->bind(FavoriteItemRepository::class, EloquentFavoriteItemRepository::class);
        $this->app->bind(FavoriteDishRepository::class, EloquentFavoriteDishRepository::class);
        $this->app->bind(DealOfferProvider::class, NullDealOfferProvider::class);
    }
}
