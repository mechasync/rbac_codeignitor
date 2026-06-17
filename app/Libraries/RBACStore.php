<?php

namespace App\Libraries;

/**
 * RBACStore
 * 
 * A session-based mock database store for Users, Roles, and Permissions
 * to allow fully functional CRUD and access checks without DB installation.
 */
class RBACStore
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
        $this->init();
    }

    /**
     * Initialise default seeds if they don't exist in session
     */
    public function init()
    {
        // 1. Initialise Permissions
        if (!$this->session->has('rbac_permissions')) {
            $defaultPermissions = [
                [
                    'name' => 'view_dashboard',
                    'description' => 'Can access the main dashboard overview',
                    'category' => 'Dashboard',
                    'is_system' => true
                ],
                [
                    'name' => 'manage_users',
                    'description' => 'Can list, create, edit and delete users',
                    'category' => 'User Management',
                    'is_system' => true
                ],
                [
                    'name' => 'manage_roles',
                    'description' => 'Can list, create, edit and delete roles',
                    'category' => 'Role Management',
                    'is_system' => true
                ],
                [
                    'name' => 'manage_permissions',
                    'description' => 'Can list, create, edit and delete permission rules',
                    'category' => 'Permission Management',
                    'is_system' => true
                ]
            ];
            $this->session->set('rbac_permissions', $defaultPermissions);
        }

        // 2. Initialise Roles
        if (!$this->session->has('rbac_roles')) {
            $defaultRoles = [
                [
                    'name' => 'Super Admin',
                    'description' => 'Unrestricted access to all resources and settings.',
                    'permissions' => ['view_dashboard', 'manage_users', 'manage_roles', 'manage_permissions'],
                    'is_system' => true
                ],
                [
                    'name' => 'Manager',
                    'description' => 'Can view metrics and manage team users.',
                    'permissions' => ['view_dashboard', 'manage_users'],
                    'is_system' => true
                ],
                [
                    'name' => 'User',
                    'description' => 'Standard user account with read-only dashboard access.',
                    'permissions' => ['view_dashboard'],
                    'is_system' => true
                ]
            ];
            $this->session->set('rbac_roles', $defaultRoles);
        }

        // 3. Initialise Users
        if (!$this->session->has('rbac_users')) {
            $defaultUsers = [
                [
                    'id' => 1,
                    'name' => 'Alex Admin',
                    'email' => 'admin@rbac.com',
                    'password_hash' => password_hash('admin', PASSWORD_DEFAULT),
                    'role' => 'Super Admin',
                    'status' => 'Active',
                    'created_at' => '2026-06-01 09:00:00'
                ],
                [
                    'id' => 2,
                    'name' => 'Morgan Manager',
                    'email' => 'manager@rbac.com',
                    'password_hash' => password_hash('manager', PASSWORD_DEFAULT),
                    'role' => 'Manager',
                    'status' => 'Active',
                    'created_at' => '2026-06-05 10:30:00'
                ],
                [
                    'id' => 3,
                    'name' => 'Sam User',
                    'email' => 'user@rbac.com',
                    'password_hash' => password_hash('user', PASSWORD_DEFAULT),
                    'role' => 'User',
                    'status' => 'Active',
                    'created_at' => '2026-06-10 14:15:00'
                ]
            ];
            $this->session->set('rbac_users', $defaultUsers);
        }
    }

    // ==========================================
    // PERMISSIONS CRUD
    // ==========================================

    public function getPermissions()
    {
        return $this->session->get('rbac_permissions') ?? [];
    }

    public function getPermission($name)
    {
        $perms = $this->getPermissions();
        foreach ($perms as $perm) {
            if ($perm['name'] === $name) {
                return $perm;
            }
        }
        return null;
    }

    public function savePermission($data)
    {
        $perms = $this->getPermissions();
        $exists = false;

        foreach ($perms as $key => $perm) {
            if ($perm['name'] === $data['name']) {
                $perms[$key] = array_merge($perm, $data);
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $data['is_system'] = $data['is_system'] ?? false;
            $perms[] = $data;
        }

        $this->session->set('rbac_permissions', $perms);
        return true;
    }

    public function deletePermission($name)
    {
        $perms = $this->getPermissions();
        foreach ($perms as $key => $perm) {
            if ($perm['name'] === $name) {
                if (!empty($perm['is_system'])) {
                    return false; // Prevent system rules deletion
                }
                unset($perms[$key]);
                $this->session->set('rbac_permissions', array_values($perms));

                // Also remove this permission from any roles
                $this->removePermissionFromRoles($name);
                return true;
            }
        }
        return false;
    }

    protected function removePermissionFromRoles($permName)
    {
        $roles = $this->getRoles();
        $updated = false;
        foreach ($roles as $key => $role) {
            if (($idx = array_search($permName, $role['permissions'])) !== false) {
                unset($roles[$key]['permissions'][$idx]);
                $roles[$key]['permissions'] = array_values($roles[$key]['permissions']);
                $updated = true;
            }
        }
        if ($updated) {
            $this->session->set('rbac_roles', $roles);
        }
    }

    // ==========================================
    // ROLES CRUD
    // ==========================================

    public function getRoles()
    {
        return $this->session->get('rbac_roles') ?? [];
    }

    public function getRole($name)
    {
        $roles = $this->getRoles();
        foreach ($roles as $role) {
            if ($role['name'] === $name) {
                return $role;
            }
        }
        return null;
    }

    public function saveRole($data)
    {
        $roles = $this->getRoles();
        $exists = false;

        foreach ($roles as $key => $role) {
            if ($role['name'] === $data['name']) {
                $roles[$key] = array_merge($role, $data);
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $data['is_system'] = $data['is_system'] ?? false;
            $data['permissions'] = $data['permissions'] ?? [];
            $roles[] = $data;
        }

        $this->session->set('rbac_roles', $roles);
        return true;
    }

    public function deleteRole($name)
    {
        $roles = $this->getRoles();
        foreach ($roles as $key => $role) {
            if ($role['name'] === $name) {
                if (!empty($role['is_system'])) {
                    return false; // Prevent system role deletion
                }
                unset($roles[$key]);
                $this->session->set('rbac_roles', array_values($roles));

                // Re-assign users with this role to 'User' default role
                $this->reassignUsersRole($name, 'User');
                return true;
            }
        }
        return false;
    }

    protected function reassignUsersRole($oldRole, $newRole)
    {
        $users = $this->getUsers();
        $updated = false;
        foreach ($users as $key => $user) {
            if ($user['role'] === $oldRole) {
                $users[$key]['role'] = $newRole;
                $updated = true;
            }
        }
        if ($updated) {
            $this->session->set('rbac_users', $users);
        }
    }

    // ==========================================
    // USERS CRUD
    // ==========================================

    public function getUsers()
    {
        return $this->session->get('rbac_users') ?? [];
    }

    public function getUser($id)
    {
        $users = $this->getUsers();
        foreach ($users as $user) {
            if ((int)$user['id'] === (int)$id) {
                return $user;
            }
        }
        return null;
    }

    public function getUserByEmail($email)
    {
        $users = $this->getUsers();
        foreach ($users as $user) {
            if (strtolower($user['email']) === strtolower(trim($email))) {
                return $user;
            }
        }
        return null;
    }

    public function saveUser($data)
    {
        $users = $this->getUsers();
        $id = $data['id'] ?? null;

        if ($id) {
            // Update
            foreach ($users as $key => $user) {
                if ((int)$user['id'] === (int)$id) {
                    if (isset($data['password']) && !empty($data['password'])) {
                        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
                        unset($data['password']);
                    }
                    $users[$key] = array_merge($user, $data);
                    $this->session->set('rbac_users', $users);
                    return true;
                }
            }
        } else {
            // Create
            $maxId = 0;
            foreach ($users as $user) {
                if ($user['id'] > $maxId) {
                    $maxId = $user['id'];
                }
            }
            $data['id'] = $maxId + 1;
            $data['password_hash'] = password_hash($data['password'] ?? 'user123', PASSWORD_DEFAULT);
            unset($data['password']);
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['status'] = $data['status'] ?? 'Active';
            $data['role'] = $data['role'] ?? 'User';
            $users[] = $data;
            $this->session->set('rbac_users', $users);
            return true;
        }

        return false;
    }

    public function deleteUser($id)
    {
        $users = $this->getUsers();
        foreach ($users as $key => $user) {
            if ((int)$user['id'] === (int)$id) {
                // Prevent deleting own self
                if ($this->session->get('logged_user_id') == $id) {
                    return false;
                }
                unset($users[$key]);
                $this->session->set('rbac_users', array_values($users));
                return true;
            }
        }
        return false;
    }

    // ==========================================
    // ACCESS CONTROL LOGIC
    // ==========================================

    /**
     * Checks if a user has a specific permission
     */
    public function hasPermission($userId, $permissionName)
    {
        $user = $this->getUser($userId);
        if (!$user) {
            return false;
        }

        $roleName = $user['role'];
        $role = $this->getRole($roleName);
        if (!$role) {
            return false;
        }

        // Super Admin has unrestricted access to everything
        if ($role['name'] === 'Super Admin') {
            return true;
        }

        return in_array($permissionName, $role['permissions']);
    }
}
