<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-5xl mx-auto pb-14">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Kalender &amp; Jadwal Supervisi Akademik</h2>
            <p class="text-xs text-slate-500">Plotting Jadwal Kunjungan Kelas &amp; Observasi KBM Deep Learning</p>
        </div>
        <a href="#formSchedule" 
           class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow transition flex items-center gap-1.5 self-start sm:self-auto">
            <i data-lucide="calendar-plus" class="w-4 h-4"></i>
            <span>Tambah Jadwal Observasi</span>
        </a>
    </div>

    <!-- 1. Daftar Jadwal Terencana -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4 text-blue-700"></i>
                <span>Agenda Supervisi Kelas Terjadwal</span>
            </h3>
            <span class="text-xs text-slate-400"><?= count($schedules) ?> Agenda Terdaftar</span>
        </div>

        <?php if (empty($schedules)): ?>
            <div class="p-8 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl">
                Belum ada agenda supervisi akademik yang dijadwalkan. Gunakan formulir di bawah untuk menambah jadwal baru.
            </div>
        <?php endif; ?>

        <div class="divide-y divide-slate-100 text-xs">
            <?php foreach ($schedules as $sc): ?>
                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800 text-sm"><?= esc($sc['profiles']['full_name'] ?? 'Guru') ?></span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                <?php 
                                    switch($sc['status']) {
                                        case 'completed': echo 'bg-emerald-100 text-emerald-800'; break;
                                        case 'rescheduled': echo 'bg-amber-100 text-amber-800'; break;
                                        case 'cancelled': echo 'bg-rose-100 text-rose-800'; break;
                                        default: echo 'bg-blue-100 text-blue-800'; break;
                                    }
                                ?>">
                                <?= esc($sc['status']) ?>
                            </span>
                        </div>
                        <div class="text-slate-500 text-[11px] flex flex-wrap items-center gap-3">
                            <span class="flex items-center gap-1">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                                <?= date('d M Y', strtotime($sc['scheduled_date'])) ?> (<?= substr($sc['start_time'], 0, 5) ?> - <?= substr($sc['end_time'], 0, 5) ?> WIB)
                            </span>
                            <span>&bull;</span>
                            <span>Kelas: <strong><?= esc($sc['class_name']) ?></strong></span>
                            <span>&bull;</span>
                            <span>Mapel: <strong><?= esc($sc['subject']) ?></strong></span>
                        </div>
                        <?php if (!empty($sc['target_topic'])): ?>
                            <p class="text-slate-600 text-[11px]">
                                Target Materi: <span class="italic text-slate-800 font-medium">"<?= esc($sc['target_topic']) ?>"</span>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <?php if ($sc['status'] === 'scheduled'): ?>
                            <a href="<?= base_url('supervisor/supervise/' . $sc['teacher_id']) ?>" 
                               class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-xs">
                                <i data-lucide="play" class="w-3.5 h-3.5"></i>
                                <span>Mulai Asesmen</span>
                            </a>
                        <?php endif; ?>

                        <!-- Update Status Form -->
                        <form action="<?= base_url('supervisor/schedules/update-status/' . $sc['id']) ?>" method="post" class="inline">
                            <?= csrf_field() ?>
                            <select name="status" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-[11px] font-semibold text-slate-700">
                                <option value="scheduled" <?= $sc['status'] === 'scheduled' ? 'selected' : '' ?>>Terjadwal</option>
                                <option value="completed" <?= $sc['status'] === 'completed' ? 'selected' : '' ?>>Selesai</option>
                                <option value="rescheduled" <?= $sc['status'] === 'rescheduled' ? 'selected' : '' ?>>Reschedule</option>
                                <option value="cancelled" <?= $sc['status'] === 'cancelled' ? 'selected' : '' ?>>Batal</option>
                            </select>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 2. Formulir Tambah Jadwal Baru -->
    <div id="formSchedule" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-2">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-blue-700"></i>
                <span>Formulir Penetapan Jadwal Supervisi Kelas</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Tentukan guru target, waktu kunjungan, dan fokus materi observasi pembelajaran.</p>
        </div>

        <form action="<?= base_url('supervisor/schedules/store') ?>" method="post" class="space-y-4 text-xs">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Pilih Guru Target</label>
                    <select name="teacher_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <option value="">-- Pilih Dewan Guru --</option>
                        <?php foreach ($gurus as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= esc($g['full_name']) ?> (<?= esc($g['subject_specialty']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Kunjungan</label>
                    <input type="date" name="scheduled_date" value="<?= date('Y-m-d') ?>" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Jam Mulai</label>
                    <input type="time" name="start_time" value="08:00" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Jam Selesai</label>
                    <input type="time" name="end_time" value="09:30" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Rombel / Kelas</label>
                    <input type="text" name="class_name" required placeholder="Contoh: X-1 / Fase E" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Mata Pelajaran</label>
                    <input type="text" name="subject" required placeholder="Contoh: Matematika / Informatika" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Topik / Materi Pokok Observasi</label>
                <input type="text" name="target_topic" placeholder="Contoh: Pembelajaran Berdiferensiasi pada Eksponensial" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Tambahan untuk Guru (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Tuliskan petunjuk kesiapan RPP / modul ajar yang perlu disiapkan guru..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                    <i data-lucide="calendar-check" class="w-4 h-4"></i>
                    <span>Tetapkan &amp; Jadwalkan Supervisi</span>
                </button>
            </div>
        </form>
    </div>

</div>
<?= $this->endSection() ?>
