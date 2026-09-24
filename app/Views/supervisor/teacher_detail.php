<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-4xl mx-auto pb-14">

    <!-- Header & Breadcrumb -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="<?= base_url('supervisor/teachers') ?>" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h2 class="text-lg font-bold text-slate-800">Lembar Audit Kinerja &amp; Asesmen Guru</h2>
                <p class="text-xs text-slate-500">Evaluasi Menyeluruh KBM, Portofolio &amp; Observasi Kelas</p>
            </div>
        </div>

        <a href="<?= base_url('supervisor/supervise/' . $teacher['id']) ?>" 
           class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow transition flex items-center gap-1.5">
            <i data-lucide="clipboard-check" class="w-4 h-4"></i>
            <span>Beri Nilai Supervisi Baru</span>
        </a>
    </div>

    <!-- 1. Hero Kartu Identitas Guru -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white rounded-3xl p-6 shadow-sm flex flex-col sm:flex-row items-center sm:items-start justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 text-center sm:text-left">
            <img src="<?= $teacher['avatar_url'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80' ?>" 
                 alt="Avatar" class="w-20 h-20 rounded-3xl object-cover border-4 border-sky-300 shadow">
            <div>
                <div class="inline-block px-2.5 py-0.5 bg-sky-400/20 text-sky-200 rounded-full text-[10px] font-bold uppercase tracking-wider mb-1">
                    Dewan Guru Deep Learning
                </div>
                <h1 class="text-xl font-bold text-white"><?= esc($teacher['full_name']) ?></h1>
                <p class="text-xs text-sky-200 font-mono mt-0.5">NIP: <?= esc($teacher['nip']) ?></p>
                <span class="inline-block mt-1.5 px-3 py-1 bg-white/10 text-sky-100 rounded-lg text-xs font-medium border border-white/10">
                    <?= esc($teacher['subject_specialty'] ?: 'Mata Pelajaran Belum Diset') ?>
                </span>
            </div>
        </div>

        <div class="flex sm:flex-col items-center sm:items-end gap-2 shrink-0">
            <a href="<?= base_url('portfolio/' . $teacher['nip']) ?>" target="_blank"
               class="px-3.5 py-1.5 bg-white text-blue-950 rounded-xl text-xs font-bold shadow hover:bg-sky-50 transition flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>Lihat e-CV Publik</span>
            </a>
            <span class="text-[11px] text-sky-200">
                Terdaftar: <?= date('d M Y', strtotime($teacher['created_at'] ?? 'now')) ?>
            </span>
        </div>
    </div>

    <!-- 2. Ringkasan Kinerja Guru (KPI Metrics) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-blue-700"><?= count($attendances) ?></span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Hari Hadir</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-indigo-700"><?= count($activities) ?></span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Jurnal KBM</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-sky-600"><?= count($creativities) ?></span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Modul/Karya</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-emerald-600"><?= count($supervisions) ?></span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Sesi Asesmen</span>
        </div>
    </div>

    <!-- 3. Riwayat Asesmen Supervisi KBM Klinis Guru Ini -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i data-lucide="award" class="w-4 h-4 text-blue-700"></i>
                <span>Riwayat Rapor Asesmen Supervisi Klinis</span>
            </h3>
            <span class="text-xs text-slate-400"><?= count($supervisions) ?> Hasil Penilaian</span>
        </div>

        <?php if (empty($supervisions)): ?>
            <div class="p-6 bg-slate-50 rounded-2xl text-center text-xs text-slate-500 border border-slate-100">
                Belum ada berkas asesmen supervisi untuk guru ini. Silakan klik tombol <strong>"Beri Nilai Supervisi Baru"</strong> di atas.
            </div>
        <?php endif; ?>

        <div class="space-y-3">
            <?php foreach ($supervisions as $sup): ?>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-2">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-1">
                        <div>
                            <span class="font-bold text-slate-800 text-sm">Kelas <?= esc($sup['class_name']) ?> &bull; <?= esc($sup['subject']) ?></span>
                            <span class="text-[11px] text-slate-500 block">Kunjungan Kelas: <?= date('d F Y', strtotime($sup['supervision_date'])) ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full">
                                Skor Deep Learning: <?= $sup['deep_learning_score'] ?>/4.0
                            </span>
                        </div>
                    </div>

                    <!-- 4 Pilar Score Bar -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-slate-200 text-center">
                        <div class="p-1.5 bg-white rounded-lg border border-slate-200">
                            <span class="text-[10px] text-slate-500 block">Pedagogik</span>
                            <strong class="text-blue-700"><?= $sup['score_pedagogic'] ?>/4.0</strong>
                        </div>
                        <div class="p-1.5 bg-white rounded-lg border border-slate-200">
                            <span class="text-[10px] text-slate-500 block">Kepribadian</span>
                            <strong class="text-blue-700"><?= $sup['score_personality'] ?>/4.0</strong>
                        </div>
                        <div class="p-1.5 bg-white rounded-lg border border-slate-200">
                            <span class="text-[10px] text-slate-500 block">Sosial</span>
                            <strong class="text-blue-700"><?= $sup['score_social'] ?>/4.0</strong>
                        </div>
                        <div class="p-1.5 bg-white rounded-lg border border-slate-200">
                            <span class="text-[10px] text-slate-500 block">Profesional</span>
                            <strong class="text-blue-700"><?= $sup['score_professional'] ?>/4.0</strong>
                        </div>
                    </div>

                    <div class="pt-2 text-slate-700">
                        <strong class="text-blue-900 block text-[11px] uppercase">Catatan Coaching Kepala Sekolah:</strong>
                        <p class="italic bg-white p-2.5 rounded-xl border border-slate-200 mt-1">
                            "<?= esc($sup['coaching_notes']) ?>"
                        </p>
                    </div>

                    <?php if (!empty($sup['followup_action'])): ?>
                        <div class="text-slate-700">
                            <strong class="text-emerald-900 block text-[11px] uppercase">Rekomendasi Tindak Lanjut:</strong>
                            <p class="bg-emerald-50 text-emerald-900 p-2.5 rounded-xl border border-emerald-200 mt-1">
                                <?= esc($sup['followup_action']) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 4. Seluruh Jurnal Aktivitas KBM Guru (Dengan Form Verifikasi Langsung) -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i data-lucide="clipboard-list" class="w-4 h-4 text-amber-600"></i>
                <span>Jurnal Aktivitas 4 Kompetensi Guru</span>
            </h3>
            <span class="text-xs text-slate-400"><?= count($activities) ?> Berkas</span>
        </div>

        <?php if (empty($activities)): ?>
            <p class="text-xs text-slate-400">Guru belum mencatat aktivitas KBM.</p>
        <?php endif; ?>

        <div class="space-y-3">
            <?php foreach ($activities as $act): ?>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md font-bold uppercase text-[10px]
                                <?= ($act['competency_type'] === 'pedagogik') ? 'bg-blue-100 text-blue-800' : 'bg-indigo-100 text-indigo-800' ?>">
                                <?= esc($act['competency_type']) ?>
                            </span>
                            <span class="px-2 py-0.5 bg-sky-100 text-sky-800 rounded-md text-[10px] font-semibold">
                                <?= ucfirst(esc($act['deep_learning_pillar'])) ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-slate-400"><?= date('d M Y', strtotime($act['activity_date'])) ?></span>
                            <span class="px-2 py-0.5 rounded-full font-bold text-[10px] uppercase
                                <?= ($act['verification_status'] === 'approved') ? 'bg-emerald-100 text-emerald-800' : ($act['verification_status'] === 'revision' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') ?>">
                                <?= esc($act['verification_status']) ?>
                            </span>
                        </div>
                    </div>

                    <h4 class="font-bold text-slate-800 text-sm"><?= esc($act['title']) ?></h4>
                    <p class="text-slate-600 leading-relaxed"><?= nl2br(esc($act['description'])) ?></p>

                    <?php if (!empty($act['evidence_file_url'])): ?>
                        <a href="<?= base_url($act['evidence_file_url']) ?>" target="_blank"
                           class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:underline">
                            <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                            <span>Buka Dokumen Bukti Lampiran</span>
                        </a>
                    <?php endif; ?>

                    <!-- Form Verifikasi Cepat oleh Kepala Sekolah -->
                    <form action="<?= base_url('supervisor/verify-activity/' . $act['id']) ?>" method="post" 
                          class="pt-2 border-t border-slate-200 flex flex-col sm:flex-row items-center gap-2">
                        <?= csrf_field() ?>
                        <input type="text" name="verification_notes" 
                               value="<?= esc($act['verification_notes'] ?? '') ?>"
                               placeholder="Tuliskan catatan arahan / instruksi perbaikan..."
                               class="flex-1 w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="submit" name="status" value="approved" 
                                    class="flex-1 sm:flex-none px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                <span>Setujui</span>
                            </button>
                            <button type="submit" name="status" value="revision" 
                                    class="flex-1 sm:flex-none px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                <span>Revisi</span>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 5. Repositori Karya & Modul Ajar Guru (Dengan Form Kurasi Kepala Sekolah) -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i data-lucide="sparkles" class="w-4 h-4 text-sky-600"></i>
                <span>Karya &amp; Modul Ajar Guru</span>
            </h3>
            <span class="text-xs text-slate-400"><?= count($creativities) ?> Karya</span>
        </div>

        <?php if (empty($creativities)): ?>
            <p class="text-xs text-slate-400">Guru belum mengunggah karya modul ajar.</p>
        <?php endif; ?>

        <div class="space-y-3">
            <?php foreach ($creativities as $cr): ?>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 bg-slate-200 text-slate-800 rounded-md font-bold uppercase text-[10px]">
                                <?= str_replace('_', ' ', esc($cr['category'])) ?>
                            </span>
                            <span class="text-[11px] text-sky-700 font-semibold">
                                Fokus: <?= ucfirst(esc($cr['deep_learning_focus'])) ?>
                            </span>
                        </div>
                        <?php if (!empty($cr['is_featured'])): ?>
                            <span class="px-2.5 py-0.5 bg-amber-100 text-amber-900 border border-amber-300 rounded-full font-bold text-[10px] flex items-center gap-1">
                                <i data-lucide="award" class="w-3 h-3 text-amber-600"></i>
                                <span>Karya Terpilih Kurikulum</span>
                            </span>
                        <?php endif; ?>
                    </div>

                    <h4 class="font-bold text-slate-800 text-sm"><?= esc($cr['title']) ?></h4>
                    <p class="text-slate-600"><?= nl2br(esc($cr['description'])) ?></p>

                    <?php if (!empty($cr['file_attachment_url'])): ?>
                        <a href="<?= base_url($cr['file_attachment_url']) ?>" target="_blank"
                           class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:underline">
                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                            <span>Unduh Berkas Modul / LKPD</span>
                        </a>
                    <?php endif; ?>

                    <!-- Form Kurasi Kepala Sekolah -->
                    <form action="<?= base_url('supervisor/curate-creativity/' . $cr['id']) ?>" method="post" 
                          class="pt-2 border-t border-slate-200 flex flex-col sm:flex-row items-center gap-2">
                        <?= csrf_field() ?>
                        <input type="text" name="curator_notes" 
                               value="<?= esc($cr['curator_notes'] ?? '') ?>"
                               placeholder="Catatan telaah kelayakan modul ajar..."
                               class="flex-1 w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <input type="hidden" name="is_featured" value="<?= !empty($cr['is_featured']) ? '0' : '1' ?>">
                        <button type="submit" 
                                class="w-full sm:w-auto px-4 py-1.5 <?= !empty($cr['is_featured']) ? 'bg-slate-700 hover:bg-slate-800 text-white' : 'bg-amber-500 hover:bg-amber-600 text-slate-950' ?> rounded-xl text-xs font-bold transition flex items-center justify-center gap-1">
                            <i data-lucide="award" class="w-3.5 h-3.5"></i>
                            <span><?= !empty($cr['is_featured']) ? 'Batal Terpilih' : 'Sematkan Karya Terpilih' ?></span>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 6. Riwayat Refleksi Pedagogis Guru -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i data-lucide="book-open" class="w-4 h-4 text-indigo-700"></i>
                <span>Jurnal Refleksi KBM 4 Level Kemendikdasmen</span>
            </h3>
            <span class="text-xs text-slate-400"><?= count($reflections) ?> Refleksi</span>
        </div>

        <?php if (empty($reflections)): ?>
            <p class="text-xs text-slate-400">Guru belum menuliskan jurnal refleksi.</p>
        <?php endif; ?>

        <div class="space-y-3">
            <?php foreach ($reflections as $rf): ?>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 bg-indigo-100 text-indigo-800 font-bold rounded-lg text-[10px]">
                            Level <?= $rf['competency_level'] ?> Kemendikdasmen
                        </span>
                        <span class="text-[11px] text-slate-400"><?= date('d F Y', strtotime($rf['reflection_date'])) ?></span>
                    </div>

                    <div class="space-y-1">
                        <p class="text-slate-700"><strong class="text-blue-800">1. Situasi:</strong> <?= esc($rf['situation_analysis']) ?></p>
                        <p class="text-slate-700"><strong class="text-amber-800">2. Kendala:</strong> <?= esc($rf['challenge_identification']) ?></p>
                        <p class="text-slate-700"><strong class="text-emerald-800">3. Solusi KBM:</strong> <?= esc($rf['action_plan']) ?></p>
                    </div>

                    <!-- Input / Edit Coaching Feedback -->
                    <form action="<?= base_url('supervisor/feedback-reflection/' . $rf['id']) ?>" method="post" 
                          class="pt-2 border-t border-slate-200 space-y-2">
                        <?= csrf_field() ?>
                        <textarea name="principal_feedback" rows="2" required 
                                  placeholder="Berikan umpan balik dialog apresiatif & coaching..."
                                  class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"><?= esc($rf['principal_feedback'] ?? '') ?></textarea>
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="px-4 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                                <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                <span>Simpan Umpan Balik Coaching</span>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
