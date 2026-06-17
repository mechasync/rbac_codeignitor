<?= $this->extend('dashboard/layout') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-400">Define access roles and map precise system permissions to them.</p>
        </div>
        <button onclick="openAddRoleModal()" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-600/10">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Add New Role
        </button>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($roles as $r): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex flex-col justify-between shadow-xl space-y-4">
                <div class="space-y-2">
                    <div class="flex justify-between items-start">
                        <h3 class="text-lg font-bold text-white"><?= esc($r['name']) ?></h3>
                        <?php if (!empty($r['is_system'])): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-950/80 text-indigo-300 border border-indigo-900/40 uppercase tracking-wider">
                                System
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed"><?= esc($r['description']) ?></p>
                </div>

                <!-- Assigned Permissions Section -->
                <div>
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Assigned Rules (<?= count($r['permissions']) ?>)</h4>
                    <div class="flex flex-wrap gap-1">
                        <?php if (empty($r['permissions'])): ?>
                            <span class="text-xs text-slate-600 italic">No permissions assigned.</span>
                        <?php else: ?>
                            <?php foreach ($r['permissions'] as $p): ?>
                                <span class="text-[10px] px-2 py-0.5 bg-slate-800 text-slate-300 border border-slate-700/60 rounded">
                                    <?= esc($p) ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card Action Bar -->
                <div class="pt-4 border-t border-slate-800/80 flex justify-between items-center text-xs font-semibold">
                    <button 
                        onclick='openEditRoleModal(<?= json_encode($r) ?>)'
                        class="text-indigo-400 hover:text-indigo-300 transition-colors">
                        Edit Settings & Mapping
                    </button>
                    
                    <?php if (empty($r['is_system'])): ?>
                        <a href="/dashboard/roles/delete/<?= urlencode($r['name']) ?>"
                           onclick="return confirm('Are you sure you want to delete the role \'<?= esc($r['name']) ?>\'? Associated users will revert to the default \'User\' role.');"
                           class="text-rose-400 hover:text-rose-300 transition-colors">
                            Delete Role
                        </a>
                    <?php else: ?>
                        <span class="text-slate-600 cursor-not-allowed" title="System protection lock">System Lock</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Role Modal -->
    <div id="role-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="relative bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg shadow-2xl p-6 md:p-8 transform transition-all">
            
            <!-- Close Modal -->
            <button onclick="closeRoleModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Title -->
            <h3 id="modal-title" class="text-xl font-bold text-white mb-6">Add New Role</h3>

            <!-- Form -->
            <form action="/dashboard/roles/save" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                
                <div>
                    <label for="modal-name" class="block text-sm font-medium text-slate-300">Role Name</label>
                    <input type="text" name="name" id="modal-name" required placeholder="Staff Member"
                        class="mt-1 block w-full px-3 py-2 border border-slate-700 rounded-lg bg-slate-800 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm transition-all">
                    <p id="modal-name-warning" class="hidden text-xs text-amber-400 mt-1">Note: You are editing a system role. Changing the name will create a new role.</p>
                </div>

                <div>
                    <label for="modal-description" class="block text-sm font-medium text-slate-300">Description</label>
                    <textarea name="description" id="modal-description" required rows="2" placeholder="Describe what users of this role can do..."
                        class="mt-1 block w-full px-3 py-2 border border-slate-700 rounded-lg bg-slate-800 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm transition-all"></textarea>
                </div>

                <!-- Checkbox matrix for permissions -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Permission Mappings</label>
                    <div class="bg-slate-950/40 border border-slate-800 rounded-xl p-4 max-h-48 overflow-y-auto space-y-2.5">
                        <?php foreach ($permissions as $p): ?>
                            <label class="flex items-start cursor-pointer group">
                                <input type="checkbox" name="permissions[]" value="<?= esc($p['name']) ?>" id="perm-<?= esc($p['name']) ?>"
                                    class="h-4 w-4 mt-0.5 rounded border-slate-700 text-indigo-600 focus:ring-indigo-500 bg-slate-800 transition-colors">
                                <div class="ml-3">
                                    <span class="text-xs font-semibold text-white group-hover:text-indigo-400 transition-colors"><?= esc($p['name']) ?></span>
                                    <span class="block text-[10px] text-slate-400 leading-tight"><?= esc($p['description']) ?></span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" onclick="closeRoleModal()" class="px-4 py-2 border border-slate-700 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-sm font-semibold text-white rounded-lg shadow-lg shadow-indigo-600/15 transition-colors">
                        Save Role
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- Modal Control Script -->
<script>
    const modal = document.getElementById('role-modal');
    const modalTitle = document.getElementById('modal-title');
    const nameInput = document.getElementById('modal-name');
    const nameWarning = document.getElementById('modal-name-warning');
    const descInput = document.getElementById('modal-description');

    function openAddRoleModal() {
        modalTitle.textContent = "Add New Role";
        nameInput.value = "";
        nameInput.readOnly = false;
        nameWarning.classList.add('hidden');
        descInput.value = "";
        
        // Uncheck all permissions
        document.querySelectorAll('input[name="permissions[]"]').forEach(el => {
            el.checked = false;
        });

        modal.classList.remove('hidden');
    }

    function openEditRoleModal(role) {
        modalTitle.textContent = "Edit Role & Mappings";
        nameInput.value = role.name;
        descInput.value = role.description;

        // If it's a system role, we don't want them renaming it directly as that could break auth filter configurations, but they can edit mappings
        if (role.is_system) {
            nameInput.readOnly = true;
            nameWarning.classList.remove('hidden');
        } else {
            nameInput.readOnly = false;
            nameWarning.classList.add('hidden');
        }

        // Check assigned permissions
        document.querySelectorAll('input[name="permissions[]"]').forEach(el => {
            el.checked = role.permissions.includes(el.value);
        });

        modal.classList.remove('hidden');
    }

    function closeRoleModal() {
        modal.classList.add('hidden');
    }
</script>
<?= $this->endSection() ?>
