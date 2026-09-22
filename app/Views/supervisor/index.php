<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-5 max-w-4xl mx-auto pb-12">

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
                <span>Kelola Guru & Supervisi</span>
            </a>
        </div>
    </div>

    <!-- Executive Metrics Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-blue-800"><?= $totalGurus ?></span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Total Guru</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-emerald-600"><?= count($attendancesToday) ?></span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Presensi Hari Ini</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-amber-600"><?= count($pendingActivities) ?></span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Jurnal Review</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-indigo-600"><?= count($pendingReflections) ?></span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Refleksi Menunggu</span>
        </div>
    </div>

    <!-- Grid 2 Kolom: Review Jurnal & Feedback Refleksi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        <!-- 1. Jurnal Aktivitas Menunggu Verifikasi -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="check-square" class="w-4 h-4 text-amber-600"></i>
                    <span>Verifikasi Jurnal KBM Guru</span>
                </h3>
                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200">
                    <?= count($pendingActivities) ?> Antrean
                </span>
            </div>

            <?php if (empty($pendingActivities)): ?>
                <div class="p-6 text-center text-slate-400 text-xs">
                    Semua jurnal guru telah diverifikasi.
                </div>
            <?php endif; ?>

            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
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
                        <p class="text-slate-600 text-[11px]"><?= esc($act['description']) ?></p>

                        <!-- Action Form Verifikasi -->
                        <form action="<?= base_url('supervisor/verify-activity/' . $act['id']) ?>" method="post" class="flex gap-2 pt-1 border-t border-slate-200">
                            <?= csrf_field() ?>
                            <button type="submit" name="status" value="approved" 
                                    class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold transition flex items-center gap-1">
                                <i data-lucide="check" class="w-3 h-3"></i>
                                <span>Setujui</span>
                            </button>
                            <button type="submit" name="status" value="revision" 
                                    class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-[11px] font-bold transition flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                <span>Revisi</span>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 2. Refleksi Menunggu Umpan Balik / Coaching Notes -->
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
                <div class="p-6 text-center text-slate-400 text-xs">
                    Tidak ada refleksi yang menunggu respon.
                </div>
            <?php endif; ?>

            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                <?php foreach ($pendingReflections as $ref): ?>
                    <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-blue-900"><?= esc($ref['profiles']['full_name'] ?? 'Guru') ?></span>
                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded text-[10px] font-bold">
                                Level <?= $ref['competency_level'] ?>
                            </span>
                        </div>
                        <p class="text-slate-700 text-[11px]"><strong>Situasi:</strong> <?= esc(mb_strimwidth($ref['situation_analysis'], 0, 100, '...')) ?></p>
                        <p class="text-slate-700 text-[11px]"><strong>Tantangan:</strong> <?= esc(mb_strimwidth($ref['challenge_identification'], 0, 100, '...')) ?></p>

                        <!-- Input Feedback Kepala Sekolah -->
                        <form action="<?= base_url('supervisor/feedback-reflection/' . $ref['id']) ?>" method="post" class="space-y-2 pt-1 border-t border-slate-200">
                            <?= csrf_field() ?>
                            <input type="text" name="principal_feedback" required placeholder="Tulis catatan coaching & apresiasi..."
                                   class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                            <button type="submit" class="px-3 py-1 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-[11px] font-bold flex items-center gap-1 transition">
                                <i data-lucide="send" class="w-3 h-3"></i>
                                <span>Kirim Umpan Balik</span>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <!-- Riwayat Supervisi KBM Terakhir -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
        <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
            <i data-lucide="award" class="w-4 h-4 text-emerald-600"></i>
            <span>Riwayat Supervisi Klinis KBM Terkini</span>
        </h3>

        <?php if (empty($supervisions)): ?>
            <p class="text-xs text-slate-400">Belum ada catatan supervisi klinis yang diterbitkan.</p>
        <?php endif; ?>

        <div class="divide-y divide-slate-100 text-xs">
            <?php foreach ($supervisions as $sup): ?>
                <div class="py-2.5 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-800"><?= esc($sup['profiles']['full_name'] ?? 'Guru') ?></span>
                        <span class="text-slate-500 block text-[11px]"><?= esc($sup['class_name']) ?> - <?= esc($sup['subject']) ?> (<?= date('d M Y', strtotime($sup['supervision_date'])) ?>)</span>
                    </div>
                    <div class="text-right">
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold text-[11px]">
                            Skor DL: <?= $sup['deep_learning_score'] ?>/4.0
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
