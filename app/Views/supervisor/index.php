<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-4xl mx-auto pb-14">

    <!-- Header Banner Kepala Sekolah -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white rounded-3xl p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-400/20 border border-amber-300/30 text-amber-200 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                <i data-lucide="shield" class="w-3.5 h-3.5 text-amber-400"></i>
                <span>Portal Supervisi Kepala Sekolah</span>
            </div>
            <h2 class="text-xl font-bold">Halo, <?= esc(session()->get('full_name')) ?></h2>
            <p class="text-xs text-sky-200 mt-0.5">Monitoring KBM Berbasis 4 Kompetensi & Paradigma Deep Learning</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="<?= base_url('supervisor/teachers') ?>" 
               class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold shadow transition flex items-center gap-1.5">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span>Manajemen Guru</span>
            </a>
        </div>
    </div>

    <!-- Executive Metrics Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-blue-800"><?= $totalGurus ?></span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Dewan Guru</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-emerald-600"><?= count($attendancesToday) ?></span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Presensi Hari Ini</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-amber-600"><?= count($pendingActivities) ?></span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Antrean Jurnal</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-indigo-600"><?= count($pendingReflections) ?></span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Antrean Refleksi</span>
        </div>
    </div>

    <!-- 1. Ringkasan Kehadiran Dewan Guru Hari Ini -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="calendar-check" class="w-4 h-4 text-emerald-600"></i>
                <span>Monitoring Kehadiran Guru (Hari Ini: <?= date('d M Y') ?>)</span>
            </h3>
            <span class="text-[11px] text-slate-500">
                <?= count($attendancesToday) ?> dari <?= $totalGurus ?> Guru Hadir
            </span>
        </div>

        <?php if (!empty($unattendedGurus)): ?>
            <div class="p-3 bg-amber-50/80 border border-amber-200 rounded-2xl text-xs space-y-1">
                <div class="font-bold text-amber-900 flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-amber-600"></i>
                    <span>Belum Melakukan Presensi Hari Ini (<?= count($unattendedGurus) ?> Guru):</span>
                </div>
                <div class="flex flex-wrap gap-1.5 pt-1">
                    <?php foreach ($unattendedGurus as $ug): ?>
                        <span class="px-2.5 py-1 bg-white border border-amber-300 text-amber-900 rounded-lg text-[11px] font-medium">
                            <?= esc($ug['full_name']) ?> (<?= esc($ug['subject_specialty'] ?: 'Guru') ?>)
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($attendancesToday)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs pt-1">
                <?php foreach ($attendancesToday as $att): ?>
                    <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800 block"><?= esc($att['profiles']['full_name'] ?? 'Guru') ?></span>
                            <span class="text-[10px] text-slate-500 font-mono">
                                Masuk: <?= substr($att['check_in_time'] ?? '--', 0, 5) ?> WIB
                                <?= !empty($att['check_out_time']) ? '| Pulang: ' . substr($att['check_out_time'], 0, 5) : '' ?>
                            </span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                            <?= ($att['status'] === 'hadir') ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                            <?= esc($att['status']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- 2. Grid 2 Kolom: Verifikasi Jurnal KBM & Umpan Balik Refleksi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        <!-- A. Verifikasi Jurnal Aktivitas Guru -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="check-square" class="w-4 h-4 text-amber-600"></i>
                    <span>Verifikasi Jurnal Aktivitas KBM</span>
                </h3>
                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200">
                    <?= count($pendingActivities) ?> Menunggu
                </span>
            </div>

            <?php if (empty($pendingActivities)): ?>
                <div class="p-8 text-center text-slate-400 text-xs">
                    <i data-lucide="check-circle-2" class="w-8 h-8 text-emerald-500 mx-auto mb-1 opacity-70"></i>
                    <p class="font-medium text-slate-600">Seluruh jurnal KBM telah ditinjau</p>
                </div>
            <?php endif; ?>

            <div class="space-y-3.5 max-h-[460px] overflow-y-auto pr-1">
                <?php foreach ($pendingActivities as $act): ?>
                    <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-blue-900"><?= esc($act['profiles']['full_name'] ?? 'Guru') ?></span>
                            <span class="text-[10px] text-slate-400"><?= date('d M Y', strtotime($act['activity_date'])) ?></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-md text-[10px] font-bold uppercase">
                                <?= esc($act['competency_type']) ?>
                            </span>
                            <span class="px-2 py-0.5 bg-sky-100 text-sky-800 rounded-md text-[10px] font-semibold">
                                <?= ucfirst(esc($act['deep_learning_pillar'])) ?>
                            </span>
                        </div>
                        <p class="font-semibold text-slate-800"><?= esc($act['title']) ?></p>
                        <p class="text-slate-600 text-[11px] leading-relaxed"><?= nl2br(esc($act['description'])) ?></p>

                        <?php if (!empty($act['evidence_file_url'])): ?>
                            <a href="<?= base_url($act['evidence_file_url']) ?>" target="_blank"
                               class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:underline">
                                <i data-lucide="paperclip" class="w-3 h-3"></i>
                                <span>Lihat Dokumen Bukti Lampiran</span>
                            </a>
                        <?php endif; ?>

                        <!-- Form Verifikasi dengan Catatan Revisi / Setujui -->
                        <form action="<?= base_url('supervisor/verify-activity/' . $act['id']) ?>" method="post" class="space-y-2 pt-2 border-t border-slate-200">
                            <?= csrf_field() ?>
                            <input type="text" name="verification_notes" placeholder="Catatan perbaikan / apresiasi (opsional)..."
                                   class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                            <div class="flex gap-2">
                                <button type="submit" name="status" value="approved" 
                                        class="flex-1 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold transition flex items-center justify-center gap-1">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    <span>Setujui</span>
                                </button>
                                <button type="submit" name="status" value="revision" 
                                        class="flex-1 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-[11px] font-bold transition flex items-center justify-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                    <span>Minta Revisi</span>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- B. Umpan Balik Refleksi Guru (Coaching Dialog Apresiatif) -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="message-square" class="w-4 h-4 text-indigo-600"></i>
                    <span>Umpan Balik Refleksi Guru</span>
                </h3>
                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded-full border border-indigo-200">
                    <?= count($pendingReflections) ?> Menunggu
                </span>
            </div>

            <?php if (empty($pendingReflections)): ?>
                <div class="p-8 text-center text-slate-400 text-xs">
                    <i data-lucide="sparkles" class="w-8 h-8 text-indigo-500 mx-auto mb-1 opacity-70"></i>
                    <p class="font-medium text-slate-600">Tidak ada refleksi yang menunggu umpan balik</p>
                </div>
            <?php endif; ?>

            <div class="space-y-3.5 max-h-[460px] overflow-y-auto pr-1">
                <?php foreach ($pendingReflections as $ref): ?>
                    <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-blue-900"><?= esc($ref['profiles']['full_name'] ?? 'Guru') ?></span>
                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-md text-[10px] font-bold">
                                Level <?= $ref['competency_level'] ?> Kemendikdasmen
                            </span>
                        </div>
                        <div class="space-y-1">
                            <p class="text-slate-700 text-[11px]">
                                <strong class="text-blue-800">Situasi KBM:</strong> <?= esc($ref['situation_analysis']) ?>
                            </p>
                            <p class="text-slate-700 text-[11px]">
                                <strong class="text-amber-800">Tantangan:</strong> <?= esc($ref['challenge_identification']) ?>
                            </p>
                            <p class="text-slate-700 text-[11px]">
                                <strong class="text-emerald-800">Rencana Aksi:</strong> <?= esc($ref['action_plan']) ?>
                            </p>
                        </div>

                        <!-- Input Coaching Feedback Kepala Sekolah -->
                        <form action="<?= base_url('supervisor/feedback-reflection/' . $ref['id']) ?>" method="post" class="space-y-2 pt-2 border-t border-slate-200">
                            <?= csrf_field() ?>
                            <textarea name="principal_feedback" required rows="2" placeholder="Tuliskan catatan coaching, apresiasi, dan arahan pedagogis..."
                                      class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"></textarea>
                            <button type="submit" class="w-full py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-[11px] font-bold flex items-center justify-center gap-1 transition">
                                <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                <span>Kirim Umpan Balik Coaching</span>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <!-- 3. Riwayat Asesmen Supervisi KBM Klinis Terkini -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="award" class="w-4 h-4 text-emerald-600"></i>
                <span>Riwayat Asesmen Supervisi Klinis Terkini</span>
            </h3>
            <a href="<?= base_url('supervisor/teachers') ?>" class="text-[11px] font-semibold text-blue-700 hover:underline">
                Kelola Semua Guru &rarr;
            </a>
        </div>

        <?php if (empty($supervisions)): ?>
            <div class="p-6 text-center text-slate-400 text-xs">
                Belum ada berkas asesmen supervisi KBM yang diterbitkan. Silakan buka menu <strong>Manajemen Guru</strong> untuk memulai observasi kelas.
            </div>
        <?php endif; ?>

        <div class="divide-y divide-slate-100 text-xs">
            <?php foreach ($supervisions as $sup): ?>
                <div class="py-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800"><?= esc($sup['profiles']['full_name'] ?? 'Guru') ?></span>
                            <span class="text-[10px] text-slate-400 font-mono"><?= date('d M Y', strtotime($sup['supervision_date'])) ?></span>
                        </div>
                        <span class="text-slate-500 block text-[11px] mt-0.5">
                            Kelas <?= esc($sup['class_name']) ?> &bull; <?= esc($sup['subject']) ?>
                        </span>
                        <p class="text-slate-600 text-[11px] mt-1 italic">
                            "<?= esc(mb_strimwidth($sup['coaching_notes'], 0, 100, '...')) ?>"
                        </p>
                    </div>
                    <div class="flex sm:flex-col items-end gap-1 shrink-0">
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold text-[11px]">
                            Skor DL: <?= $sup['deep_learning_score'] ?>/4.0
                        </span>
                        <span class="text-[10px] text-slate-400">
                            Pedagogik: <?= $sup['score_pedagogic'] ?> &bull; Prof: <?= $sup['score_professional'] ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
