<?php

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a tenant with Malaysian defaults', function () {
    $tenant = Tenant::factory()->create();

    expect($tenant->timezone)->toBe('Asia/Kuala_Lumpur')
        ->and($tenant->currency)->toBe('MYR')
        ->and($tenant->locale)->toBeIn(['en', 'ms']);
});

it('requires a unique slug', function () {
    Tenant::factory()->create(['slug' => 'demo-shop']);

    Tenant::factory()->create(['slug' => 'demo-shop']);
})->throws(QueryException::class);

it('soft deletes tenants', function () {
    $tenant = Tenant::factory()->create();

    $tenant->delete();

    expect(Tenant::count())->toBe(0)
        ->and(Tenant::withTrashed()->count())->toBe(1);
});

it('relates users to their tenant', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->forTenant($tenant)->create();

    expect($user->tenant->is($tenant))->toBeTrue()
        ->and($tenant->users->modelKeys())->toBe([$user->id]);
});

it('allows users without a tenant', function () {
    expect(User::factory()->create()->tenant)->toBeNull();
});

it('creates a tenant when none is given to the user factory', function () {
    $user = User::factory()->forTenant()->create();

    expect($user->tenant)->toBeInstanceOf(Tenant::class);
});

it('nulls the tenant on users when the tenant is force deleted', function () {
    $user = User::factory()->forTenant()->create();

    $user->tenant->forceDelete();

    expect($user->refresh()->tenant_id)->toBeNull();
});

it('seeds a demo tenant with an owner', function () {
    $this->seed(DatabaseSeeder::class);

    $tenant = Tenant::where('slug', 'demo-shop')->firstOrFail();

    expect($tenant->users)->toHaveCount(1)
        ->and($tenant->users->first()->email)->toBe('owner@example.com');
});
