<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_a_successful_response(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('ერთი სია', false);
        $response->assertSee('შექმენი პირველი სია', false);
        $response->assertSee('საერთო საყიდლები, მარტივად.', false);
        $response->assertDontSee('Gather', false);
    }
}
