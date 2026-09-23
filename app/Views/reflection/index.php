<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-xl mx-auto pb-10">

    <!-- Header bar -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Our Refleksi</h2>
            <p class="text-xs text-slate-500">Model Refleksi 4 Level Kemendikdasmen</p>
        </div>
        <a href="<?= base_url('reflection/create') ?>" 
           class="bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold py-2 px-3.5 rounded-xl shadow transition flex items-center gap-1.5">
            <i data-lucide="pen-tool" class="w-4 h-4"></i>
            <span>Tulis Refleksi</span>
        </a>
    </div>

    <!-- List Jurnal Refleksi -->
    <div class="space-y-4">
        <?php if (empty($reflections)): ?>
            <div class="bg-white rounded-3xl p-8 text-center border border-slate-200">
                <i data-lucide="book-open" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-slate-600">Belum ada jurnal refleksi</p>
                <p class="text-xs text-slate-400 mt-1">Refleksikan KBM Anda untuk meningkatkan kompetensi berkala.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($reflections as $ref): ?>
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <!-- Badge Level Kemendikdasmen (PRD Section 2) -->
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                        <?php 
                            switch($ref['competency_level']) {
                                case 1: echo 'bg-amber-100 text-amber-800 border border-amber-200'; break;
                                case 2: echo 'bg-blue-100 text-blue-800 border border-blue-200'; break;
                                case 3: echo 'bg-indigo-100 text-indigo-800 border border-indigo-200'; break;
                                case 4: echo 'bg-emerald-100 text-emerald-800 border border-emerald-200'; break;
                            }
                        ?>">
                        Level <?= $ref['competency_level'] ?>: 
                        <?php 
                            switch($ref['competency_level']) {
                                case 1: echo 'Berkembang'; break;
                                case 2: echo 'Berdaya'; break;
                                case 3: echo 'Mahir'; break;
                                case 4: echo 'Pengimbas'; break;
                            }
                        ?>
                    </span>

                    <span class="text-[11px] text-slate-400">
                        <?= date('d M Y', strtotime($ref['reflection_date'])) ?>
                    </span>
                </div>

                <!-- 1. Situasi -->
                <div class="text-xs">
                    <span class="font-bold text-slate-700 block text-[11px] uppercase tracking-wider text-blue-800">1. Analisis Situasi KBM (Mindful / Meaningful / Joyful)</span>
                    <p class="text-slate-600 mt-0.5 leading-relaxed"><?= nl2br(esc($ref['situation_analysis'])) ?></p>
                </div>

                <!-- 2. Kendala / Tantangan -->
                <div class="text-xs">
                    <span class="font-bold text-slate-700 block text-[11px] uppercase tracking-wider text-amber-800">2. Miskonsepsi & Kendala Siswa</span>
                    <p class="text-slate-600 mt-0.5 leading-relaxed"><?= nl2br(esc($ref['challenge_identification'])) ?></p>
                </div>

                <!-- 3. Rencana Aksi Konkret -->
                <div class="text-xs">
                    <span class="font-bold text-slate-700 block text-[11px] uppercase tracking-wider text-emerald-800">3. Rencana Aksi Solusi Siklus Selanjutnya</span>
                    <p class="text-slate-600 mt-0.5 leading-relaxed"><?= nl2br(esc($ref['action_plan'])) ?></p>
                </div>

                <!-- Feedback Kepala Sekolah bila ada -->
                <?php if (!empty($ref['principal_feedback'])): ?>
                    <div class="p-3.5 bg-blue-50/70 border border-blue-200/80 rounded-2xl text-xs mt-2">
                        <div class="flex items-center gap-1.5 font-bold text-blue-900 text-[11px] uppercase mb-1">
                            <i data-lucide="message-square" class="w-3.5 h-3.5 text-blue-600"></i>
                            <span>Catatan & Umpan Balik Kepala Sekolah</span>
                        </div>
                        <p class="text-slate-700 italic"><?= nl2br(esc($ref['principal_feedback'])) ?></p>
                    </div>
                <?php else: ?>
                    <div class="text-[11px] text-slate-400 italic flex items-center gap-1 pt-1">
                        <i data-lucide="clock" class="w-3 h-3 text-slate-400"></i>
                        <span>Menunggu telaah & catatan apresiasi Kepala Sekolah</span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

</div>
<?= $this->endSection() ?>
