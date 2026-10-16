<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppearanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_appearance_page_offers_text_fonts(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('appearance.edit'))
            ->assertOk()
            ->assertSee('ძირითადი ტექსტი')
            ->assertSee('ALK Life')
            ->assertSee('მზექალა')
            ->assertSee('slip-text-font', false);
    }
}
