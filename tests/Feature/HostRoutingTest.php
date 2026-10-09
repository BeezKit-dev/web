<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('serves the authentication pages on any host', function (string $host) {
    User::factory()->create();

    $this->get("{$host}/login")->assertOk();
    $this->get("{$host}/dashboard")->assertRedirect(route('login'));
})->with([
    'localhost with a port' => 'http://localhost:8000',
    'an IP address' => 'http://192.168.1.10:8000',
    'the app subdomain' => 'https://app.beezkit.local',
    'the root domain' => 'https://beezkit.local',
]);

it('serves the registration page on any host while registration is open', function (string $host) {
    $this->get("{$host}/register")->assertOk();
})->with([
    'localhost with a port' => 'http://localhost:8000',
    'an IP address' => 'http://192.168.1.10:8000',
    'the app subdomain' => 'https://app.beezkit.local',
]);

it('sends guests from the home page to the login page', function () {
    User::factory()->create();

    $this->get('/')->assertRedirect(route('login'));
});

it('sends signed-in users from the home page to the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertRedirect(route('dashboard'));
});

it('does not show the default Laravel welcome page', function () {
    $this->followingRedirects()->get('/')->assertDontSee('Laravel');
});
