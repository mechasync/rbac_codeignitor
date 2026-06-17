<?php

namespace App\Libraries;

/**
 * RBACStore
 * 
 * A relational database-backed store for Users, Roles, and Permissions
 * providing robust access control.
 */
class RBACStore
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Stub init function to maintain backward compatibility
     */
    public function init()
    {
        // No-op. Handled by database migrations and seeders.
    }

    // ==========================================
    // PERMISSIONS CRUD
    // ==========================================

    public function getPermissions()
    {
        return $this->db->table('permissions')
                        ->orderBy('category', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    public function getPermission($name)
    {
        return $this->db->table('permissions')
                        ->where('name', $name)
                        ->get()
                        ->getRowArray();
    }

    public function savePermission($data)
    {
        $existing = $this->getPermission($data['name']);
        if ($existing) {
            $updateData = [
                'description' => $data['description'] ?? '',
                'category'    => $data['category'] ?? 'General',
                'updated_at'  => date('Y-m-d H:i:s')
            ];
            return $this->db->table('permissions')
                            ->where('name', $data['name'])
                            ->update($updateData);
        } else {
            $insertData = [
                'name'        => $data['name'],
                'description' => $data['description'] ?? '',
                'category'    => $data['category'] ?? 'General',
                'is_system'   => $data['is_system'] ?? 0,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ];
            return $this->db->table('permissions')->insert($insertData);
        }
    }

    public function deletePermission($name)
    {
        $perm = $this->getPermission($name);
        if (!$perm || !empty($perm['is_system'])) {
            return false; // Prevent system rules deletion
        }
        return $this->db->table('permissions')
                        ->where('name', $name)
                        ->delete();
    }

    // ==========================================
    // ROLES CRUD
    // ==========================================

    public function getRoles()
    {
        $roles = $this->db->table('roles')
                         ->orderBy('name', 'ASC')
                         ->get()
                         ->getResultArray();

        foreach ($roles as &$role) {
            $role['permissions'] = $this->getRolePermissionNames($role['id']);
        }

        return $roles;
    }

    public function getRole($name)
    {
        $role = $this->db->table('roles')
                        ->where('name', $name)
                        ->get()
                        ->getRowArray();

        if ($role) {
            $role['permissions'] = $this->getRolePermissionNames($role['id']);
        }

        return $role;
    }

    protected function getRolePermissionNames($roleId)
    {
        $query = $this->db->table('role_permissions')
                         ->select('permissions.name')
                         ->join('permissions', 'role_permissions.permission_id = permissions.id')
                         ->where('role_permissions.role_id', $roleId)
                         ->get();

        $result = $query->getResultArray();
        return array_column($result, 'name');
    }

    public function saveRole($data)
    {
        $name = $data['name'];
        $description = $data['description'] ?? '';
        $permissions = $data['permissions'] ?? [];

        $existing = $this->db->table('roles')
                            ->where('name', $name)
                            ->get()
                            ->getRowArray();

        $this->db->transStart();

        if ($existing) {
            $roleId = $existing['id'];
            $this->db->table('roles')
                     ->where('id', $roleId)
                     ->update([
                         'description' => $description,
                         'updated_at'  => date('Y-m-d H:i:s')
                     ]);
        } else {
            $this->db->table('roles')->insert([
                'name'        => $name,
                'description' => $description,
                'is_system'   => $data['is_system'] ?? 0,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ]);
            $roleId = $this->db->insertID();
        }

        // Update permissions mapping
        $this->db->table('role_permissions')
                 ->where('role_id', $roleId)
                 ->delete();

        if (!empty($permissions)) {
            $permsData = $this->db->table('permissions')
                                 ->select('id')
                                 ->whereIn('name', $permissions)
                                 ->get()
                                 ->getResultArray();

            $insertData = [];
            foreach ($permsData as $perm) {
                $insertData[] = [
                    'role_id'       => $roleId,
                    'permission_id' => $perm['id']
                ];
            }

            if (!empty($insertData)) {
                $this->db->table('role_permissions')->insertBatch($insertData);
            }
        }

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    public function deleteRole($name)
    {
        $role = $this->getRole($name);
        if (!$role || !empty($role['is_system'])) {
            return false;
        }

        $this->db->transStart();

        $userRole = $this->db->table('roles')
                             ->where('name', 'User')
                             ->get()
                             ->getRowArray();
        $userRoleId = $userRole ? $userRole['id'] : null;

        $this->db->table('users')
                 ->where('role_id', $role['id'])
                 ->update(['role_id' => $userRoleId]);

        $this->db->table('roles')
                 ->where('id', $role['id'])
                 ->delete();

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    // ==========================================
    // USERS CRUD
    // ==========================================

    public function getUsers()
    {
        $result = $this->db->table('users')
                           ->select('users.*, roles.name as role')
                           ->join('roles', 'users.role_id = roles.id', 'left')
                           ->orderBy('users.id', 'ASC')
                           ->get()
                           ->getResultArray();

        foreach ($result as &$user) {
            if (empty($user['role'])) {
                $user['role'] = 'User';
            }
        }

        return $result;
    }

    public function getUser($id)
    {
        $user = $this->db->table('users')
                        ->select('users.*, roles.name as role')
                        ->join('roles', 'users.role_id = roles.id', 'left')
                        ->where('users.id', $id)
                        ->get()
                        ->getRowArray();

        if ($user && empty($user['role'])) {
            $user['role'] = 'User';
        }

        return $user;
    }

    public function getUserByEmail($email)
    {
        $user = $this->db->table('users')
                        ->select('users.*, roles.name as role')
                        ->join('roles', 'users.role_id = roles.id', 'left')
                        ->where('LOWER(users.email)', strtolower(trim($email)))
                        ->get()
                        ->getRowArray();

        if ($user && empty($user['role'])) {
            $user['role'] = 'User';
        }

        return $user;
    }

    public function saveUser($data)
    {
        $id = $data['id'] ?? null;

        $roleId = null;
        if (isset($data['role'])) {
            $role = $this->db->table('roles')
                             ->where('name', $data['role'])
                             ->get()
                             ->getRowArray();
            if ($role) {
                $roleId = $role['id'];
            }
        }

        $dbData = [];
        if (isset($data['name'])) $dbData['name'] = $data['name'];
        if (isset($data['email'])) $dbData['email'] = $data['email'];
        if ($roleId) $dbData['role_id'] = $roleId;
        if (isset($data['status'])) $dbData['status'] = $data['status'];

        if (isset($data['password']) && !empty($data['password'])) {
            $dbData['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } elseif (isset($data['password_hash'])) {
            $dbData['password_hash'] = $data['password_hash'];
        }

        if ($id) {
            $dbData['updated_at'] = date('Y-m-d H:i:s');
            return $this->db->table('users')
                            ->where('id', $id)
                            ->update($dbData);
        } else {
            if (!isset($dbData['password_hash'])) {
                $dbData['password_hash'] = password_hash($data['password'] ?? 'user123', PASSWORD_DEFAULT);
            }
            $dbData['created_at'] = date('Y-m-d H:i:s');
            $dbData['updated_at'] = date('Y-m-d H:i:s');
            $dbData['status'] = $dbData['status'] ?? 'Active';
            if (!isset($dbData['role_id'])) {
                $defaultRole = $this->db->table('roles')->where('name', 'User')->get()->getRowArray();
                $dbData['role_id'] = $defaultRole ? $defaultRole['id'] : null;
            }

            return $this->db->table('users')->insert($dbData);
        }
    }

    public function deleteUser($id)
    {
        if (session()->get('logged_user_id') == $id) {
            return false;
        }

        return $this->db->table('users')
                        ->where('id', $id)
                        ->delete();
    }

    // ==========================================
    // ACCESS CONTROL LOGIC
    // ==========================================

    public function hasPermission($userId, $permissionName)
    {
        $user = $this->getUser($userId);
        if (!$user) {
            return false;
        }

        $roleName = $user['role'];
        if ($roleName === 'Super Admin') {
            return true;
        }

        $role = $this->getRole($roleName);
        if (!$role) {
            return false;
        }

        return in_array($permissionName, $role['permissions']);
    }
}
