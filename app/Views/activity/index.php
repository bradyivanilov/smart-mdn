<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-4 max-w-xl mx-auto pb-10">

    <!-- Header bar -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Our Activity</h2>
            <p class="text-xs text-slate-500">Jurnal Harian 4 Pilar Kompetensi Guru</p>
        </div>
        <a href="<?= base_url('activity/create') ?>" 
           class="bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold py-2 px-3.5 rounded-xl shadow transition flex items-center gap-1.5">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Tambah Jurnal</span>
        </a>
    </div>

    <!-- Filter chips kompetensi -->
    <div class="flex gap-2 overflow-x-auto pb-1 text-xs no-scrollbar">
        <button onclick="filterComp('all')" class="filter-btn active px-3 py-1.5 rounded-full font-semibold bg-blue-700 text-white">Semua</button>
        <button onclick="filterComp('pedagogik')" class="filter-btn px-3 py-1.5 rounded-full font-semibold bg-white border border-slate-200 text-slate-600">Pedagogik</button>
        <button onclick="filterComp('kepribadian')" class="filter-btn px-3 py-1.5 rounded-full font-semibold bg-white border border-slate-200 text-slate-600">Kepribadian</button>
        <button onclick="filterComp('sosial')" class="filter-btn px-3 py-1.5 rounded-full font-semibold bg-white border border-slate-200 text-slate-600">Sosial</button>
        <button onclick="filterComp('profesional')" class="filter-btn px-3 py-1.5 rounded-full font-semibold bg-white border border-slate-200 text-slate-600">Profesional</button>
    </div>

    <!-- Timeline Activity Items -->
    <div class="space-y-3" id="activityList">
        <?php if (empty($activities)): ?>
            <div class="bg-white rounded-3xl p-8 text-center border border-slate-200">
                <i data-lucide="clipboard-x" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-slate-600">Belum ada jurnal aktivitas</p>
                <p class="text-xs text-slate-400 mt-1">Mulai catat aktivitas KBM harian Anda hari ini.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($activities as $act): ?>
            <div class="activity-card bg-white rounded-2xl p-4 border border-slate-200 shadow-sm transition hover:shadow-md" data-comp="<?= $act['competency_type'] ?>">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <!-- Badge Kompetensi -->
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            <?php 
                                switch($act['competency_type']) {
                                    case 'pedagogik': echo 'bg-blue-100 text-blue-800 border border-blue-200'; break;
                                    case 'kepribadian': echo 'bg-emerald-100 text-emerald-800 border border-emerald-200'; break;
                                    case 'sosial': echo 'bg-amber-100 text-amber-800 border border-amber-200'; break;
                                    case 'profesional': echo 'bg-purple-100 text-purple-800 border border-purple-200'; break;
                                }
                            ?>">
                            <?= esc($act['competency_type']) ?>
                        </span>

                        <!-- Badge Deep Learning Pillar (PRD 8.2) -->
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                            <?php 
                                switch($act['deep_learning_pillar']) {
                                    case 'mindful': echo 'bg-blue-50 text-blue-700 border border-blue-200'; break;
                                    case 'meaningful': echo 'bg-indigo-50 text-indigo-700 border border-indigo-200'; break;
                                    case 'joyful': echo 'bg-sky-50 text-sky-700 border border-sky-200'; break;
                                    default: echo 'bg-slate-100 text-slate-600'; break;
                                }
                            ?>">
                            <?= ucfirst(esc($act['deep_learning_pillar'])) ?>
                        </span>
                    </div>

                    <span class="text-[11px] text-slate-400 font-medium">
                        <?= date('d M Y', strtotime($act['activity_date'])) ?>
                    </span>
                </div>

                <h3 class="font-bold text-sm text-slate-800 mt-2 leading-snug">
                    <?= esc($act['title']) ?>
                </h3>

                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    <?= nl2br(esc($act['description'])) ?>
                </p>

                <?php if (!empty($act['evidence_file_url'])): ?>
                    <div class="mt-2.5">
                        <a href="<?= base_url($act['evidence_file_url']) ?>" target="_blank" 
                           class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg border border-blue-200">
                            <i data-lucide="paperclip" class="w-3 h-3"></i>
                            <span>Lihat Bukti Lampiran</span>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                    <span class="text-slate-400 flex items-center gap-1">
                        <i data-lucide="shield-check" class="w-3 h-3 text-slate-400"></i>
                        <span>Status Verifikasi:</span>
                    </span>
                    <span class="font-bold 
                        <?= ($act['verification_status'] === 'approved') ? 'text-emerald-600' : ($act['verification_status'] === 'revision' ? 'text-rose-600' : 'text-amber-600') ?>">
                        <?= strtoupper(esc($act['verification_status'])) ?>
                    </span>
                </div>

                <?php if (!empty($act['verification_notes'])): ?>
                    <div class="mt-2 p-2.5 <?= ($act['verification_status'] === 'revision') ? 'bg-rose-50 border border-rose-200 text-rose-800' : 'bg-blue-50 border border-blue-200 text-blue-800' ?> rounded-xl text-xs">
                        <strong class="block text-[10px] uppercase font-bold mb-0.5">Catatan Kepala Sekolah:</strong>
                        <span><?= esc($act['verification_notes']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function filterComp(type) {
        const cards = document.querySelectorAll('.activity-card');
        const buttons = document.querySelectorAll('.filter-btn');

        buttons.forEach(btn => {
            btn.classList.remove('bg-blue-700', 'text-white');
            btn.classList.add('bg-white', 'text-slate-600');
        });
        event.target.classList.add('bg-blue-700', 'text-white');
        event.target.classList.remove('bg-white', 'text-slate-600');

        cards.forEach(card => {
            if (type === 'all' || card.getAttribute('data-comp') === type) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
<?= $this->endSection() ?>
