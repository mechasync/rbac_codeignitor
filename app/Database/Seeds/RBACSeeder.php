<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RBACSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Disable foreign key checks to safely truncate
        $db->disableForeignKeyChecks();
        $db->table('users')->truncate();
        $db->table('role_permissions')->truncate();
        $db->table('permissions')->truncate();
        $db->table('roles')->truncate();
        $db->enableForeignKeyChecks();

        // 1. Seed Permissions
        $permissions = [
            [
                'id'          => 1,
                'name'        => 'view_dashboard',
                'description' => 'Can access the main dashboard overview',
                'category'    => 'Dashboard',
                'is_system'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 2,
                'name'        => 'manage_users',
                'description' => 'Can list, create, edit and delete users',
                'category'    => 'User Management',
                'is_system'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 3,
                'name'        => 'manage_roles',
                'description' => 'Can list, create, edit and delete roles',
                'category'    => 'Role Management',
                'is_system'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 4,
                'name'        => 'manage_permissions',
                'description' => 'Can list, create, edit and delete permission rules',
                'category'    => 'Permission Management',
                'is_system'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];
        $db->table('permissions')->insertBatch($permissions);

        // 2. Seed Roles
        $roles = [
            [
                'id'          => 1,
                'name'        => 'Super Admin',
                'description' => 'Unrestricted access to all resources and settings.',
                'is_system'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 2,
                'name'        => 'Manager',
                'description' => 'Can view metrics and manage team users.',
                'is_system'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 3,
                'name'        => 'User',
                'description' => 'Standard user account with read-only dashboard access.',
                'is_system'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];
        $db->table('roles')->insertBatch($roles);

        // 3. Seed Role Permissions (Junction)
        $rolePermissions = [
            ['role_id' => 1, 'permission_id' => 1],
            ['role_id' => 1, 'permission_id' => 2],
            ['role_id' => 1, 'permission_id' => 3],
            ['role_id' => 1, 'permission_id' => 4],
            ['role_id' => 2, 'permission_id' => 1],
            ['role_id' => 2, 'permission_id' => 2],
            ['role_id' => 3, 'permission_id' => 1],
        ];
        $db->table('role_permissions')->insertBatch($rolePermissions);

        // 4. Seed Users
        $users = [
            [
                'id'            => 1,
                'name'          => 'Alex Admin',
                'email'         => 'admin@rbac.com',
                'password_hash' => password_hash('admin', PASSWORD_DEFAULT),
                'role_id'       => 1,
                'status'        => 'Active',
                'created_at'    => '2026-06-01 09:00:00',
            ],
            [
                'id'            => 2,
                'name'          => 'Morgan Manager',
                'email'         => 'manager@rbac.com',
                'password_hash' => password_hash('manager', PASSWORD_DEFAULT),
                'role_id'       => 2,
                'status'        => 'Active',
                'created_at'    => '2026-06-05 10:30:00',
            ],
            [
                'id'            => 3,
                'name'          => 'Sam User',
                'email'         => 'user@rbac.com',
                'password_hash' => password_hash('user', PASSWORD_DEFAULT),
                'role_id'       => 3,
                'status'        => 'Active',
                'created_at'    => '2026-06-10 14:15:00',
            ],
        ];
        $db->table('users')->insertBatch($users);
    }
}
