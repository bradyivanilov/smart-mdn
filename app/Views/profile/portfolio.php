<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Digital Teaching Portfolio Card' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .print-card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; border-radius: 0 !important; }
            @page { margin: 1.5cm; size: A4; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 md:p-8">

    <div class="max-w-3xl mx-auto space-y-5">

        <!-- Top Action Bar -->
        <div class="flex items-center justify-between no-print">
            <a href="<?= session()->get('role') === 'kepala_sekolah' ? base_url('supervisor/teachers') : base_url('profile') ?>" 
               class="text-xs text-blue-700 font-semibold flex items-center gap-1 hover:underline">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow flex items-center gap-1.5 transition">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak Dokumen Resmi (PDF)</span>
            </button>
        </div>

        <!-- e-CV Card Main -->
        <div class="print-card bg-white rounded-3xl p-6 md:p-10 shadow-sm border border-slate-200 space-y-6">

            <!-- Formal Kop Portofolio PKB Kemendikdasmen -->
            <div class="border-b-2 border-slate-900 pb-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-900 text-white rounded-xl flex items-center justify-center font-black text-xl">
                        SM
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900 tracking-wider uppercase">KEMENTERIAN PENDIDIKAN DASAR DAN MENENGAH</h2>
                        <h1 class="text-base font-bold text-blue-900">PORTOFOLIO PENGEMBANGAN KEPROFESIAN BERKELANJUTAN (PKB) GURU</h1>
                        <span class="text-[10px] text-slate-500 font-medium">Platform SMART MADANI &bull; Terintegrasi Paradigma Deep Learning</span>
                    </div>
                </div>
                <div class="text-right hidden sm:block">
                    <span class="text-[10px] font-mono text-slate-400 block">DOKUMEN RESMI</span>
                    <strong class="text-xs font-mono text-slate-700"><?= date('Y/m/d') ?>/PKB/<?= substr(md5($user['nip']), 0, 6) ?></strong>
                </div>
            </div>

            <!-- Identitas Pendidik -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 pb-5 border-b border-slate-100">
                <img src="<?= $user['avatar_url'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80' ?>" 
                     alt="Avatar" class="w-24 h-24 rounded-2xl border-4 border-slate-100 object-cover shadow-sm">
                <div class="space-y-1 text-center sm:text-left">
                    <h3 class="text-lg font-black text-slate-900"><?= esc($user['full_name']) ?></h3>
                    <p class="text-xs text-slate-600 font-mono">NIP / NUPTK: <strong><?= esc($user['nip']) ?></strong></p>
                    <p class="text-xs text-blue-800 font-semibold"><?= esc($user['subject_specialty'] ?: 'Pendidik Deep Learning') ?></p>
                    <p class="text-xs text-slate-500 italic mt-1 max-w-lg leading-relaxed">
                        "<?= esc($user['bio'] ?? 'Pendidik aktif penggerak paradigma Deep Learning: Mindful, Meaningful, Joyful Learning.') ?>"
                    </p>
                </div>
            </div>

            <!-- Matriks Ringkasan Kinerja Guru -->
            <div class="grid grid-cols-3 gap-3 text-center text-xs">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block">Total Modul Ajar</span>
                    <strong class="text-base font-black text-blue-700"><?= count($creativities) ?> Berkas</strong>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block">Refleksi KBM</span>
                    <strong class="text-base font-black text-indigo-700"><?= count($reflections) ?> Siklus</strong>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block">Status Portofolio</span>
                    <strong class="text-base font-black text-emerald-700">Tervalidasi ✓</strong>
                </div>
            </div>

            <!-- 1. Repositori Modul & Karya Pedagogis Terkurasi -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5 border-b border-slate-200 pb-1">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-blue-700"></i>
                    <span>1. Inovasi Pedagogis &amp; Modul Ajar Terpilih Kurikulum</span>
                </h4>
                <div class="space-y-2 text-xs">
                    <?php if (empty($creativities)): ?>
                        <p class="text-slate-400 italic">Belum ada karya yang diunggah.</p>
                    <?php endif; ?>
                    <?php foreach ($creativities as $cr): ?>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-start justify-between gap-2">
                            <div>
                                <strong class="text-slate-800 text-xs block"><?= esc($cr['title']) ?></strong>
                                <span class="text-[10px] text-slate-500 uppercase font-semibold">
                                    Format: <?= str_replace('_', ' ', esc($cr['category'])) ?> &bull; Fokus: <?= ucfirst(esc($cr['deep_learning_focus'])) ?>
                                </span>
                                <?php if (!empty($cr['description'])): ?>
                                    <p class="text-[11px] text-slate-600 mt-1"><?= esc($cr['description']) ?></p>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($cr['is_featured'])): ?>
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-900 border border-amber-300 rounded font-bold text-[9px] uppercase shrink-0">
                                    Karya Terpilih
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 2. Rekam Jejak Refleksi Kemendikdasmen -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5 border-b border-slate-200 pb-1">
                    <i data-lucide="book-open" class="w-3.5 h-3.5 text-indigo-700"></i>
                    <span>2. Riwayat Refleksi KBM 4 Level Kemendikdasmen</span>
                </h4>
                <div class="space-y-2 text-xs">
                    <?php if (empty($reflections)): ?>
                        <p class="text-slate-400 italic">Belum ada catatan refleksi yang tercatat.</p>
                    <?php endif; ?>
                    <?php foreach ($reflections as $rf): ?>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                            <div class="flex items-center justify-between text-[10px]">
                                <span class="text-slate-500"><?= date('d F Y', strtotime($rf['reflection_date'])) ?></span>
                                <span class="font-bold text-blue-900 px-2 py-0.5 bg-blue-100 rounded">
                                    Kemendikdasmen Level <?= $rf['competency_level'] ?>
                                </span>
                            </div>
                            <p class="text-slate-700"><strong>Situasi KBM:</strong> <?= esc($rf['situation_analysis']) ?></p>
                            <p class="text-slate-700"><strong>Rencana Aksi:</strong> <?= esc($rf['action_plan']) ?></p>
                            <?php if (!empty($rf['principal_feedback'])): ?>
                                <p class="text-blue-900 bg-blue-50/70 p-2 rounded-lg italic text-[11px] mt-1 border border-blue-100">
                                    <strong>Catatan Coaching Kepala Sekolah:</strong> "<?= esc($rf['principal_feedback']) ?>"
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tanda Tangan & Pengesahan Dokumen Resmi -->
            <div class="pt-8 border-t-2 border-slate-200 grid grid-cols-2 text-center text-xs">
                <div>
                    <span class="text-[11px] text-slate-500 block mb-12">Pendidik Yang Bersangkutan,</span>
                    <strong class="text-slate-900 underline block"><?= esc($user['full_name']) ?></strong>
                    <span class="text-[10px] text-slate-400 font-mono">NIP: <?= esc($user['nip']) ?></span>
                </div>
                <div>
                    <span class="text-[11px] text-slate-500 block mb-12">Mengesahkan, Kepala Sekolah</span>
                    <strong class="text-slate-900 underline block">Baskoro</strong>
                    <span class="text-[10px] text-slate-400 font-mono">NIP: 09283890324</span>
                </div>
            </div>

        </div>

    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
