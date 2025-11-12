<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Usuários
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Funções
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Pacientes
            'view patients',
            'create patients',
            'edit patients',
            'delete patients',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->assignPermissionsToRole('admin', [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view patients',
            'create patients',
            'edit patients',
            'delete patients',
        ]);

        $this->assignPermissionsToRole('receptionista', [
            'view patients',
            'create patients',
            'edit patients',
            'delete patients',
        ]);
    }

    private function assignPermissionsToRole(string $roleName, array $permissions): void
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        $role->syncPermissions($permissions);
    }
}
