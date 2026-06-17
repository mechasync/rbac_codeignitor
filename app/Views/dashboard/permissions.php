<?= $this->extend('dashboard/layout') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left panel: Add/Register custom rule -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-xl space-y-5 h-fit">
            <div>
                <h3 class="text-lg font-bold text-white">Create Permission Rule</h3>
                <p class="text-xs text-slate-400 mt-1 leading-relaxed">Add highly customized security gates that can be immediately assigned to any system role.</p>
            </div>

            <form action="/dashboard/permissions/save" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label for="perm-name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Rule Identifier / Slug</label>
                    <input type="text" name="name" id="perm-name" required placeholder="export_financial_reports"
                        class="mt-1 block w-full px-3 py-2 border border-slate-700 rounded-lg bg-slate-850 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm bg-slate-800 transition-all">
                    <span class="block text-[10px] text-slate-500 mt-1">Identifiers are converted to lowercase snake_case automatically. E.g. "view_dashboard"</span>
                </div>

                <div>
                    <label for="perm-category" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Scope / Category</label>
                    <input type="text" name="category" id="perm-category" placeholder="Financials"
                        class="mt-1 block w-full px-3 py-2 border border-slate-700 rounded-lg bg-slate-850 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm bg-slate-800 transition-all">
                </div>

                <div>
                    <label for="perm-description" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Scope Description</label>
                    <textarea name="description" id="perm-description" required rows="3" placeholder="Grants access to run and export PDF format balance reports..."
                        class="mt-1 block w-full px-3 py-2 border border-slate-700 rounded-lg bg-slate-850 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm bg-slate-800 transition-all"></textarea>
                </div>

                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-lg shadow-indigo-600/10 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-indigo-500 transition-all transform hover:scale-[1.01]">
                    Register Permission Node
                </button>
            </form>
        </div>

        <!-- Right panel: Rules listing -->
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden h-fit">
            <div class="px-6 py-5 border-b border-slate-800 bg-slate-950/25">
                <h3 class="text-lg font-bold text-white">System Permission Scope Gateways</h3>
            </div>
            
            <div class="divide-y divide-slate-800">
                <?php if (empty($permissions)): ?>
                    <p class="p-6 text-center text-slate-500 text-sm">No permissions configured.</p>
                <?php else: ?>
                    <?php foreach ($permissions as $p): ?>
                        <div class="p-4 md:p-5 flex items-start justify-between hover:bg-slate-800/20 transition-colors">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <code class="text-xs font-bold text-indigo-400 bg-indigo-950/30 px-2 py-0.5 rounded border border-indigo-900/40">
                                        <?= esc($p['name']) ?>
                                    </code>
                                    <span class="inline-block text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 border border-slate-700">
                                        <?= esc($p['category'] ?? 'General') ?>
                                    </span>
                                    <?php if (!empty($p['is_system'])): ?>
                                        <span class="inline-block text-[9px] px-1.5 py-0.5 rounded bg-slate-800/70 text-slate-500 font-semibold uppercase tracking-wider border border-slate-800">
                                            System Guarded
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-slate-400 leading-relaxed pt-1"><?= esc($p['description']) ?></p>
                            </div>

                            <!-- Delete permission if not system-guarded -->
                            <?php if (empty($p['is_system'])): ?>
                                <a href="/dashboard/permissions/delete/<?= urlencode($p['name']) ?>"
                                   onclick="return confirm('Are you sure you want to delete permission rule \'<?= esc($p['name']) ?>\'? This will revoke it from all roles instantly.');"
                                   class="ml-4 shrink-0 text-xs font-semibold text-rose-400 hover:text-rose-300 transition-colors bg-rose-950/20 px-2 py-1.5 rounded border border-rose-900/30">
                                    Revoke Scope
                                </a>
                            <?php else: ?>
                                <span class="ml-4 shrink-0 text-slate-600 text-xs italic">Read-Only</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>
<?= $this->endSection() ?>
