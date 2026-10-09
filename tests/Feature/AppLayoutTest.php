<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->for(Tenant::factory(['name' => 'Kedai Aisyah']))->create([
        'name' => 'Aisyah Rahman',
        'email' => 'aisyah@example.com',
    ]);
});

it('shows the user, shop and e-mail in the app layout header', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Aisyah Rahman')
        ->assertSee('Kedai Aisyah')
        ->assertSee('aisyah@example.com')
        ->assertSee('<title>Dashboard · '.config('app.name').'</title>', false);
});

it('has a log out form in the header menu', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertSee('action="'.route('logout').'"', false)
        ->assertSee('Log out');
});

it('copes with a user that has no tenant', function () {
    $this->actingAs(User::factory()->create(['name' => 'No Shop']))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('No Shop');
});

it('links the logo to the dashboard', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertSee('href="'.route('dashboard').'"', false);
});

it('translates the dashboard into Malay', function () {
    app()->setLocale('ms');

    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertSee('Selamat datang, Aisyah Rahman')
        ->assertSee('Log keluar');
});

it('loads the Livewire scripts so the header menu works on a plain page', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertSee('/livewire', false)
        ->assertSee('x-data', false);
});
