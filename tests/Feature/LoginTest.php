<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->for(Tenant::factory())->create([
        'email' => 'aisyah@example.com',
        'password' => 'correct-horse-battery',
    ]);
});

it('shows the login page to guests at /login', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSeeLivewire('pages::login')
        ->assertSee('Welcome back');

    expect(route('login', absolute: false))->toBe('/login');
});

it('redirects authenticated users away from the login page', function () {
    $this->actingAs($this->user)->get(route('login'))->assertRedirect();
});

it('logs in with the right credentials and goes to the dashboard', function () {
    Livewire::test('pages::login')
        ->set('email', 'aisyah@example.com')
        ->set('password', 'correct-horse-battery')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($this->user);
});

it('goes to the page the guest first asked for', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));

    Livewire::test('pages::login')
        ->set('email', 'aisyah@example.com')
        ->set('password', 'correct-horse-battery')
        ->call('login')
        ->assertRedirect(route('dashboard'));
});

it('remembers the user when asked to', function () {
    Livewire::test('pages::login')
        ->set('email', 'aisyah@example.com')
        ->set('password', 'correct-horse-battery')
        ->set('remember', true)
        ->call('login');

    expect($this->user->fresh()->remember_token)->not->toBeEmpty();
});

it('shows the same generic error for a wrong password and an unknown email', function (string $email, string $password) {
    Livewire::test('pages::login')
        ->set('email', $email)
        ->set('password', $password)
        ->call('login')
        ->assertHasErrors(['email' => __('auth.failed')])
        ->assertNoRedirect();

    $this->assertGuest();
})->with([
    'wrong password' => ['aisyah@example.com', 'wrong-password'],
    'unknown email' => ['nobody@example.com', 'correct-horse-battery'],
]);

it('validates the login form', function (string $email, string $password, string $field) {
    Livewire::test('pages::login')
        ->set('email', $email)
        ->set('password', $password)
        ->call('login')
        ->assertHasErrors($field);
})->with([
    'missing email' => ['', 'secret', 'email'],
    'invalid email' => ['not-an-email', 'secret', 'email'],
    'missing password' => ['aisyah@example.com', '', 'password'],
]);

it('throttles after five failed attempts', function () {
    Event::fake([Lockout::class]);

    $component = Livewire::test('pages::login')
        ->set('email', 'aisyah@example.com')
        ->set('password', 'wrong-password');

    foreach (range(1, 5) as $attempt) {
        $component->call('login')->assertHasErrors(['email' => __('auth.failed')]);
    }

    $component
        ->set('password', 'correct-horse-battery')
        ->call('login')
        ->assertHasErrors('email');

    expect(collect($component->errors()->get('email'))->first())->toContain('Too many login attempts');

    $this->assertGuest();
    Event::assertDispatched(Lockout::class);
});

it('clears the failed attempts after a successful login', function () {
    Livewire::test('pages::login')
        ->set('email', 'aisyah@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->set('password', 'correct-horse-battery')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    expect(RateLimiter::attempts('aisyah@example.com|127.0.0.1'))->toBe(0);
});

it('shows the login page in Malay when the locale is ms', function () {
    app()->setLocale('ms');

    $this->get(route('login'))
        ->assertSee('Selamat kembali')
        ->assertSee('Ingat saya')
        ->assertDontSee('Welcome back');
});

it('highlights both fields but shows one message when the credentials are wrong', function () {
    $html = Livewire::test('pages::login')
        ->set('email', 'aisyah@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->html();

    expect(preg_match_all('/\sdata-invalid(?=[\s>])/', $html))->toBe(2)
        ->and(preg_match_all('/\sdata-error-for="/', $html))->toBe(1);
});

it('redirects the login page to registration when nobody has registered yet', function () {
    User::query()->delete();

    $this->get(route('login'))->assertRedirect(route('register'));
});

it('does not show a registration link on the login page', function () {
    $this->get(route('login'))->assertOk()->assertDontSee(route('register'), false);
});
