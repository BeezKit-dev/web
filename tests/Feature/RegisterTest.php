<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function registrationForm(array $overrides = []): array
{
    return array_merge([
        'name' => 'Aisyah Rahman',
        'shop_name' => 'Kedai Aisyah',
        'email' => 'aisyah@example.com',
        'password' => 'correct-horse-battery',
        'password_confirmation' => 'correct-horse-battery',
    ], $overrides);
}

it('shows the registration page to guests', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSeeLivewire('pages::register');

    expect(route('register', absolute: false))->toBe('/register');
});

it('redirects authenticated users away from the registration page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('register'))
        ->assertRedirect();
});

it('registers an owner with a new tenant and logs them in', function () {
    Livewire::test('pages::register')
        ->set(registrationForm())
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $user = User::where('email', 'aisyah@example.com')->firstOrFail();

    expect($user->tenant)->not->toBeNull()
        ->and($user->tenant->name)->toBe('Kedai Aisyah')
        ->and($user->tenant->slug)->toBe('kedai-aisyah')
        ->and(Hash::check('correct-horse-battery', $user->password))->toBeTrue()
        ->and(auth()->id())->toBe($user->id);
});

it('gives each tenant a unique slug when shop names collide', function () {
    Tenant::factory()->create(['slug' => 'kedai-aisyah']);

    Livewire::test('pages::register')
        ->set(registrationForm())
        ->call('register')
        ->assertHasNoErrors();

    $slug = User::firstWhere('email', 'aisyah@example.com')->tenant->slug;

    expect($slug)->toStartWith('kedai-aisyah-')->not->toBe('kedai-aisyah');
});

it('validates the registration form', function (array $overrides, string $field) {
    Livewire::test('pages::register')
        ->set(registrationForm($overrides))
        ->call('register')
        ->assertHasErrors($field);

    expect(User::count())->toBe(0)->and(Tenant::count())->toBe(0);
})->with([
    'missing name' => [['name' => ''], 'name'],
    'missing shop name' => [['shop_name' => ''], 'shop_name'],
    'invalid email' => [['email' => 'not-an-email'], 'email'],
    'short password' => [['password' => 'short', 'password_confirmation' => 'short'], 'password'],
    'password mismatch' => [['password_confirmation' => 'different-password'], 'password_confirmation'],
    'missing password confirmation' => [['password_confirmation' => ''], 'password_confirmation'],
]);

it('shows the registration page in English by default', function () {
    $this->get(route('register'))
        ->assertSee('Start with sales and add more as your business grows')
        ->assertSee('Shop or business name');
});

it('shows the registration page in Malay when the locale is ms', function () {
    app()->setLocale('ms');

    $this->get(route('register'))
        ->assertSee('Mulakan dengan jualan dan tambah ciri lain apabila perniagaan anda berkembang')
        ->assertSee('Nama kedai atau perniagaan')
        ->assertSee('Cipta akaun')
        ->assertDontSee('Start with sales and add more as your business grows');
});

it('stores the active language as the tenant locale', function () {
    app()->setLocale('ms');

    Livewire::test('pages::register')
        ->set(registrationForm())
        ->call('register');

    expect(User::firstWhere('email', 'aisyah@example.com')->tenant->locale)->toBe('ms');
});

it('shows validation errors with translated field names', function () {
    app()->setLocale('ms');

    Livewire::test('pages::register')
        ->set(registrationForm(['shop_name' => '']))
        ->call('register')
        ->assertHasErrors('shop_name')
        ->assertSee('Nama kedai atau perniagaan');
});

it('uses a language-appropriate shop name example', function () {
    $this->get(route('register'))->assertSee('e.g. Aisyah&#039;s Corner Shop', false);

    app()->setLocale('ms');

    $this->get(route('register'))->assertSee('cth. Kedai Runcit Aisyah');
});

it('leaves no tenant behind when creating the owner fails', function () {
    User::creating(fn () => throw new RuntimeException('Could not create the owner.'));

    try {
        expect(fn () => Livewire::test('pages::register')
            ->set(registrationForm())
            ->call('register'))->toThrow(RuntimeException::class, 'Could not create the owner.');
    } finally {
        User::flushEventListeners();
    }

    expect(Tenant::count())->toBe(0)
        ->and(User::count())->toBe(0);

    $this->assertGuest();
});
