<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SMART MADANI' ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#1e40af', // blue-800
                            dark: '#1e3a8a',
                            light: '#3b82f6',
                        },
                        accent: {
                            DEFAULT: '#0ea5e9', // sky-500
                            light: '#38bdf8',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Chart.js for Deep Learning Radar Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .bottom-nav {
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05), 0 -2px 4px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen pb-24 md:pb-8">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 bg-blue-800 text-white shadow-md">
        <div class="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="<?= session()->get('role') === 'kepala_sekolah' ? base_url('supervisor') : base_url('dashboard') ?>" class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg bg-sky-400 flex items-center justify-center font-black text-blue-950 text-base shadow">
                    SM
                </div>
                <div>
                    <h1 class="text-base font-bold leading-none tracking-tight">SMART MADANI</h1>
                    <span class="text-[10px] text-sky-200 tracking-wider font-medium uppercase">
                        <?= session()->get('role') === 'kepala_sekolah' ? 'Portal Kepala Sekolah' : 'Guru Deep Learning' ?>
                    </span>
                </div>
            </a>

            <?php if (session()->get('is_logged_in')): ?>
                <div class="flex items-center space-x-3">
                    <?php if (session()->get('role') === 'kepala_sekolah'): ?>
                        <a href="<?= base_url('supervisor') ?>" class="text-xs font-bold bg-amber-400 text-blue-950 px-2.5 py-1 rounded-lg flex items-center gap-1 shadow">
                            <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                            <span class="hidden sm:inline">Supervisi</span>
                        </a>
                    <?php endif; ?>

                    <a href="<?= base_url('profile') ?>" class="flex items-center space-x-2 hover:opacity-90 transition">
                        <img src="<?= session()->get('avatar_url') ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80' ?>" 
                             alt="Avatar" class="w-8 h-8 rounded-full border-2 border-sky-300 object-cover">
                        <span class="hidden sm:inline text-xs font-semibold text-slate-100"><?= esc(session()->get('full_name')) ?></span>
                    </a>
                    <a href="<?= base_url('logout') ?>" title="Keluar" class="p-1.5 rounded-lg bg-blue-900/60 hover:bg-red-600/80 text-sky-100 transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Flash Messages -->
    <main class="max-w-4xl mx-auto px-4 pt-4">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                <span><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                <span><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <!-- Mobile-First Bottom Navigation Bar -->
    <?php if (session()->get('is_logged_in')): ?>
        <?php 
            $uri = service('uri')->getSegment(1) ?? 'dashboard';
            $isKepsek = session()->get('role') === 'kepala_sekolah';
        ?>
        <nav class="bottom-nav fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200 py-1.5 px-3">
            <div class="max-w-md mx-auto grid <?= $isKepsek ? 'grid-cols-4' : 'grid-cols-5' ?> text-center text-xs">
                <?php if ($isKepsek): ?>
                    <!-- Menu Kepala Sekolah -->
                    <a href="<?= base_url('supervisor') ?>" class="flex flex-col items-center py-1 <?= ($uri === 'supervisor') ? 'text-blue-700 font-bold' : 'text-slate-500 hover:text-blue-600' ?>">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 mb-0.5"></i>
                        <span>Supervisi</span>
                    </a>
                    <a href="<?= base_url('supervisor/teachers') ?>" class="flex flex-col items-center py-1 <?= (service('uri')->getSegment(2) === 'teachers') ? 'text-blue-700 font-bold' : 'text-slate-500 hover:text-blue-600' ?>">
                        <i data-lucide="users" class="w-5 h-5 mb-0.5"></i>
                        <span>Daftar Guru</span>
                    </a>
                    <a href="<?= base_url('creativity') ?>" class="flex flex-col items-center py-1 <?= ($uri === 'creativity') ? 'text-blue-700 font-bold' : 'text-slate-500 hover:text-blue-600' ?>">
                        <i data-lucide="sparkles" class="w-5 h-5 mb-0.5"></i>
                        <span>Karya Guru</span>
                    </a>
                    <a href="<?= base_url('profile') ?>" class="flex flex-col items-center py-1 <?= ($uri === 'profile') ? 'text-blue-700 font-bold' : 'text-slate-500 hover:text-blue-600' ?>">
                        <i data-lucide="user" class="w-5 h-5 mb-0.5"></i>
                        <span>Profil</span>
                    </a>
                <?php else: ?>
                    <!-- Menu Guru -->
                    <a href="<?= base_url('dashboard') ?>" class="flex flex-col items-center py-1 <?= in_array($uri, ['dashboard', '']) ? 'text-blue-700 font-bold' : 'text-slate-500 hover:text-blue-600' ?>">
                        <i data-lucide="home" class="w-5 h-5 mb-0.5"></i>
                        <span>Beranda</span>
                    </a>
                    <a href="<?= base_url('activity') ?>" class="flex flex-col items-center py-1 <?= ($uri === 'activity') ? 'text-blue-700 font-bold' : 'text-slate-500 hover:text-blue-600' ?>">
                        <i data-lucide="clipboard-list" class="w-5 h-5 mb-0.5"></i>
                        <span>Aktivitas</span>
                    </a>
                    <a href="<?= base_url('creativity') ?>" class="flex flex-col items-center py-1 <?= ($uri === 'creativity') ? 'text-blue-700 font-bold' : 'text-slate-500 hover:text-blue-600' ?>">
                        <i data-lucide="sparkles" class="w-5 h-5 mb-0.5"></i>
                        <span>Karya</span>
                    </a>
                    <a href="<?= base_url('reflection') ?>" class="flex flex-col items-center py-1 <?= ($uri === 'reflection') ? 'text-blue-700 font-bold' : 'text-slate-500 hover:text-blue-600' ?>">
                        <i data-lucide="book-heart" class="w-5 h-5 mb-0.5"></i>
                        <span>Refleksi</span>
                    </a>
                    <a href="<?= base_url('profile') ?>" class="flex flex-col items-center py-1 <?= ($uri === 'profile') ? 'text-blue-700 font-bold' : 'text-slate-500 hover:text-blue-600' ?>">
                        <i data-lucide="user" class="w-5 h-5 mb-0.5"></i>
                        <span>Profil</span>
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    <?php endif; ?>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
