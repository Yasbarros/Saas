<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;
use Database\Seeders\PermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);

            $this->call(PermissionSeeder::class);

            //TODO: Criar users suporte que não são exibidos no front
            $user = User::factory()->create([
                'name' => 'Kayky Lopes',
                'email' => 'kayky@email.com',
                'password' => bcrypt('12345678'),
            ]);

            $user->assignRole('admin');

            $user->is_active = true;
            $user->save();

            // User::factory(100)->create();

            tenancy()->end();
        }
    }
}
