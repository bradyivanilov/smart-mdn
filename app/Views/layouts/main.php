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
                            DEFAULT: '#1e40af', // royal blue
                            dark: '#0f172a',
                            light: '#3b82f6',
                        },
                        accent: {
                            DEFAULT: '#0284c7', // sky-600
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
            box-shadow: 0 -4px 10px -2px rgba(15, 23, 42, 0.06);
        }
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        @media print {
            .no-print { display: none !important; }
            .print-full { margin: 0 !important; width: 100% !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen bg-slate-50 flex flex-col md:flex-row pb-20 md:pb-0">

    <?php 
        $isLoggedIn = session()->get('is_logged_in');
        $role = session()->get('role') ?? 'guru';
        $uri1 = service('uri')->getSegment(1) ?? '';
        $uri2 = service('uri')->getSegment(2) ?? '';
        $fullName = session()->get('full_name') ?? 'Pengguna';
        $avatarUrl = session()->get('avatar_url') ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80';
    ?>

    <?php if ($isLoggedIn): ?>
        <!-- ============================================================== -->
        <!-- 1. DESKTOP PERMANENT SIDEBAR (> 768px) -->
        <!-- ============================================================== -->
        <aside class="hidden md:flex flex-col w-64 bg-slate-900 text-white min-h-screen shrink-0 border-r border-slate-800 sticky top-0 h-screen sidebar-scroll overflow-y-auto no-print">
            <!-- Brand Logo -->
            <div class="p-5 border-b border-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-700 to-sky-400 flex items-center justify-center font-black text-white text-lg shadow-lg">
                    SM
                </div>
                <div>
                    <h1 class="text-sm font-bold tracking-tight text-white leading-tight">SMART MADANI</h1>
                    <span class="text-[10px] text-sky-400 font-semibold tracking-wider uppercase block">
                        <?= $role === 'kepala_sekolah' ? 'Supervisi Sekolah' : 'Pendidik Deep Learning' ?>
                    </span>
                </div>
            </div>

            <!-- Profile Info Widget -->
            <div class="p-4 mx-3 my-3 rounded-2xl bg-slate-800/60 border border-slate-700/60 flex items-center gap-3">
                <img src="<?= $avatarUrl ?>" alt="Avatar" class="w-10 h-10 rounded-xl object-cover border-2 border-sky-400 shadow">
                <div class="overflow-hidden">
                    <span class="text-xs font-bold text-white block truncate"><?= esc($fullName) ?></span>
                    <span class="text-[10px] text-slate-400 font-medium block truncate">
                        <?= $role === 'kepala_sekolah' ? 'Kepala Sekolah' : esc(session()->get('subject_specialty') ?: 'Guru') ?>
                    </span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 space-y-1 text-xs">
                <?php if ($role === 'kepala_sekolah'): ?>
                    <!-- MENU KEPALA SEKOLAH -->
                    <div class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Portal Pengawasan</div>
                    
                    <a href="<?= base_url('supervisor') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= ($uri1 === 'supervisor' && empty($uri2)) ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-sky-400"></i>
                        <span>Executive Dashboard</span>
                    </a>

                    <a href="<?= base_url('supervisor/teachers') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= ($uri1 === 'supervisor' && in_array($uri2, ['teachers', 'teacher', 'supervise'])) ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="users" class="w-4 h-4 text-blue-400"></i>
                        <span>Manajemen Guru</span>
                    </a>

                    <a href="<?= base_url('supervisor/schedules') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= ($uri1 === 'supervisor' && $uri2 === 'schedules') ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="calendar" class="w-4 h-4 text-amber-400"></i>
                        <span>Jadwal Supervisi</span>
                    </a>

                    <a href="<?= base_url('supervisor/attendance-recap') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= ($uri1 === 'supervisor' && $uri2 === 'attendance-recap') ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="clipboard-check" class="w-4 h-4 text-emerald-400"></i>
                        <span>Rekap Presensi & Jam</span>
                    </a>

                    <a href="<?= base_url('supervisor/coaching-plans') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= ($uri1 === 'supervisor' && $uri2 === 'coaching-plans') ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="target" class="w-4 h-4 text-purple-400"></i>
                        <span>Pelacak RTL Coaching</span>
                    </a>

                    <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Kurasi & Koleksi</div>

                    <a href="<?= base_url('creativity') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= ($uri1 === 'creativity') ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="sparkles" class="w-4 h-4 text-amber-300"></i>
                        <span>Kurasi Modul Ajar</span>
                    </a>

                    <a href="<?= base_url('profile') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= ($uri1 === 'profile') ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                        <span>Profil Pengguna</span>
                    </a>

                <?php else: ?>
                    <!-- MENU GURU -->
                    <div class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Aktivitas Mengajar</div>

                    <a href="<?= base_url('dashboard') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= in_array($uri1, ['dashboard', '']) ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="home" class="w-4 h-4 text-sky-400"></i>
                        <span>Dashboard KBM</span>
                    </a>

                    <a href="<?= base_url('attendance') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= ($uri1 === 'attendance') ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="camera" class="w-4 h-4 text-emerald-400"></i>
                        <span>Smart Attendance</span>
                    </a>

                    <a href="<?= base_url('activity') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= ($uri1 === 'activity') ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="clipboard-list" class="w-4 h-4 text-blue-400"></i>
                        <span>Jurnal 4 Kompetensi</span>
                    </a>

                    <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Pengembangan Diri</div>

                    <a href="<?= base_url('creativity') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= ($uri1 === 'creativity') ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="sparkles" class="w-4 h-4 text-amber-300"></i>
                        <span>Our Creativity</span>
                    </a>

                    <a href="<?= base_url('reflection') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= ($uri1 === 'reflection' && $uri2 !== 'peer-observation') ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="book-heart" class="w-4 h-4 text-rose-400"></i>
                        <span>Our Refleksi</span>
                    </a>

                    <a href="<?= base_url('reflection/peer-observation') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= ($uri1 === 'reflection' && $uri2 === 'peer-observation') ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="eye" class="w-4 h-4 text-purple-400"></i>
                        <span>Peer Observation</span>
                    </a>

                    <a href="<?= base_url('profile') ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition <?= ($uri1 === 'profile') ? 'bg-blue-600 text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                        <span>Profil &amp; e-CV</span>
                    </a>
                <?php endif; ?>
            </nav>

            <!-- Bottom Logout -->
            <div class="p-3 border-t border-slate-800">
                <a href="<?= base_url('logout') ?>" 
                   class="flex items-center justify-center gap-2 px-3 py-2 bg-slate-800 hover:bg-red-700/80 text-slate-300 hover:text-white rounded-xl text-xs font-semibold transition">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span>Keluar Akun</span>
                </a>
            </div>
        </aside>
    <?php endif; ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Topbar Desktop & Mobile Header -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200 px-4 h-14 flex items-center justify-between shadow-xs no-print">
            <div class="flex items-center gap-3">
                <!-- Mobile Logo -->
                <div class="md:hidden flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-700 text-white font-black text-sm flex items-center justify-center shadow">
                        SM
                    </div>
                    <span class="font-bold text-sm text-slate-800 tracking-tight">SMART MADANI</span>
                </div>

                <!-- Desktop Breadcrumb -->
                <div class="hidden md:flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <span class="text-blue-700 font-bold"><?= $role === 'kepala_sekolah' ? 'Portal Supervisi' : 'Portal Guru' ?></span>
                    <span>/</span>
                    <span class="text-slate-800 capitalize"><?= esc($uri1 ?: 'Beranda') ?></span>
                    <?php if (!empty($uri2)): ?>
                        <span>/</span>
                        <span class="text-slate-500 capitalize"><?= esc($uri2) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($isLoggedIn): ?>
                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline text-xs text-slate-500"><?= date('l, d F Y') ?></span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                        <?= $role === 'kepala_sekolah' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-blue-100 text-blue-800 border border-blue-200' ?>">
                        <?= $role === 'kepala_sekolah' ? 'Kepala Sekolah' : 'Guru' ?>
                    </span>

                    <a href="<?= base_url('profile') ?>" class="flex items-center gap-2 hover:opacity-80 transition">
                        <img src="<?= $avatarUrl ?>" alt="Avatar" class="w-8 h-8 rounded-full object-cover border-2 border-slate-200">
                    </a>

                    <!-- Mobile Logout Icon -->
                    <a href="<?= base_url('logout') ?>" class="md:hidden p-1.5 text-slate-400 hover:text-red-600 transition" title="Keluar">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </a>
                </div>
            <?php endif; ?>
        </header>

        <!-- Flash Messages & Main Body -->
        <main class="flex-1 px-4 py-5 max-w-6xl w-full mx-auto print-full">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs flex items-center gap-2 shadow-xs">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                    <span><?= session()->getFlashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs flex items-center gap-2 shadow-xs">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                    <span><?= session()->getFlashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- ============================================================== -->
    <!-- 2. MOBILE BOTTOM NAVIGATION (< 768px) -->
    <!-- ============================================================== -->
    <?php if ($isLoggedIn): ?>
        <nav class="md:hidden bottom-nav fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200 py-1.5 px-3 no-print">
            <div class="max-w-md mx-auto grid <?= $role === 'kepala_sekolah' ? 'grid-cols-5' : 'grid-cols-5' ?> text-center text-xs">
                <?php if ($role === 'kepala_sekolah'): ?>
                    <a href="<?= base_url('supervisor') ?>" class="flex flex-col items-center py-1 <?= ($uri1 === 'supervisor' && empty($uri2)) ? 'text-blue-700 font-bold' : 'text-slate-500' ?>">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 mb-0.5"></i>
                        <span class="text-[10px]">Dashboard</span>
                    </a>
                    <a href="<?= base_url('supervisor/teachers') ?>" class="flex flex-col items-center py-1 <?= ($uri1 === 'supervisor' && $uri2 === 'teachers') ? 'text-blue-700 font-bold' : 'text-slate-500' ?>">
                        <i data-lucide="users" class="w-5 h-5 mb-0.5"></i>
                        <span class="text-[10px]">Guru</span>
                    </a>
                    <a href="<?= base_url('supervisor/schedules') ?>" class="flex flex-col items-center py-1 <?= ($uri1 === 'supervisor' && $uri2 === 'schedules') ? 'text-blue-700 font-bold' : 'text-slate-500' ?>">
                        <i data-lucide="calendar" class="w-5 h-5 mb-0.5"></i>
                        <span class="text-[10px]">Jadwal</span>
                    </a>
                    <a href="<?= base_url('creativity') ?>" class="flex flex-col items-center py-1 <?= ($uri1 === 'creativity') ? 'text-blue-700 font-bold' : 'text-slate-500' ?>">
                        <i data-lucide="sparkles" class="w-5 h-5 mb-0.5"></i>
                        <span class="text-[10px]">Kurasi</span>
                    </a>
                    <a href="<?= base_url('profile') ?>" class="flex flex-col items-center py-1 <?= ($uri1 === 'profile') ? 'text-blue-700 font-bold' : 'text-slate-500' ?>">
                        <i data-lucide="user" class="w-5 h-5 mb-0.5"></i>
                        <span class="text-[10px]">Profil</span>
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('dashboard') ?>" class="flex flex-col items-center py-1 <?= in_array($uri1, ['dashboard', '']) ? 'text-blue-700 font-bold' : 'text-slate-500' ?>">
                        <i data-lucide="home" class="w-5 h-5 mb-0.5"></i>
                        <span class="text-[10px]">Beranda</span>
                    </a>
                    <a href="<?= base_url('activity') ?>" class="flex flex-col items-center py-1 <?= ($uri1 === 'activity') ? 'text-blue-700 font-bold' : 'text-slate-500' ?>">
                        <i data-lucide="clipboard-list" class="w-5 h-5 mb-0.5"></i>
                        <span class="text-[10px]">Aktivitas</span>
                    </a>
                    <a href="<?= base_url('creativity') ?>" class="flex flex-col items-center py-1 <?= ($uri1 === 'creativity') ? 'text-blue-700 font-bold' : 'text-slate-500' ?>">
                        <i data-lucide="sparkles" class="w-5 h-5 mb-0.5"></i>
                        <span class="text-[10px]">Karya</span>
                    </a>
                    <a href="<?= base_url('reflection') ?>" class="flex flex-col items-center py-1 <?= ($uri1 === 'reflection') ? 'text-blue-700 font-bold' : 'text-slate-500' ?>">
                        <i data-lucide="book-heart" class="w-5 h-5 mb-0.5"></i>
                        <span class="text-[10px]">Refleksi</span>
                    </a>
                    <a href="<?= base_url('profile') ?>" class="flex flex-col items-center py-1 <?= ($uri1 === 'profile') ? 'text-blue-700 font-bold' : 'text-slate-500' ?>">
                        <i data-lucide="user" class="w-5 h-5 mb-0.5"></i>
                        <span class="text-[10px]">Profil</span>
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
