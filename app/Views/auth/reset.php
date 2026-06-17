<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - RBAC Security Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-tr from-slate-900 via-indigo-950 to-slate-900 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo Icon -->
        <div class="flex justify-center">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                </svg>
            </div>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-white tracking-tight">Reset your password</h2>
        <p class="mt-2 text-center text-sm text-slate-400">
            Remembered your credentials?
            <a href="/login" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                Back to login
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-slate-900/50 backdrop-blur-md py-8 px-4 border border-slate-800 shadow-2xl sm:rounded-2xl sm:px-10">
            
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 bg-red-950/40 border border-red-500/50 rounded-lg p-3 text-sm text-red-200">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="/reset" method="POST">
                <?= csrf_field() ?>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300">
                        Email address
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            placeholder="admin@rbac.com"
                            class="appearance-none block w-full px-3 py-2 border border-slate-700 rounded-lg bg-slate-800 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all">
                    </div>
                </div>

                <div class="text-xs text-slate-400">
                    For quick demo purposes, resetting will change the password of any valid account directly to <span class="text-indigo-400 font-semibold">password123</span> immediately.
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-indigo-500 transition-all transform hover:scale-[1.01]">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
