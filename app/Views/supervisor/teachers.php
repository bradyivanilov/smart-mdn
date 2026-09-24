<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-5 max-w-4xl mx-auto pb-14">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Manajemen Dewan Guru</h2>
            <p class="text-xs text-slate-500">Pemantauan Kinerja, Portofolio Digital &amp; Pelaksanaan Supervisi</p>
        </div>
        <a href="<?= base_url('supervisor') ?>" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold flex items-center gap-1 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Portal</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php if (empty($teachers)): ?>
            <div class="col-span-2 bg-white rounded-3xl p-10 text-center border border-slate-200 text-slate-400 text-xs">
                Belum ada akun guru yang terdaftar. Guru dapat mendaftar mandiri via halaman registrasi.
            </div>
        <?php endif; ?>

        <?php foreach ($teachers as $t): ?>
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4 hover:shadow-md transition">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <a href="<?= base_url('supervisor/teacher/' . $t['id']) ?>" class="flex items-start gap-3 hover:opacity-90 transition">
                            <img src="<?= $t['avatar_url'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80' ?>" 
                                 alt="Avatar" class="w-13 h-13 rounded-2xl object-cover border-2 border-blue-200 shadow shrink-0">
                            <div>
                                <h3 class="font-bold text-sm text-slate-800 leading-snug hover:text-blue-700"><?= esc($t['full_name']) ?></h3>
                                <p class="text-xs text-slate-500 font-mono">NIP: <?= esc($t['nip']) ?></p>
                                <span class="inline-block mt-1 px-2.5 py-0.5 bg-blue-50 text-blue-700 rounded-md text-[10px] font-semibold border border-blue-100">
                                    <?= esc($t['subject_specialty'] ?: 'Mata Pelajaran Belum Diisi') ?>
                                </span>
                            </div>
                        </a>

                        <!-- Status Presensi Hari Ini -->
                        <div>
                            <?php if (!empty($t['today_attendance'])): ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    <?= ($t['today_attendance']['status'] === 'hadir') ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                    <?= esc($t['today_attendance']['status']) ?>
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-500">
                                    Belum Presensi
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Ringkasan Asesmen Supervisi Guru -->
                    <div class="mt-3.5 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2 bg-slate-50 rounded-xl">
                            <span class="text-[10px] text-slate-500 block">Sesi Supervisi:</span>
                            <strong class="text-slate-800 text-sm"><?= $t['supervision_count'] ?> kali</strong>
                        </div>
                        <div class="p-2 bg-slate-50 rounded-xl">
                            <span class="text-[10px] text-slate-500 block">Rata-rata Skor DL:</span>
                            <strong class="text-blue-700 text-sm">
                                <?= $t['avg_supervision'] ? $t['avg_supervision'] . '/4.0' : 'Belum Ada' ?>
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: Audit Lembar Kinerja & Supervisi Klinis Baru -->
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="<?= base_url('supervisor/teacher/' . $t['id']) ?>" 
                       class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition">
                        <i data-lucide="folder-search" class="w-3.5 h-3.5 text-blue-700"></i>
                        <span>Audit Kinerja</span>
                    </a>

                    <a href="<?= base_url('supervisor/supervise/' . $t['id']) ?>" 
                       class="flex-1 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow flex items-center justify-center gap-1.5 transition">
                        <i data-lucide="clipboard-check" class="w-3.5 h-3.5"></i>
                        <span>Asesmen Baru</span>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>
