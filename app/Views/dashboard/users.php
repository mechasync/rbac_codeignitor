<?= $this->extend('dashboard/layout') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-400">View and manage system user accounts, statuses, and login roles.</p>
        </div>
        <button onclick="openAddUserModal()" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-600/10">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Add New User
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-left text-sm text-slate-300">
                <thead class="bg-slate-950/45 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Assigned Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Registered Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                No users found in mock storage.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr class="hover:bg-slate-800/25 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-indigo-400">
                                            <?= esc(substr($u['name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-white"><?= esc($u['name']) ?></p>
                                            <p class="text-xs text-slate-400"><?= esc($u['email']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                        $role = $u['role'];
                                        $badgeClass = 'bg-slate-800 text-slate-300 border-slate-700';
                                        if ($role === 'Super Admin') {
                                            $badgeClass = 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30';
                                        } elseif ($role === 'Manager') {
                                            $badgeClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30';
                                        }
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $badgeClass ?>">
                                        <?= esc($role) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center">
                                        <span class="h-2 w-2 rounded-full mr-2 <?= ($u['status'] === 'Active') ? 'bg-emerald-400 shadow-md shadow-emerald-400/50' : 'bg-rose-400 shadow-md shadow-rose-400/50' ?>"></span>
                                        <span class="text-xs font-medium text-slate-300"><?= esc($u['status']) ?></span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400">
                                    <?= esc($u['created_at']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium space-x-2">
                                    <!-- Edit Button (pre-fills the modal data) -->
                                    <button 
                                        onclick='openEditUserModal(<?= json_encode($u) ?>)'
                                        class="inline-flex items-center px-2.5 py-1.5 rounded bg-slate-800 text-indigo-400 hover:text-indigo-300 hover:bg-slate-700 border border-slate-700 transition-colors">
                                        Edit
                                    </button>

                                    <!-- Delete Button -->
                                    <?php if (session()->get('logged_user_id') != $u['id']): ?>
                                        <a href="/dashboard/users/delete/<?= $u['id'] ?>" 
                                           onclick="return confirm('Are you sure you want to delete this user? This will remove them from the mock session database.');"
                                           class="inline-flex items-center px-2.5 py-1.5 rounded bg-slate-800 text-rose-400 hover:text-rose-300 hover:bg-rose-950/30 border border-slate-700 transition-colors">
                                            Delete
                                        </a>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1.5 rounded bg-slate-800/40 text-slate-600 border border-slate-800 cursor-not-allowed" title="Cannot delete your logged-in account">
                                            Self
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit User Modal Backdrop -->
    <div id="user-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="relative bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md shadow-2xl p-6 md:p-8 transform transition-all">
            
            <!-- Close Modal -->
            <button onclick="closeUserModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Title -->
            <h3 id="modal-title" class="text-xl font-bold text-white mb-6">Add New User</h3>

            <!-- Form -->
            <form action="/dashboard/users/save" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="user-id">

                <div>
                    <label for="modal-name" class="block text-sm font-medium text-slate-300">Full Name</label>
                    <input type="text" name="name" id="modal-name" required placeholder="Alice Jones"
                        class="mt-1 block w-full px-3 py-2 border border-slate-700 rounded-lg bg-slate-850 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm bg-slate-800 transition-all">
                </div>

                <div>
                    <label for="modal-email" class="block text-sm font-medium text-slate-300">Email Address</label>
                    <input type="email" name="email" id="modal-email" required placeholder="alice@rbac.com"
                        class="mt-1 block w-full px-3 py-2 border border-slate-700 rounded-lg bg-slate-850 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm bg-slate-800 transition-all">
                </div>

                <div>
                    <label for="modal-password" class="block text-sm font-medium text-slate-300">
                        Password <span id="password-hint" class="text-xs text-slate-500 font-normal">(leave blank to keep current)</span>
                    </label>
                    <input type="password" name="password" id="modal-password" placeholder="••••••••"
                        class="mt-1 block w-full px-3 py-2 border border-slate-700 rounded-lg bg-slate-850 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm bg-slate-800 transition-all">
                </div>

                <div>
                    <label for="modal-role" class="block text-sm font-medium text-slate-300">Assign Role</label>
                    <select name="role" id="modal-role" required
                        class="mt-1 block w-full px-3 py-2 border border-slate-700 rounded-lg bg-slate-850 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm bg-slate-800 transition-all">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= esc($r['name']) ?>"><?= esc($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="modal-status" class="block text-sm font-medium text-slate-300">Account Status</label>
                    <select name="status" id="modal-status" required
                        class="mt-1 block w-full px-3 py-2 border border-slate-700 rounded-lg bg-slate-850 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm bg-slate-800 transition-all">
                        <option value="Active">Active</option>
                        <option value="Suspended">Suspended</option>
                    </select>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" onclick="closeUserModal()" class="px-4 py-2 border border-slate-700 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-sm font-semibold text-white rounded-lg shadow-lg shadow-indigo-600/15 transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- Modal Control Script -->
<script>
    const modal = document.getElementById('user-modal');
    const modalTitle = document.getElementById('modal-title');
    const userIdInput = document.getElementById('user-id');
    const nameInput = document.getElementById('modal-name');
    const emailInput = document.getElementById('modal-email');
    const passwordInput = document.getElementById('modal-password');
    const passwordHint = document.getElementById('password-hint');
    const roleSelect = document.getElementById('modal-role');
    const statusSelect = document.getElementById('modal-status');

    function openAddUserModal() {
        modalTitle.textContent = "Add New User";
        userIdInput.value = "";
        nameInput.value = "";
        emailInput.value = "";
        passwordInput.value = "";
        passwordInput.required = true;
        passwordHint.classList.add('hidden');
        roleSelect.value = "User";
        statusSelect.value = "Active";
        
        modal.classList.remove('hidden');
    }

    function openEditUserModal(user) {
        modalTitle.textContent = "Edit User Settings";
        userIdInput.value = user.id;
        nameInput.value = user.name;
        emailInput.value = user.email;
        passwordInput.value = "";
        passwordInput.required = false;
        passwordHint.classList.remove('hidden');
        roleSelect.value = user.role;
        statusSelect.value = user.status;

        modal.classList.remove('hidden');
    }

    function closeUserModal() {
        modal.classList.add('hidden');
    }
</script>
<?= $this->endSection() ?>
