<?php

namespace App\Controllers;

use App\Libraries\RBACStore;

class Dashboard extends BaseController
{
    protected $store;
    protected $userId;
    protected $userRole;

    public function __construct()
    {
        $this->store = new RBACStore();
    }

    /**
     * Helper to load shared view data (like logged user info)
     */
    protected function getSharedData($title = 'Dashboard')
    {
        $userId = session()->get('logged_user_id');
        $user = $this->store->getUser($userId);
        
        return [
            'title' => $title,
            'currentUser' => $user,
            'store' => $this->store,
            'activeSegment' => service('uri')->getSegment(1) . '/' . service('uri')->getSegment(2)
        ];
    }

    /**
     * Overview / Metrics page
     */
    public function index()
    {
        $data = $this->getSharedData('Overview - RBAC Dashboard');
        $data['activeMenu'] = 'dashboard';
        
        // Fetch stats
        $data['stats'] = [
            'users_count' => count($this->store->getUsers()),
            'roles_count' => count($this->store->getRoles()),
            'permissions_count' => count($this->store->getPermissions()),
            'active_sessions' => 1 + rand(1, 3) // Mock visual metric
        ];

        $data['users'] = $this->store->getUsers();
        $data['roles'] = $this->store->getRoles();
        $data['permissions'] = $this->store->getPermissions();

        return view('dashboard/index', $data);
    }

    /**
     * Users list & management
     */
    public function users()
    {
        $data = $this->getSharedData('Users Management');
        $data['activeMenu'] = 'users';
        $data['users'] = $this->store->getUsers();
        $data['roles'] = $this->store->getRoles();

        return view('dashboard/users', $data);
    }

    /**
     * Create or Edit User
     */
    public function saveUser()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/dashboard/users');
        }

        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $role = $this->request->getPost('role');
        $status = $this->request->getPost('status');

        if (empty($name) || empty($email)) {
            session()->setFlashdata('error', 'Name and Email are required.');
            return redirect()->back()->withInput();
        }

        // Validate email uniqueness if new or changed
        $existing = $this->store->getUserByEmail($email);
        if ($existing && (!$id || (int)$existing['id'] !== (int)$id)) {
            session()->setFlashdata('error', 'A user with this email already exists.');
            return redirect()->back()->withInput();
        }

        $userData = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'status' => $status
        ];

        if ($id) {
            $userData['id'] = $id;
        }

        if (!empty($password)) {
            $userData['password'] = $password;
        }

        $this->store->saveUser($userData);
        session()->setFlashdata('success', $id ? 'User updated successfully.' : 'User created successfully.');
        return redirect()->to('/dashboard/users');
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        if (session()->get('logged_user_id') == $id) {
            session()->setFlashdata('error', 'You cannot delete your own logged-in account.');
            return redirect()->to('/dashboard/users');
        }

        if ($this->store->deleteUser($id)) {
            session()->setFlashdata('success', 'User deleted successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to delete user.');
        }

        return redirect()->to('/dashboard/users');
    }

    /**
     * Roles list & management
     */
    public function roles()
    {
        $data = $this->getSharedData('Roles Management');
        $data['activeMenu'] = 'roles';
        $data['roles'] = $this->store->getRoles();
        $data['permissions'] = $this->store->getPermissions();

        return view('dashboard/roles', $data);
    }

    /**
     * Create or Edit Role
     */
    public function saveRole()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/dashboard/roles');
        }

        $name = trim($this->request->getPost('name'));
        $description = trim($this->request->getPost('description'));
        $permsInput = $this->request->getPost('permissions') ?? [];

        if (empty($name)) {
            session()->setFlashdata('error', 'Role Name is required.');
            return redirect()->back()->withInput();
        }

        $roleData = [
            'name' => $name,
            'description' => $description,
            'permissions' => $permsInput
        ];

        $this->store->saveRole($roleData);
        session()->setFlashdata('success', 'Role saved successfully.');
        return redirect()->to('/dashboard/roles');
    }

    /**
     * Delete Role
     */
    public function deleteRole($name)
    {
        $role = $this->store->getRole($name);
        if ($role && !empty($role['is_system'])) {
            session()->setFlashdata('error', 'System roles cannot be deleted.');
            return redirect()->to('/dashboard/roles');
        }

        if ($this->store->deleteRole($name)) {
            session()->setFlashdata('success', 'Role deleted successfully. Users with this role have been reassigned to "User".');
        } else {
            session()->setFlashdata('error', 'Failed to delete role.');
        }

        return redirect()->to('/dashboard/roles');
    }

    /**
     * Permissions list & management
     */
    public function permissions()
    {
        $data = $this->getSharedData('Permissions Management');
        $data['activeMenu'] = 'permissions';
        $data['permissions'] = $this->store->getPermissions();

        return view('dashboard/permissions', $data);
    }

    /**
     * Create or Edit Permission
     */
    public function savePermission()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/dashboard/permissions');
        }

        $name = strtolower(str_replace(' ', '_', trim($this->request->getPost('name'))));
        $description = trim($this->request->getPost('description'));
        $category = trim($this->request->getPost('category')) ?: 'General';

        if (empty($name)) {
            session()->setFlashdata('error', 'Permission Name is required.');
            return redirect()->back()->withInput();
        }

        $permData = [
            'name' => $name,
            'description' => $description,
            'category' => $category
        ];

        $this->store->savePermission($permData);
        session()->setFlashdata('success', 'Permission saved successfully.');
        return redirect()->to('/dashboard/permissions');
    }

    /**
     * Delete Permission
     */
    public function deletePermission($name)
    {
        $perm = $this->store->getPermission($name);
        if ($perm && !empty($perm['is_system'])) {
            session()->setFlashdata('error', 'System permissions cannot be deleted.');
            return redirect()->to('/dashboard/permissions');
        }

        if ($this->store->deletePermission($name)) {
            session()->setFlashdata('success', 'Permission deleted successfully and removed from all assigned roles.');
        } else {
            session()->setFlashdata('error', 'Failed to delete permission.');
        }

        return redirect()->to('/dashboard/permissions');
    }
}
