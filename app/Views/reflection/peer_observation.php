<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-4xl mx-auto pb-14">

    <!-- Header bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Peer Observation (Lesson Study Antarguru)</h2>
            <p class="text-xs text-slate-500">Mewujudkan Kolaborasi &amp; Budaya Pengimbasan Praktik Baik Sejawat (Level 4)</p>
        </div>
        <a href="#formObservation" 
           class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow transition flex items-center gap-1.5 self-start sm:self-auto">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Tulis Observasi Baru</span>
        </a>
    </div>

    <!-- 1. Catatan Observasi yang Saya Terima dari Rekan -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="inbox" class="w-4 h-4 text-emerald-600"></i>
                <span>Masukan &amp; Catatan dari Rekan Pengamat Kelas Saya</span>
            </h3>
            <span class="text-xs text-slate-400"><?= count($receivedObservations) ?> Observasi Masuk</span>
        </div>

        <?php if (empty($receivedObservations)): ?>
            <div class="p-6 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl">
                Belum ada rekan guru yang mengirimkan lembar observasi kelas Anda.
            </div>
        <?php endif; ?>

        <div class="space-y-3">
            <?php foreach ($receivedObservations as $ro): ?>
                <div class="p-4 bg-emerald-50/50 rounded-2xl border border-emerald-200 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-emerald-950 text-sm">
                            Pengamat: <?= esc($ro['profiles']['full_name'] ?? 'Rekan Guru') ?>
                        </span>
                        <span class="text-[11px] text-slate-400"><?= date('d F Y', strtotime($ro['observation_date'])) ?></span>
                    </div>
                    <div class="text-[11px] text-slate-500">
                        Kelas: <strong><?= esc($ro['class_name']) ?></strong> &bull; Mapel: <strong><?= esc($ro['subject']) ?></strong>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 pt-1 text-[11px]">
                        <?php if (!empty($ro['mindful_notes'])): ?>
                            <div class="p-2 bg-white rounded-xl border border-slate-200">
                                <strong class="text-blue-800 block">Aspek Mindful:</strong>
                                <span class="text-slate-600"><?= esc($ro['mindful_notes']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($ro['meaningful_notes'])): ?>
                            <div class="p-2 bg-white rounded-xl border border-slate-200">
                                <strong class="text-indigo-800 block">Aspek Meaningful:</strong>
                                <span class="text-slate-600"><?= esc($ro['meaningful_notes']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($ro['joyful_notes'])): ?>
                            <div class="p-2 bg-white rounded-xl border border-slate-200">
                                <strong class="text-sky-800 block">Aspek Joyful:</strong>
                                <span class="text-slate-600"><?= esc($ro['joyful_notes']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-2 border-t border-emerald-200 text-slate-800">
                        <strong class="text-emerald-900 block text-[11px] uppercase">Rekomendasi Konstruktif Rekan:</strong>
                        <p class="italic bg-white p-2.5 rounded-xl border border-slate-200 mt-1">
                            "<?= esc($ro['constructive_feedback']) ?>"
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 2. Formulir Observasi Rekan Sejawat Baru -->
    <div id="formObservation" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-2">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i data-lucide="clipboard-edit" class="w-4 h-4 text-blue-700"></i>
                <span>Formulir Observasi Teman Sejawat (Tandem Teaching)</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Catat temuan positif dan masukan apresiatif saat menghadiri kelas rekan sejawat.</p>
        </div>

        <form action="<?= base_url('reflection/peer-observation/store') ?>" method="post" class="space-y-4 text-xs">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Guru yang Diobservasi</label>
                    <select name="host_teacher_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <option value="">-- Pilih Rekan Guru --</option>
                        <?php foreach ($gurus as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= esc($g['full_name']) ?> (<?= esc($g['subject_specialty']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Kunjungan</label>
                    <input type="date" name="observation_date" value="<?= date('Y-m-d') ?>" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Kelas / Rombel</label>
                    <input type="text" name="class_name" required placeholder="Contoh: X-1 / Fase E" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Mata Pelajaran</label>
                    <input type="text" name="subject" required placeholder="Contoh: Matematika" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>
            </div>

            <!-- 3 Catatan Aspek Deep Learning -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                <div>
                    <label class="block font-bold text-blue-900 mb-1">1. Aspek Mindful</label>
                    <textarea name="mindful_notes" rows="2" placeholder="Fokus & keterlibatan siswa..." class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
                </div>
                <div>
                    <label class="block font-bold text-indigo-900 mb-1">2. Aspek Meaningful</label>
                    <textarea name="meaningful_notes" rows="2" placeholder="Relevansi kasus nyata..." class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
                </div>
                <div>
                    <label class="block font-bold text-sky-900 mb-1">3. Aspek Joyful</label>
                    <textarea name="joyful_notes" rows="2" placeholder="Atmosfer menyenangkan..." class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1">Umpan Balik Konstruktif &amp; Apresiasi Praktik Baik</label>
                <textarea name="constructive_feedback" rows="3" required placeholder="Tuliskan kekuatan yang menginspirasi Anda dan saran perbaikan yang membangun..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none leading-relaxed"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Kirim Hasil Observasi ke Rekan Guru</span>
                </button>
            </div>
        </form>
    </div>

    <!-- 3. Riwayat Observasi yang Pernah Saya Lakukan -->
    <?php if (!empty($myObservations)): ?>
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-3">
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">
                Histori Observasi yang Saya Lakukan ke Rekan Lain (<?= count($myObservations) ?>)
            </h3>
            <div class="divide-y divide-slate-100 text-xs">
                <?php foreach ($myObservations as $mo): ?>
                    <div class="py-2.5 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800"><?= esc($mo['profiles']['full_name'] ?? 'Guru') ?></span>
                            <span class="text-slate-500 block text-[11px]"><?= esc($mo['class_name']) ?> - <?= esc($mo['subject']) ?> (<?= date('d M Y', strtotime($mo['observation_date'])) ?>)</span>
                        </div>
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full font-bold text-[10px]">
                            Terkirim
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
