<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto pb-14 space-y-5">
    <div class="flex items-center gap-2 mb-2">
        <a href="<?= base_url('supervisor/teachers') ?>" class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-base font-bold text-slate-800">Instrumen Asesmen Supervisi KBM Klinis</h2>
            <p class="text-xs text-slate-500">Evaluasi Guru Berbasis Standar Kemendikdasmen & Paradigma Deep Learning</p>
        </div>
    </div>

    <!-- Banner Info Guru -->
    <div class="bg-gradient-to-r from-blue-800 to-indigo-900 text-white rounded-3xl p-5 shadow-sm flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <img src="<?= $teacher['avatar_url'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80' ?>" 
                 alt="Avatar" class="w-12 h-12 rounded-2xl object-cover border-2 border-sky-300 shadow">
            <div>
                <h3 class="font-bold text-sm text-white"><?= esc($teacher['full_name']) ?></h3>
                <p class="text-xs text-sky-200 font-mono">NIP: <?= esc($teacher['nip']) ?></p>
                <span class="text-[11px] text-sky-100 block mt-0.5"><?= esc($teacher['subject_specialty']) ?></span>
            </div>
        </div>
        <a href="<?= base_url('portfolio/' . $teacher['nip']) ?>" target="_blank"
           class="px-3 py-1.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-xs font-semibold text-white transition flex items-center gap-1 shrink-0">
            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
            <span>Portofolio</span>
        </a>
    </div>

    <!-- Form Instrumen Supervisi -->
    <form action="<?= base_url('supervisor/store-supervision') ?>" method="post" 
          class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-5">
        <?= csrf_field() ?>
        <input type="hidden" name="teacher_id" value="<?= esc($teacher['id']) ?>">

        <!-- Metadata KBM Observasi -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Kunjungan</label>
                <input type="date" name="supervision_date" value="<?= date('Y-m-d') ?>" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Rombel / Kelas</label>
                <input type="text" name="class_name" required placeholder="Contoh: X-1 / XI IPA 2"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mata Pelajaran</label>
                <input type="text" name="subject" required value="<?= esc($teacher['subject_specialty']) ?>"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>
        </div>

        <!-- Bagian 1: 4 Pilar Kompetensi Standar Nasional (1.0 - 4.0) -->
        <div class="space-y-4 pt-2">
            <div class="border-b border-blue-100 pb-1.5 flex items-center justify-between">
                <h3 class="font-bold text-xs text-blue-900 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="layers" class="w-4 h-4 text-blue-700"></i>
                    <span>1. Rubrik 4 Standar Kompetensi Guru (Skala 1.0 - 4.0)</span>
                </h3>
                <span class="text-[10px] text-slate-400">1: Berkembang | 2: Berdaya | 3: Mahir | 4: Pengimbas</span>
            </div>

            <!-- Pedagogik -->
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-1.5">
                <div class="flex justify-between text-xs">
                    <div>
                        <span class="font-bold text-slate-800">Kompetensi Pedagogik</span>
                        <p class="text-[11px] text-slate-500">Kesiapan modul ajar, diferensiasi konten/proses, asesmen diagnostik</p>
                    </div>
                    <span id="labelPedagogic" class="font-black text-blue-700 text-sm">3.5</span>
                </div>
                <input type="range" name="score_pedagogic" min="1.0" max="4.0" step="0.1" value="3.5"
                       oninput="document.getElementById('labelPedagogic').innerText = this.value"
                       class="w-full accent-blue-700">
            </div>

            <!-- Kepribadian -->
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-1.5">
                <div class="flex justify-between text-xs">
                    <div>
                        <span class="font-bold text-slate-800">Kompetensi Kepribadian</span>
                        <p class="text-[11px] text-slate-500">Keteladanan moral, budaya 5S, disiplin waktu, kestabilan emosi pendidik</p>
                    </div>
                    <span id="labelPersonality" class="font-black text-blue-700 text-sm">3.8</span>
                </div>
                <input type="range" name="score_personality" min="1.0" max="4.0" step="0.1" value="3.8"
                       oninput="document.getElementById('labelPersonality').innerText = this.value"
                       class="w-full accent-blue-700">
            </div>

            <!-- Sosial -->
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-1.5">
                <div class="flex justify-between text-xs">
                    <div>
                        <span class="font-bold text-slate-800">Kompetensi Sosial</span>
                        <p class="text-[11px] text-slate-500">Komunikasi empatik, iklim inklusif, pendampingan dialogis tanpa diskriminasi</p>
                    </div>
                    <span id="labelSocial" class="font-black text-blue-700 text-sm">3.6</span>
                </div>
                <input type="range" name="score_social" min="1.0" max="4.0" step="0.1" value="3.6"
                       oninput="document.getElementById('labelSocial').innerText = this.value"
                       class="w-full accent-blue-700">
            </div>

            <!-- Profesional -->
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-1.5">
                <div class="flex justify-between text-xs">
                    <div>
                        <span class="font-bold text-slate-800">Kompetensi Profesional</span>
                        <p class="text-[11px] text-slate-500">Penguasaan struktur materi keilmuan mutakhir dan relevansinya di era digital</p>
                    </div>
                    <span id="labelProfessional" class="font-black text-blue-700 text-sm">3.7</span>
                </div>
                <input type="range" name="score_professional" min="1.0" max="4.0" step="0.1" value="3.7"
                       oninput="document.getElementById('labelProfessional').innerText = this.value"
                       class="w-full accent-blue-700">
            </div>
        </div>

        <!-- Bagian 2: Indeks Keterlaksanaan Deep Learning (Mindful, Meaningful, Joyful) -->
        <div class="p-4 bg-sky-50/70 border border-sky-200 rounded-3xl space-y-4">
            <div class="flex items-center justify-between border-b border-sky-200 pb-2">
                <h3 class="font-bold text-xs text-sky-950 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="compass" class="w-4 h-4 text-sky-700"></i>
                    <span>2. Indeks Keterlaksanaan Deep Learning (1.0 - 4.0)</span>
                </h3>
                <div class="text-right">
                    <span class="text-[10px] text-sky-700 block">Rata-rata DL:</span>
                    <strong id="avgDLDisplay" class="text-sky-900 text-base font-black">3.70</strong>
                </div>
            </div>

            <!-- Mindful -->
            <div class="space-y-1">
                <div class="flex justify-between text-xs">
                    <span class="font-semibold text-sky-950">Mindful Learning (Kesadaran &amp; Keterlibatan Penuh)</span>
                    <span id="labelMindful" class="font-bold text-sky-800">3.7</span>
                </div>
                <input type="range" name="score_mindful" id="inputMindful" min="1.0" max="4.0" step="0.1" value="3.7"
                       oninput="updateDLCalc()" class="w-full accent-sky-600">
            </div>

            <!-- Meaningful -->
            <div class="space-y-1">
                <div class="flex justify-between text-xs">
                    <span class="font-semibold text-sky-950">Meaningful Learning (Kontekstualisasi Masalah Riil)</span>
                    <span id="labelMeaningful" class="font-bold text-sky-800">3.6</span>
                </div>
                <input type="range" name="score_meaningful" id="inputMeaningful" min="1.0" max="4.0" step="0.1" value="3.6"
                       oninput="updateDLCalc()" class="w-full accent-sky-600">
            </div>

            <!-- Joyful -->
            <div class="space-y-1">
                <div class="flex justify-between text-xs">
                    <span class="font-semibold text-sky-950">Joyful Learning (Suasana Menyenangkan &amp; Eksploratif)</span>
                    <span id="labelJoyful" class="font-bold text-sky-800">3.8</span>
                </div>
                <input type="range" name="score_joyful" id="inputJoyful" min="1.0" max="4.0" step="0.1" value="3.8"
                       oninput="updateDLCalc()" class="w-full accent-sky-600">
            </div>
        </div>

        <!-- Bagian 3: Catatan Coaching & Rekomendasi Tindak Lanjut -->
        <div class="space-y-3 pt-1">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Catatan Coaching &amp; Apresiasi Kepala Sekolah
                </label>
                <textarea name="coaching_notes" rows="3" required placeholder="Tuliskan kekuatan KBM guru yang teramati, dialog apresiatif, serta hal baik yang patut dipertahankan..."
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none leading-relaxed"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Rekomendasi Tindak Lanjut (Follow-up Action Plan)
                </label>
                <textarea name="followup_action" rows="2" placeholder="Contoh: Mengikuti sesi Lesson Study bersama tim MGMP sekolah, atau penyusunan asesmen formatif adaptif..."
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none leading-relaxed"></textarea>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-xl text-xs shadow-md transition flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>Terbitkan &amp; Simpan Hasil Supervisi KBM</span>
            </button>
        </div>
    </form>

    <!-- Riwayat Supervisi Guru Sebelumnya -->
    <?php if (!empty($history)): ?>
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="history" class="w-4 h-4 text-blue-700"></i>
                <span>Histori Supervisi Terdahulu untuk <?= esc($teacher['full_name']) ?></span>
            </h3>
            <div class="divide-y divide-slate-100 text-xs">
                <?php foreach ($history as $h): ?>
                    <div class="py-2.5 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800"><?= date('d M Y', strtotime($h['supervision_date'])) ?></span>
                            <span class="text-slate-500 block text-[11px]"><?= esc($h['class_name']) ?> &bull; <?= esc($h['subject']) ?></span>
                        </div>
                        <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-full font-bold text-[11px]">
                            Skor DL: <?= $h['deep_learning_score'] ?>/4.0
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function updateDLCalc() {
        const m = parseFloat(document.getElementById('inputMindful').value);
        const mn = parseFloat(document.getElementById('inputMeaningful').value);
        const j = parseFloat(document.getElementById('inputJoyful').value);

        document.getElementById('labelMindful').innerText = m.toFixed(1);
        document.getElementById('labelMeaningful').innerText = mn.toFixed(1);
        document.getElementById('labelJoyful').innerText = j.toFixed(1);

        const avg = ((m + mn + j) / 3).toFixed(2);
        document.getElementById('avgDLDisplay').innerText = avg;
    }
    updateDLCalc();
</script>
<?= $this->endSection() ?>
