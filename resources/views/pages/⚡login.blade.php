<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::guest')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /**
     * Set the page title in the active language.
     */
    public function rendering(View $view): void
    {
        $view->title(__('Log in'));
    }

    /**
     * Attempt to log the user in, then go to the intended page.
     */
    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [], [
            'email' => __('E-mail'),
            'password' => __('Password'),
        ]);

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        session()->regenerate();

        $this->redirectIntended(route('dashboard'), navigate: true);
    }

    /**
     * Stop the attempt when there have been too many failed logins.
     */
    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => RateLimiter::availableIn($this->throttleKey()),
            ]),
        ]);
    }

    /**
     * Get the rate limiting key, unique per email and IP address.
     */
    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
};
?>

<div>
    <h1 class="text-2xl font-extrabold tracking-tight">{{ __('Welcome back') }}</h1>
    <p class="mt-1 text-sm text-ink-700 dark:text-honey-50/70">{{ __('Log in to your BeezKit account.') }}</p>

    <form wire:submit="login" class="mt-6 space-y-4">
        <x-input name="email" :clears="['password']" :label="__('E-mail')" wire:model="email" type="email" autocomplete="username" autofocus required />
        <x-input name="password" :label="__('Password')" :clears="['email']" :shares-errors-with="['email']" wire:model="password" type="password" autocomplete="current-password" required />

        <label class="flex cursor-pointer items-center gap-2 text-sm font-medium">
            <input type="checkbox" wire:model="remember" class="checkbox">
            {{ __('Remember me') }}
        </label>

        <x-button wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __('Log in') }}</span>
            <span wire:loading>{{ __('Logging in…') }}</span>
        </x-button>
    </form>
</div>
