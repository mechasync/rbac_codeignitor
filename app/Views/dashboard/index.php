<?= $this->extend('dashboard/layout') ?>

<?= $this->section('content') ?>
<div class="space-y-8">
    
    <!-- Hero / Welcome Panel -->
    <div class="relative bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800 rounded-2xl p-6 md:p-8 overflow-hidden shadow-xl">
        <div class="relative z-10 max-w-2xl">
            <h2 class="text-2xl md:text-3xl font-extrabold text-white">RBAC Administration Console</h2>
            <p class="mt-2 text-sm md:text-base text-slate-300">
                You are currently running with a production-ready relational database store. All creations, modifications, and deletions are synchronized directly with your MySQL database.
            </p>
            <div class="mt-4 flex space-x-3 text-xs md:text-sm">
                <span class="inline-flex items-center px-3 py-1 bg-slate-800 rounded-full text-slate-300 font-medium border border-slate-700">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                    MySQL Database Active
                </span>
                <span class="inline-flex items-center px-3 py-1 bg-slate-800 rounded-full text-slate-300 font-medium border border-slate-700">
                    CI Version: <?= esc(\CodeIgniter\CodeIgniter::CI_VERSION) ?>
                </span>
            </div>
        </div>
        <!-- Decorative abstract shape in the background -->
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Users Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex items-center justify-between shadow-lg">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Users</p>
                <p class="text-3xl font-bold text-white"><?= esc($stats['users_count']) ?></p>
            </div>
            <div class="p-4 rounded-xl bg-indigo-500/10 text-indigo-400">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>

        <!-- Roles Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex items-center justify-between shadow-lg">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Configured Roles</p>
                <p class="text-3xl font-bold text-white"><?= esc($stats['roles_count']) ?></p>
            </div>
            <div class="p-4 rounded-xl bg-emerald-500/10 text-emerald-400">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
            </div>
        </div>

        <!-- Permissions Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex items-center justify-between shadow-lg">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Permissions</p>
                <p class="text-3xl font-bold text-white"><?= esc($stats['permissions_count']) ?></p>
            </div>
            <div class="p-4 rounded-xl bg-amber-500/10 text-amber-400">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- System Status Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex items-center justify-between shadow-lg">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Security Level</p>
                <p class="text-3xl font-bold text-sky-400">Strict</p>
            </div>
            <div class="p-4 rounded-xl bg-sky-500/10 text-sky-400">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Detailed Section Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- User Accounts Overview -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-800 flex justify-between items-center bg-slate-950/25">
                <h3 class="text-lg font-bold text-white">Users Overview</h3>
                <?php if ($store->hasPermission($currentUser['id'], 'manage_users')): ?>
                    <a href="/dashboard/users" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">Manage Users →</a>
                <?php endif; ?>
            </div>
            <div class="divide-y divide-slate-800">
                <?php foreach (array_slice($users, 0, 4) as $u): ?>
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="h-9 w-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-sm text-slate-300">
                                <?= esc(substr($u['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white"><?= esc($u['name']) ?></p>
                                <p class="text-xs text-slate-400"><?= esc($u['email']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">
                                <?= esc($u['role']) ?>
                            </span>
                            <span class="h-2 w-2 rounded-full <?= ($u['status'] === 'Active') ? 'bg-emerald-400' : 'bg-rose-400' ?>"></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- System Roles & assigned permissions -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-800 flex justify-between items-center bg-slate-950/25">
                <h3 class="text-lg font-bold text-white">Configured Roles & Access</h3>
                <?php if ($store->hasPermission($currentUser['id'], 'manage_roles')): ?>
                    <a href="/dashboard/roles" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">Manage Roles →</a>
                <?php endif; ?>
            </div>
            <div class="divide-y divide-slate-800">
                <?php foreach ($roles as $r): ?>
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-white"><?= esc($r['name']) ?></h4>
                            <span class="text-xs text-slate-400"><?= count($r['permissions']) ?> permissions assigned</span>
                        </div>
                        <p class="text-xs text-slate-400"><?= esc($r['description']) ?></p>
                        <div class="flex flex-wrap gap-1 pt-1">
                            <?php foreach ($r['permissions'] as $p): ?>
                                <span class="inline-block text-[10px] px-2 py-0.5 bg-indigo-950/40 text-indigo-300 border border-indigo-900/40 rounded-md">
                                    <?= esc($p) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

</div>
<?= $this->endSection() ?>
