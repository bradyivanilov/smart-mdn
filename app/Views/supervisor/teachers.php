<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-5 max-w-4xl mx-auto pb-12">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Daftar Pendidik (Guru)</h2>
            <p class="text-xs text-slate-500">Monitoring & Supervisi Kinerja Guru Deep Learning</p>
        </div>
        <a href="<?= base_url('supervisor') ?>" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php if (empty($teachers)): ?>
            <div class="col-span-2 bg-white rounded-3xl p-8 text-center border border-slate-200 text-slate-400 text-xs">
                Belum ada guru yang terdaftar dalam sistem.
            </div>
        <?php endif; ?>

        <?php foreach ($teachers as $t): ?>
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                <div class="flex items-start gap-3">
                    <img src="<?= $t['avatar_url'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80' ?>" 
                         alt="Avatar" class="w-12 h-12 rounded-2xl object-cover border-2 border-blue-200 shadow">
                    <div>
                        <h3 class="font-bold text-sm text-slate-800"><?= esc($t['full_name']) ?></h3>
                        <p class="text-xs text-slate-500 font-mono">NIP: <?= esc($t['nip']) ?></p>
                        <span class="inline-block mt-1 px-2.5 py-0.5 bg-blue-50 text-blue-700 rounded-md text-[10px] font-semibold border border-blue-100">
                            <?= esc($t['subject_specialty'] ?: 'Mata Pelajaran Belum Diisi') ?>
                        </span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="<?= base_url('portfolio/' . $t['nip']) ?>" target="_blank"
                       class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold flex items-center gap-1 transition">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        <span>Portofolio</span>
                    </a>

                    <a href="<?= base_url('supervisor/supervise/' . $t['id']) ?>" 
                       class="px-3.5 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow flex items-center gap-1.5 transition">
                        <i data-lucide="clipboard-check" class="w-3.5 h-3.5"></i>
                        <span>Asesmen Supervisi</span>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>
