<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-xl mx-auto pb-10">
    <div class="flex items-center gap-2 mb-4">
        <a href="<?= base_url('reflection') ?>" class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <h2 class="text-base font-bold text-slate-800">Tulis Refleksi KBM Terstruktur</h2>
    </div>

    <form action="<?= base_url('reflection/store') ?>" method="post" 
          class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-4">
        <?= csrf_field() ?>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal KBM</label>
                <input type="date" name="reflection_date" value="<?= date('Y-m-d') ?>" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tautkan ke Aktivitas</label>
                <select name="activity_id"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="">-- Pilih Aktivitas KBM --</option>
                    <?php foreach ($activities as $act): ?>
                        <option value="<?= $act['id'] ?>"><?= esc($act['title']) ?> (<?= $act['activity_date'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- 4 Pertanyaan Pemandu PRD Section 4 Modul 4 -->
        <div>
            <label class="block text-xs font-semibold text-blue-900 uppercase tracking-wider mb-1">
                1. Situasi KBM (What Happened?)
            </label>
            <p class="text-[11px] text-slate-500 mb-1.5">Apakah siswa aktif berdiskusi (Mindful)? Apakah materi terhubung kasus riil (Meaningful)? Apakah suasana kelas menggembirakan (Joyful)?</p>
            <textarea name="situation_analysis" rows="3" required placeholder="Ceritakan dinamika kelas dan keterlibatan aktif siswa..."
                      class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none leading-relaxed"></textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-amber-900 uppercase tracking-wider mb-1">
                2. Evaluasi Masalah & Miskonsepsi (Challenge)
            </label>
            <p class="text-[11px] text-slate-500 mb-1.5">Bagian konsep materi mana yang membuat siswa bingung atau mengalami miskonsepsi?</p>
            <textarea name="challenge_identification" rows="3" required placeholder="Tuliskan kendala pemahaman atau dinamika kelompok belajar..."
                      class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none leading-relaxed"></textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-emerald-900 uppercase tracking-wider mb-1">
                3. Aksi Perbaikan Siklus Selanjutnya (Next Action)
            </label>
            <p class="text-[11px] text-slate-500 mb-1.5">Modifikasi strategi pedagogis apa yang akan dicoba pada pertemuan berikutnya?</p>
            <textarea name="action_plan" rows="3" required placeholder="Langkah konkrit diferensiasi, media atau asesmen pemulihan..."
                      class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none leading-relaxed"></textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                4. Self-Assessment: Level Kompetensi Guru (Kemendikdasmen)
            </label>
            <div class="space-y-2 text-xs">
                <label class="flex items-start gap-2.5 p-2.5 border rounded-2xl cursor-pointer hover:bg-slate-50 border-slate-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                    <input type="radio" name="competency_level" value="1" class="mt-0.5 text-blue-600">
                    <div>
                        <strong class="text-slate-800">Level 1: Berkembang</strong>
                        <p class="text-[11px] text-slate-500">Menyadari kelemahan metode sendiri & mulai merefleksikan kebutuhan murid.</p>
                    </div>
                </label>

                <label class="flex items-start gap-2.5 p-2.5 border rounded-2xl cursor-pointer hover:bg-slate-50 border-slate-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                    <input type="radio" name="competency_level" value="2" class="mt-0.5 text-blue-600">
                    <div>
                        <strong class="text-slate-800">Level 2: Berdaya</strong>
                        <p class="text-[11px] text-slate-500">Mampu mengidentifikasi solusi konkret dan mencoba inovasi metode baru di kelas.</p>
                    </div>
                </label>

                <label class="flex items-start gap-2.5 p-2.5 border rounded-2xl cursor-pointer hover:bg-slate-50 border-slate-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                    <input type="radio" name="competency_level" value="3" checked class="mt-0.5 text-blue-600">
                    <div>
                        <strong class="text-slate-800">Level 3: Mahir</strong>
                        <p class="text-[11px] text-slate-500">Mampu mengevaluasi dampak metode terhadap capaian siswa secara terukur.</p>
                    </div>
                </label>

                <label class="flex items-start gap-2.5 p-2.5 border rounded-2xl cursor-pointer hover:bg-slate-50 border-slate-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                    <input type="radio" name="competency_level" value="4" class="mt-0.5 text-blue-600">
                    <div>
                        <strong class="text-slate-800">Level 4: Pengimbas</strong>
                        <p class="text-[11px] text-slate-500">Mampu membimbing rekan guru sejawat dan mereplikasi praktik baik lintas komunitas.</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2.5 px-4 rounded-xl text-xs shadow-md transition flex items-center justify-center gap-2">
                <i data-lucide="send" class="w-4 h-4"></i>
                <span>Simpan & Kirim Refleksi</span>
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
