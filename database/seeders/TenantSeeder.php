<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Demo Shop',
            'slug' => 'demo-shop',
            'locale' => 'en',
        ]);

        User::factory()->for($tenant)->create([
            'name' => 'Demo Owner',
            'email' => 'owner@example.com',
        ]);
    }
}
