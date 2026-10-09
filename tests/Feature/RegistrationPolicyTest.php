<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function registrationPayload(): array
{
    return [
        'name' => 'Aisyah Rahman',
        'shop_name' => 'Kedai Aisyah',
        'email' => 'aisyah@example.com',
        'password' => 'correct-horse-battery',
        'password_confirmation' => 'correct-horse-battery',
    ];
}

it('allows registration while there are no users', function () {
    expect(User::registrationIsOpen())->toBeTrue();

    $this->get(route('register'))->assertOk();

    Livewire::test('pages::register')
        ->set(registrationPayload())
        ->call('register')
        ->assertRedirect(route('dashboard'));
});

it('closes registration once the first user exists', function () {
    Livewire::test('pages::register')
        ->set(registrationPayload())
        ->call('register');

    expect(User::registrationIsOpen())->toBeFalse();

    auth()->logout();

    $this->get(route('register'))->assertRedirect(route('login'));
});

it('redirects the registration page to login when a user already exists', function () {
    User::factory()->create();

    $this->get(route('register'))->assertRedirect(route('login'));
});

it('rejects a registration submitted after a user exists', function () {
    User::factory()->create();

    Livewire::test('pages::register')
        ->set(registrationPayload())
        ->call('register')
        ->assertForbidden();

    expect(User::count())->toBe(1)->and(Tenant::count())->toBe(0);
});

it('sends login visitors to registration while registration is open', function () {
    $this->get(route('login'))->assertRedirect(route('register'));

    User::factory()->create();

    $this->get(route('login'))->assertOk();
});
