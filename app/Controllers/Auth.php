<?php

namespace App\Controllers;

use App\Libraries\RBACStore;

class Auth extends BaseController
{
    protected $store;

    public function __construct()
    {
        $this->store = new RBACStore();
    }

    /**
     * Display Login Page or process Login request
     */
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->has('logged_user_id')) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            // Simple validation
            if (empty($email) || empty($password)) {
                session()->setFlashdata('error', 'Please fill in all fields.');
                return redirect()->back()->withInput();
            }

            $user = $this->store->getUserByEmail($email);

            if ($user) {
                if ($user['status'] !== 'Active') {
                    session()->setFlashdata('error', 'Your account is suspended. Please contact admin.');
                    return redirect()->back()->withInput();
                }

                if (password_verify($password, $user['password_hash'])) {
                    // Password match! Set session
                    session()->set([
                        'logged_user_id' => $user['id'],
                        'logged_user_name' => $user['name'],
                        'logged_user_email' => $user['email'],
                        'logged_user_role' => $user['role']
                    ]);

                    session()->setFlashdata('success', "Welcome back, {$user['name']}!");
                    return redirect()->to('/dashboard');
                }
            }

            session()->setFlashdata('error', 'Invalid email or password.');
            return redirect()->back()->withInput();
        }

        return view('auth/login');
    }

    /**
     * Display Register Page or process Register request
     */
    public function register()
    {
        if (session()->has('logged_user_id')) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            $name = $this->request->getPost('name');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $confirmPassword = $this->request->getPost('confirm_password');

            if (empty($name) || empty($email) || empty($password)) {
                session()->setFlashdata('error', 'Please fill in all required fields.');
                return redirect()->back()->withInput();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                session()->setFlashdata('error', 'Please enter a valid email address.');
                return redirect()->back()->withInput();
            }

            if (strlen($password) < 4) {
                session()->setFlashdata('error', 'Password must be at least 4 characters long.');
                return redirect()->back()->withInput();
            }

            if ($password !== $confirmPassword) {
                session()->setFlashdata('error', 'Passwords do not match.');
                return redirect()->back()->withInput();
            }

            // Check if email already exists
            if ($this->store->getUserByEmail($email)) {
                session()->setFlashdata('error', 'Email is already registered.');
                return redirect()->back()->withInput();
            }

            // Save new user
            $this->store->saveUser([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => 'User',
                'status' => 'Active'
            ]);

            session()->setFlashdata('success', 'Registration successful! You can now log in.');
            return redirect()->to('/login');
        }

        return view('auth/register');
    }

    /**
     * Password Reset Demo
     */
    public function reset()
    {
        if ($this->request->getMethod() === 'POST') {
            $email = $this->request->getPost('email');

            if (empty($email)) {
                session()->setFlashdata('error', 'Please enter your email.');
                return redirect()->back();
            }

            $user = $this->store->getUserByEmail($email);
            if ($user) {
                // For demo purpose, reset password to 'password123'
                $this->store->saveUser([
                    'id' => $user['id'],
                    'password' => 'password123'
                ]);

                session()->setFlashdata('success', 'Password reset successfully! Temporary password is "password123". Please log in.');
                return redirect()->to('/login');
            } else {
                session()->setFlashdata('error', 'No account found with that email address.');
                return redirect()->back();
            }
        }

        return view('auth/reset');
    }

    /**
     * Log out
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
