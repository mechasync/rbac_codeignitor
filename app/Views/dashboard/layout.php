<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col md:flex-row">

    <!-- Mobile Header -->
    <div class="md:hidden flex items-center justify-between bg-slate-900 border-b border-slate-800 px-4 py-3 w-full">
        <div class="flex items-center space-x-2">
            <div class="h-8 w-8 rounded-lg bg-gradient-to-tr from-indigo-500 to-violet-600 flex items-center justify-center">
                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>
            <span class="font-bold tracking-tight text-white">RBAC HUB</span>
        </div>
        <button id="mobile-menu-btn" class="text-slate-400 hover:text-white focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </button>
    </div>

    <!-- Sidebar navigation -->
    <aside id="sidebar" class="hidden md:flex flex-col w-full md:w-64 bg-slate-900 border-r border-slate-800 shrink-0">
        <!-- Logo -->
        <div class="hidden md:flex items-center space-x-3 px-6 py-5 border-b border-slate-800">
            <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-600 flex items-center justify-center shadow-md shadow-indigo-500/10">
                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>
            <span class="font-bold tracking-tight text-white text-lg">RBAC SECURITY</span>
        </div>

        <!-- Logged user status -->
        <div class="px-6 py-4 border-b border-slate-800 bg-slate-950/30">
            <p class="text-xs text-slate-500 font-medium">Logged in as</p>
            <p class="font-semibold text-white truncate"><?= esc($currentUser['name'] ?? 'Guest') ?></p>
            
            <!-- Badge based on role -->
            <?php 
                $role = $currentUser['role'] ?? 'User';
                $badgeClass = 'bg-slate-800 text-slate-400 border-slate-700';
                if ($role === 'Super Admin') {
                    $badgeClass = 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30';
                } elseif ($role === 'Manager') {
                    $badgeClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30';
                }
            ?>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border mt-1.5 <?= $badgeClass ?>">
                <?= esc($role) ?>
            </span>
        </div>

        <!-- Menu links -->
        <nav class="flex-1 px-4 py-4 space-y-1">
            <a href="/dashboard" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?= ($activeMenu === 'dashboard') ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>
                <span>Overview</span>
            </a>

            <!-- Only show menu links if the user has corresponding permissions -->
            <?php if ($store->hasPermission($currentUser['id'], 'manage_users')): ?>
                <a href="/dashboard/users" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?= ($activeMenu === 'users') ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Users</span>
                </a>
            <?php endif; ?>

            <?php if ($store->hasPermission($currentUser['id'], 'manage_roles')): ?>
                <a href="/dashboard/roles" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?= ($activeMenu === 'roles') ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <span>Roles</span>
                </a>
            <?php endif; ?>

            <?php if ($store->hasPermission($currentUser['id'], 'manage_permissions')): ?>
                <a href="/dashboard/permissions" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?= ($activeMenu === 'permissions') ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                    </svg>
                    <span>Permissions</span>
                </a>
            <?php endif; ?>
        </nav>

        <!-- Logout footer inside sidebar -->
        <div class="p-4 border-t border-slate-800">
            <a href="/logout" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium text-rose-400 hover:text-rose-300 hover:bg-rose-950/20 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Log Out</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col min-w-0">
        <!-- Top navigation header for desktop -->
        <header class="hidden md:flex items-center justify-between px-8 py-4 bg-slate-900 border-b border-slate-800">
            <div>
                <h1 class="text-xl font-bold text-white"><?= esc($title) ?></h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-sm font-semibold text-white"><?= esc($currentUser['name'] ?? 'Guest') ?></p>
                    <p class="text-xs text-slate-400"><?= esc($currentUser['email'] ?? '') ?></p>
                </div>
                <div class="h-10 w-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-indigo-400">
                    <?= esc(substr($currentUser['name'] ?? 'U', 0, 2)) ?>
                </div>
            </div>
        </header>

        <!-- Content space -->
        <div class="flex-1 p-4 md:p-8 overflow-y-auto">
            
            <!-- Toast alerts -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-6 flex items-center justify-between p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm">
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><?= esc(session()->getFlashdata('success')) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-6 flex items-center justify-between p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm">
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 shrink-0 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span><?= esc(session()->getFlashdata('error')) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Render the actual view content here -->
            <?= $this->renderSection('content') ?>

        </div>
    </main>

    <!-- Mobile Menu Script -->
    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        btn.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('flex');
            sidebar.classList.toggle('absolute');
            sidebar.classList.toggle('top-12');
            sidebar.classList.toggle('left-0');
            sidebar.classList.toggle('z-50');
        });
    </script>
</body>
</html>
