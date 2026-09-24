<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto pb-14 space-y-4">
    <div class="flex items-center gap-2 mb-2">
        <a href="<?= base_url('reflection') ?>" class="p-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-base font-bold text-slate-800">Tulis Refleksi KBM Terstruktur</h2>
            <p class="text-xs text-slate-500">Model Matriks Evaluasi &amp; Penilaian Diri 4 Level Kemendikdasmen</p>
        </div>
    </div>

    <form action="<?= base_url('reflection/store') ?>" method="post" 
          class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-5">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal KBM</label>
                <input type="date" name="reflection_date" value="<?= date('Y-m-d') ?>" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tautkan ke Aktivitas KBM</label>
                <select name="activity_id"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="">-- Tanpa Tautan (Refleksi Umum) --</option>
                    <?php foreach ($activities as $act): ?>
                        <option value="<?= $act['id'] ?>"><?= esc($act['title']) ?> (<?= $act['activity_date'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- 4 Pertanyaan Pemandu PRD Kemendikdasmen -->
        <div class="p-4 bg-blue-50/60 border border-blue-200 rounded-2xl space-y-1.5">
            <label class="block text-xs font-bold text-blue-900 uppercase tracking-wider">
                1. Analisis Situasi KBM (What Happened?)
            </label>
            <p class="text-[11px] text-blue-700 leading-relaxed">
                Apakah siswa aktif berdiskusi (Mindful)? Apakah materi terhubung dengan persoalan riil kehidupan siswa (Meaningful)? Apakah atmosfer belajar menggembirakan (Joyful)?
            </p>
            <textarea name="situation_analysis" rows="3" required placeholder="Ceritakan keterlibatan siswa, respon emosional, dan jalannya pembelajaran..."
                      class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none leading-relaxed"></textarea>
        </div>

        <div class="p-4 bg-amber-50/60 border border-amber-200 rounded-2xl space-y-1.5">
            <label class="block text-xs font-bold text-amber-900 uppercase tracking-wider">
                2. Identifikasi Kendala &amp; Miskonsepsi (Challenge)
            </label>
            <p class="text-[11px] text-amber-700 leading-relaxed">
                Bagian konsep materi mana yang membuat siswa bingung, pasif, atau mengalami miskonsepsi saat diskusi kelompok?
            </p>
            <textarea name="challenge_identification" rows="3" required placeholder="Uraikan kendala pedagogis dan titik kesulitan belajar murid..."
                      class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none leading-relaxed"></textarea>
        </div>

        <div class="p-4 bg-emerald-50/60 border border-emerald-200 rounded-2xl space-y-1.5">
            <label class="block text-xs font-bold text-emerald-900 uppercase tracking-wider">
                3. Rencana Aksi Siklus Selanjutnya (Next Action Plan)
            </label>
            <p class="text-[11px] text-emerald-700 leading-relaxed">
                Modifikasi strategi diferensiasi, variasi media, atau lembar kerja apa yang konkret akan dicoba pada pertemuan berikutnya?
            </p>
            <textarea name="action_plan" rows="3" required placeholder="Tuliskan komitmen perbaikan instruksional untuk pertemuan mendatang..."
                      class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none leading-relaxed"></textarea>
        </div>

        <!-- 4 Level Matriks Penilaian Diri Kemendikdasmen -->
        <div>
            <label class="block text-xs font-semibold text-slate-800 uppercase tracking-wider mb-2">
                4. Self-Assessment: Level Refleksi Guru (Kemendikdasmen)
            </label>
            <div class="space-y-2.5 text-xs">
                <label class="flex items-start gap-3 p-3 border rounded-2xl cursor-pointer hover:bg-slate-50 border-slate-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/70 transition">
                    <input type="radio" name="competency_level" value="1" onchange="toggleMentorField(1)" class="mt-0.5 text-blue-600">
                    <div>
                        <strong class="text-slate-800 text-xs block">Level 1: Berkembang (Awareness)</strong>
                        <p class="text-[11px] text-slate-500 mt-0.5">Sadar terhadap kelemahan metode mengajar sendiri dan mulai peka pada kebutuhan beragam murid.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3 border rounded-2xl cursor-pointer hover:bg-slate-50 border-slate-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/70 transition">
                    <input type="radio" name="competency_level" value="2" onchange="toggleMentorField(2)" class="mt-0.5 text-blue-600">
                    <div>
                        <strong class="text-slate-800 text-xs block">Level 2: Berdaya (Adaptation)</strong>
                        <p class="text-[11px] text-slate-500 mt-0.5">Mampu merumuskan alternatif solusi konkret dan berani menguji inovasi metode baru di kelas.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3 border rounded-2xl cursor-pointer hover:bg-slate-50 border-slate-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/70 transition">
                    <input type="radio" name="competency_level" value="3" checked onchange="toggleMentorField(3)" class="mt-0.5 text-blue-600">
                    <div>
                        <strong class="text-slate-800 text-xs block">Level 3: Mahir (Impact Evaluation)</strong>
                        <p class="text-[11px] text-slate-500 mt-0.5">Mampu mengevaluasi dampak modifikasi instruksional terhadap pemahaman dan antusiasme siswa secara terukur.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3 border rounded-2xl cursor-pointer hover:bg-slate-50 border-slate-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/70 transition">
                    <input type="radio" name="competency_level" value="4" onchange="toggleMentorField(4)" class="mt-0.5 text-blue-600">
                    <div>
                        <strong class="text-slate-800 text-xs block text-purple-900">Level 4: Pengimbas (Replication &amp; Mentoring)</strong>
                        <p class="text-[11px] text-slate-500 mt-0.5">Mampu mendiseminasikan praktik baik (Good Practice), membimbing rekan sejawat, dan melakukan lesson study kolaboratif.</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Kolom Mentor Pengimbas (Muncul jika Level 4 dipilih) -->
        <div id="mentorContainer" class="p-4 bg-purple-50/70 border border-purple-200 rounded-2xl space-y-3 hidden">
            <h4 class="text-xs font-bold text-purple-950 uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="share-2" class="w-3.5 h-3.5 text-purple-700"></i>
                <span>Diseminasi Pengimbasan Rekan Sejawat</span>
            </h4>
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Rekan Guru yang Dibimbing / Menjadi Mitra Diskusi</label>
                <select name="peer_mentor_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs">
                    <option value="">-- Pilih Rekan Sejawat --</option>
                    <?php foreach ($peers as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= esc($p['full_name']) ?> (<?= esc($p['subject_specialty']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Link Bukti Praktik Baik (Drive / YouTube / Canva)</label>
                <input type="url" name="evidence_url" placeholder="https://..." class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs">
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-xl text-xs shadow-md transition flex items-center justify-center gap-2">
                <i data-lucide="send" class="w-4 h-4"></i>
                <span>Simpan &amp; Ajukan Refleksi ke Kepala Sekolah</span>
            </button>
        </div>
    </form>
</div>

<script>
    function toggleMentorField(lvl) {
        const c = document.getElementById('mentorContainer');
        if (lvl === 4) {
            c.classList.remove('hidden');
        } else {
            c.classList.add('hidden');
        }
    }
</script>
<?= $this->endSection() ?>
