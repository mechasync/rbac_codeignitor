<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\RBACStore;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during normal execution.
     * However, when an abnormal state is encountered, the CodeIgniter
     * Response instance should be returned.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // 1. Check if user is logged in
        if (!$session->has('logged_user_id')) {
            $session->setFlashdata('error', 'Please log in to access this page.');
            return redirect()->to('/login');
        }

        $userId = $session->get('logged_user_id');
        $store = new RBACStore();
        
        // 2. Determine required permission based on the path
        $router = service('router');
        $controller = $router->controllerName();
        $method = $router->methodName();
        
        // Normalize namespaces and class name
        $controllerName = basename(str_replace('\\', '/', $controller));

        // We only enforce rbac checks on Dashboard controller.
        // Auth controller methods are public.
        if ($controllerName === 'Dashboard') {
            $permissionRequired = null;

            if (in_array($method, ['users', 'saveUser', 'deleteUser'])) {
                $permissionRequired = 'manage_users';
            } elseif (in_array($method, ['roles', 'saveRole', 'deleteRole'])) {
                $permissionRequired = 'manage_roles';
            } elseif (in_array($method, ['permissions', 'savePermission', 'deletePermission'])) {
                $permissionRequired = 'manage_permissions';
            } else {
                $permissionRequired = 'view_dashboard';
            }

            if ($permissionRequired && !$store->hasPermission($userId, $permissionRequired)) {
                // If it's an AJAX request or API endpoint, return JSON
                if ($request->isAJAX()) {
                    return service('response')
                        ->setStatusCode(403)
                        ->setJSON([
                            'status' => 'error',
                            'message' => 'Unauthorized. You do not have permission to perform this action.'
                        ]);
                }

                // Normal web page: check if they have view_dashboard permission.
                // If they don't even have view_dashboard permission, redirect to login
                if ($permissionRequired !== 'view_dashboard' && $store->hasPermission($userId, 'view_dashboard')) {
                    $session->setFlashdata('error', 'Access Denied: You do not have permission to access that section.');
                    return redirect()->to('/dashboard');
                } else {
                    // Log out completely or redirect to public page with warning
                    $session->destroy();
                    $session->setFlashdata('error', 'Access Denied: Your account does not have dashboard access.');
                    return redirect()->to('/login');
                }
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * before it is returned to the browser.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}
