<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Digital Teaching Portfolio' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 md:p-8">

    <div class="max-w-2xl mx-auto space-y-5">

        <!-- Top Action Bar -->
        <div class="flex items-center justify-between no-print">
            <a href="<?= base_url('dashboard') ?>" class="text-xs text-blue-700 font-semibold flex items-center gap-1 hover:underline">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Dashboard</span>
            </a>
            <button onclick="window.print()" class="px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow flex items-center gap-1.5 transition">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak e-CV / Simpan PDF</span>
            </button>
        </div>

        <!-- e-CV Card Main (PRD Section 5.5) -->
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200">
            <!-- Header Kartu -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 pb-6 border-b border-slate-100">
                <img src="<?= $user['avatar_url'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80' ?>" 
                     alt="Avatar" class="w-24 h-24 rounded-3xl border-4 border-blue-100 object-cover shadow">
                <div class="text-center sm:text-left">
                    <div class="inline-block px-2.5 py-0.5 bg-sky-100 text-blue-900 rounded-full text-[10px] font-bold uppercase tracking-wider mb-1">
                        Portofolio Kompetensi Guru Digital
                    </div>
                    <h1 class="text-xl font-black text-slate-900 leading-tight"><?= esc($user['full_name']) ?></h1>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">NIP: <?= esc($user['nip']) ?></p>
                    <p class="text-xs font-semibold text-blue-700 mt-1"><?= esc($user['subject_specialty']) ?></p>
                    <p class="text-xs text-slate-600 mt-2 italic max-w-md">"<?= esc($user['bio']) ?>"</p>
                </div>
            </div>

            <!-- Bagian 1: Karya Inovasi & Modul Ajar -->
            <div class="py-5 border-b border-slate-100">
                <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <i data-lucide="sparkles" class="w-4 h-4 text-blue-700"></i>
                    <span>Repositori Karya & Modul Ajar Terpilih</span>
                </h2>
                <div class="space-y-2.5">
                    <?php foreach ($creativities as $cr): ?>
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800"><?= esc($cr['title']) ?></span>
                                <span class="text-[10px] uppercase font-bold text-blue-700"><?= str_replace('_', ' ', $cr['category']) ?></span>
                            </div>
                            <p class="text-slate-600 mt-1 text-[11px]"><?= esc($cr['description']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Bagian 2: Riwayat Refleksi Pedagogis -->
            <div class="py-5">
                <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <i data-lucide="book-open" class="w-4 h-4 text-indigo-700"></i>
                    <span>Level Kompetensi & Refleksi KBM (Kemendikdasmen)</span>
                </h2>
                <div class="space-y-2.5">
                    <?php foreach ($reflections as $rf): ?>
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 text-xs">
                            <div class="flex items-center justify-between text-slate-500 text-[10px] mb-1">
                                <span><?= date('d F Y', strtotime($rf['reflection_date'])) ?></span>
                                <span class="font-bold text-blue-800 px-2 py-0.5 bg-blue-100 rounded-full">
                                    Kemendikdasmen Level <?= $rf['competency_level'] ?>
                                </span>
                            </div>
                            <p class="text-slate-700"><strong>Situasi KBM:</strong> <?= esc($rf['situation_analysis']) ?></p>
                            <p class="text-slate-700 mt-1"><strong>Rencana Aksi:</strong> <?= esc($rf['action_plan']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Footer Tanda Verifikasi -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span>Divalidasi oleh Sistem SMART MADANI Kemendikdasmen</span>
                <span>Terbit: <?= date('d M Y') ?></span>
            </div>
        </div>

    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
