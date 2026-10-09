<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs the user out and redirects to the login page', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

it('invalidates the session and regenerates the token on logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->withSession(['cart' => 'kept?']);
    $oldToken = session()->token();

    $this->post(route('logout'))->assertRedirect(route('login'));

    expect(session()->has('cart'))->toBeFalse()
        ->and(session()->token())->not->toBe($oldToken);
});

it('logs the user out when they visit the logout URL', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

it('lets a guest hit logout without an error', function () {
    $this->post(route('logout'))->assertRedirect(route('login'));
});
