<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::guest')] class extends Component
{
    public string $name = '';

    public string $shop_name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Set the page title in the active language.
     */
    public function rendering(View $view): void
    {
        $view->title(__('Create your account'));
    }

    /**
     * Create the tenant and its owner, log the owner in and go to the dashboard.
     */
    public function register(): void
    {
        abort_unless(User::registrationIsOpen(), 403);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'shop_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
        ], [], [
            'name' => __('Full Name'),
            'shop_name' => __('Shop or business name'),
            'email' => __('E-mail'),
            'password' => __('Password'),
            'password_confirmation' => __('Confirm password'),
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $tenant = Tenant::create([
                'name' => $validated['shop_name'],
                'slug' => $this->uniqueSlug($validated['shop_name']),
                'locale' => app()->getLocale(),
            ]);

            return $tenant->users()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);
        });

        Auth::login($user);

        session()->regenerate();

        $this->redirectIntended(route('dashboard'), navigate: true);
    }

    /**
     * Build a tenant slug from the shop name that is not taken yet.
     */
    private function uniqueSlug(string $shopName): string
    {
        $base = Str::slug($shopName) ?: 'shop';
        $slug = $base;

        while (Tenant::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.Str::lower(Str::random(4));
        }

        return $slug;
    }
};
?>

<div>
    <h1 class="text-2xl font-extrabold tracking-tight">{{ __('Create your account') }}</h1>
    <p class="mt-1 text-sm text-ink-700 dark:text-honey-50/70">{{ __('Start with sales and add more as your business grows.') }}</p>

    <form wire:submit="register" class="mt-6 space-y-4">
        <x-input name="name" :label="__('Your name')" wire:model="name" type="text" :placeholder="__('Full Name')" autocomplete="name" autofocus required />
        <x-input name="shop_name" :label="__('Shop or business name')" wire:model="shop_name" type="text" :placeholder="__('e.g. Aisyah\'s Corner Shop')" autocomplete="organization" required />
        <x-input name="email" :label="__('E-mail')" wire:model="email" type="email" autocomplete="email" required />
        <x-input name="password" :label="__('Password')" :clears="['password_confirmation']" wire:model="password" type="password" autocomplete="new-password" required />
        <x-input name="password_confirmation" :label="__('Confirm password')" wire:model="password_confirmation" type="password" autocomplete="new-password" required />

        <x-button wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __('Create account') }}</span>
            <span wire:loading>{{ __('Creating…') }}</span>
        </x-button>
    </form>
</div>
