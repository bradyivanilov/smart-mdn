<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-5xl mx-auto pb-14">

    <!-- Header bar -->
    <div>
        <h2 class="text-lg font-bold text-slate-800">Pelacak Rencana Tindak Lanjut Coaching (RTL)</h2>
        <p class="text-xs text-slate-500">Monitoring Komitmen Perbaikan Pedagogis Guru Pasca-Supervisi Klinis</p>
    </div>

    <!-- Status Filter / Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-rose-600">
                <?= count(array_filter($plans, fn($p) => ($p['status'] ?? '') === 'open')) ?>
            </span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Tindakan Open / Belum Selesai</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-amber-600">
                <?= count(array_filter($plans, fn($p) => ($p['status'] ?? '') === 'in_progress')) ?>
            </span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Dalam Pengerjaan (In Progress)</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="block text-2xl font-black text-emerald-600">
                <?= count(array_filter($plans, fn($p) => ($p['status'] ?? '') === 'resolved')) ?>
            </span>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Tuntas &amp; Terverifikasi</span>
        </div>
    </div>

    <!-- Tabel Daftar Tindak Lanjut RTL -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm">Daftar Rencana Aksi Guru</h3>
            <span class="text-xs text-slate-400"><?= count($plans) ?> Butir Tindak Lanjut</span>
        </div>

        <?php if (empty($plans)): ?>
            <div class="p-8 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl">
                Belum ada butir rencana tindak lanjut (RTL) yang ditetapkan. Terbitkan butir RTL saat melakukan asesmen supervisi KBM.
            </div>
        <?php endif; ?>

        <div class="space-y-3.5">
            <?php foreach ($plans as $pl): ?>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-2.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <div>
                            <a href="<?= base_url('supervisor/teacher/' . $pl['teacher_id']) ?>" class="font-bold text-slate-800 text-sm hover:text-blue-700">
                                <?= esc($pl['profiles']['full_name'] ?? 'Guru') ?>
                            </a>
                            <span class="text-[11px] text-slate-500 block">NIP: <?= esc($pl['profiles']['nip'] ?? '-') ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-slate-500">
                                Tenggat: <strong class="<?= (strtotime($pl['deadline']) < time() && $pl['status'] !== 'resolved') ? 'text-rose-600' : 'text-slate-700' ?>"><?= date('d M Y', strtotime($pl['deadline'])) ?></strong>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase
                                <?php 
                                    switch($pl['status']) {
                                        case 'resolved': echo 'bg-emerald-100 text-emerald-800'; break;
                                        case 'in_progress': echo 'bg-amber-100 text-amber-800'; break;
                                        default: echo 'bg-rose-100 text-rose-800'; break;
                                    }
                                ?>">
                                <?= esc($pl['status']) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Action Item -->
                    <div class="p-3 bg-white rounded-xl border border-slate-200 text-slate-800 leading-relaxed">
                        <strong class="text-blue-900 block text-[10px] uppercase font-bold mb-0.5">Komitmen Tindakan Perbaikan:</strong>
                        <?= nl2br(esc($pl['action_item'])) ?>
                    </div>

                    <?php if (!empty($pl['teacher_notes'])): ?>
                        <div class="p-2.5 bg-sky-50 rounded-xl border border-sky-200 text-sky-950">
                            <strong class="block text-[10px] uppercase font-bold mb-0.5">Laporan Progres Guru:</strong>
                            <p class="italic"><?= esc($pl['teacher_notes']) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Form Verifikasi Kepala Sekolah -->
                    <form action="<?= base_url('supervisor/coaching-plans/verify/' . $pl['id']) ?>" method="post" 
                          class="pt-2 border-t border-slate-200 flex flex-col sm:flex-row items-center gap-2">
                        <?= csrf_field() ?>
                        <input type="text" name="supervisor_verification" 
                               value="<?= esc($pl['supervisor_verification'] ?? '') ?>"
                               placeholder="Catatan verifikasi penuntasan oleh Kepala Sekolah..."
                               class="flex-1 w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <select name="status" class="px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-700">
                            <option value="open" <?= $pl['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                            <option value="in_progress" <?= $pl['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="resolved" <?= $pl['status'] === 'resolved' ? 'selected' : '' ?>>Resolved (Tuntas)</option>
                        </select>
                        <button type="submit" class="px-4 py-1.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl text-xs transition shrink-0">
                            Simpan Verifikasi
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
