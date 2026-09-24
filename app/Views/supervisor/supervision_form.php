<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-5 max-w-6xl mx-auto pb-14">
    <div class="flex items-center gap-2 mb-2">
        <a href="<?= base_url('supervisor/teachers') ?>" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-base font-bold text-slate-800">Instrumen Supervisi Klinis KBM (Split-Screen Observasi)</h2>
            <p class="text-xs text-slate-500">Evaluasi Guru Berbasis Standar Kemendikdasmen &amp; Paradigma Deep Learning</p>
        </div>
    </div>

    <!-- Banner Info Guru -->
    <div class="bg-gradient-to-r from-blue-900 to-indigo-950 text-white rounded-3xl p-5 shadow-sm flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <img src="<?= $teacher['avatar_url'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80' ?>" 
                 alt="Avatar" class="w-12 h-12 rounded-2xl object-cover border-2 border-sky-300 shadow">
            <div>
                <h3 class="font-bold text-sm text-white"><?= esc($teacher['full_name']) ?></h3>
                <p class="text-xs text-sky-200 font-mono">NIP: <?= esc($teacher['nip']) ?></p>
                <span class="text-[11px] text-sky-100 block mt-0.5"><?= esc($teacher['subject_specialty']) ?></span>
            </div>
        </div>
        <a href="<?= base_url('supervisor/teacher/' . $teacher['id']) ?>" 
           class="px-3.5 py-1.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-xs font-semibold text-white transition flex items-center gap-1.5 shrink-0">
            <i data-lucide="folder-search" class="w-3.5 h-3.5"></i>
            <span>Buka Audit Lengkap Guru</span>
        </a>
    </div>

    <!-- SPLIT SCREEN LAYOUT: KIRI DOKUMEN MODUL / JURNAL, KANAN RUBRIK ASESMEN -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

        <!-- PANEL KIRI (5 Kolom): Preview Modul & KBM Referensi Guru -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-4 sticky top-20">
            <div class="border-b border-slate-100 pb-2">
                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                    <i data-lucide="book-open" class="w-4 h-4 text-blue-700"></i>
                    <span>Referensi Dokumen / Modul KBM Guru</span>
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Panduan pegangan materi guru yang diobservasi.</p>
            </div>

            <!-- Preview Card Modul Ajar -->
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-2">
                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded font-bold text-[10px] uppercase">
                    Modul Ajar Deep Learning
                </span>
                <h4 class="font-bold text-slate-800 text-sm">Target Kelas &amp; Mapel</h4>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    Pastikan guru telah menyusun rancangan aktivitas berbasis 3 pilar: 
                    <strong class="text-blue-700">Mindful</strong> (diferensiasi), 
                    <strong class="text-indigo-700">Meaningful</strong> (relevansi dunia nyata), dan 
                    <strong class="text-sky-700">Joyful</strong> (iklim kelas tanpa tekanan).
                </p>

                <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-[11px]">
                    <span class="text-slate-500">Mata Pelajaran:</span>
                    <strong class="text-slate-800"><?= esc($teacher['subject_specialty'] ?: 'Mata Pelajaran Terdaftar') ?></strong>
                </div>
            </div>

            <!-- Catatan Histori Observasi Terdahulu -->
            <?php if (!empty($history)): ?>
                <div class="space-y-2 pt-1">
                    <span class="text-[11px] font-bold text-slate-700 block uppercase tracking-wider">Histori Observasi Terdahulu:</span>
                    <?php foreach ($history as $h): ?>
                        <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-[11px] flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-800 block"><?= date('d M Y', strtotime($h['supervision_date'])) ?></span>
                                <span class="text-slate-500"><?= esc($h['class_name']) ?> &bull; Skor DL: <?= $h['deep_learning_score'] ?>/4.0</span>
                            </div>
                            <span class="text-[10px] text-blue-700 italic">"<?= esc(mb_strimwidth($h['coaching_notes'], 0, 30, '...')) ?>"</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- PANEL KANAN (7 Kolom): Formulir Rubrik & RTL Coaching -->
        <div class="lg:col-span-7">
            <form action="<?= base_url('supervisor/store-supervision') ?>" method="post" 
                  class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-5">
                <?= csrf_field() ?>
                <input type="hidden" name="teacher_id" value="<?= esc($teacher['id']) ?>">

                <!-- Info Observasi -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal KBM</label>
                        <input type="date" name="supervision_date" value="<?= date('Y-m-d') ?>" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Rombel / Kelas</label>
                        <input type="text" name="class_name" required placeholder="Contoh: X-1 / Fase E"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mata Pelajaran</label>
                        <input type="text" name="subject" required value="<?= esc($teacher['subject_specialty']) ?>"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                </div>

                <!-- 1. Rubrik 4 Standar Kompetensi Nasional (1.0 - 4.0) -->
                <div class="space-y-3.5 pt-2">
                    <div class="border-b border-blue-100 pb-1 flex items-center justify-between">
                        <h3 class="font-bold text-xs text-blue-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="layers" class="w-4 h-4 text-blue-700"></i>
                            <span>1. Standar 4 Kompetensi Guru (Skala 1.0 - 4.0)</span>
                        </h3>
                        <span class="text-[10px] text-slate-400">1: Berkembang | 4: Pengimbas</span>
                    </div>

                    <!-- Pedagogik -->
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="font-bold text-slate-800">Pedagogik (Kesiapan Modul &amp; Diferensiasi)</span>
                            <span id="labelPedagogic" class="font-black text-blue-700 text-sm">3.5</span>
                        </div>
                        <input type="range" name="score_pedagogic" min="1.0" max="4.0" step="0.1" value="3.5"
                               oninput="document.getElementById('labelPedagogic').innerText = this.value"
                               class="w-full accent-blue-700">
                    </div>

                    <!-- Kepribadian -->
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="font-bold text-slate-800">Kepribadian (Keteladanan &amp; Budaya 5S)</span>
                            <span id="labelPersonality" class="font-black text-blue-700 text-sm">3.8</span>
                        </div>
                        <input type="range" name="score_personality" min="1.0" max="4.0" step="0.1" value="3.8"
                               oninput="document.getElementById('labelPersonality').innerText = this.value"
                               class="w-full accent-blue-700">
                    </div>

                    <!-- Sosial -->
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="font-bold text-slate-800">Sosial (Komunikasi Empatik &amp; Dialogis)</span>
                            <span id="labelSocial" class="font-black text-blue-700 text-sm">3.6</span>
                        </div>
                        <input type="range" name="score_social" min="1.0" max="4.0" step="0.1" value="3.6"
                               oninput="document.getElementById('labelSocial').innerText = this.value"
                               class="w-full accent-blue-700">
                    </div>

                    <!-- Profesional -->
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="font-bold text-slate-800">Profesional (Penguasaan Keilmuan Mutakhir)</span>
                            <span id="labelProfessional" class="font-black text-blue-700 text-sm">3.7</span>
                        </div>
                        <input type="range" name="score_professional" min="1.0" max="4.0" step="0.1" value="3.7"
                               oninput="document.getElementById('labelProfessional').innerText = this.value"
                               class="w-full accent-blue-700">
                    </div>
                </div>

                <!-- 2. Indeks Keterlaksanaan Deep Learning (1.0 - 4.0) -->
                <div class="p-4 bg-sky-50/70 border border-sky-200 rounded-3xl space-y-3.5">
                    <div class="flex items-center justify-between border-b border-sky-200 pb-2">
                        <h3 class="font-bold text-xs text-sky-950 uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="compass" class="w-4 h-4 text-sky-700"></i>
                            <span>2. Indeks Deep Learning (Mindful-Meaningful-Joyful)</span>
                        </h3>
                        <div class="text-right">
                            <span class="text-[10px] text-sky-700 block">Rata-rata DL:</span>
                            <strong id="avgDLDisplay" class="text-sky-900 text-sm font-black">3.70</strong>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="font-semibold text-sky-950">Mindful Learning (Fokus &amp; Diferensiasi)</span>
                            <span id="labelMindful" class="font-bold text-sky-800">3.7</span>
                        </div>
                        <input type="range" name="score_mindful" id="inputMindful" min="1.0" max="4.0" step="0.1" value="3.7"
                               oninput="updateDLCalc()" class="w-full accent-sky-600">
                    </div>

                    <div class="space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="font-semibold text-sky-950">Meaningful Learning (Kontekstual Nyata)</span>
                            <span id="labelMeaningful" class="font-bold text-sky-800">3.6</span>
                        </div>
                        <input type="range" name="score_meaningful" id="inputMeaningful" min="1.0" max="4.0" step="0.1" value="3.6"
                               oninput="updateDLCalc()" class="w-full accent-sky-600">
                    </div>

                    <div class="space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="font-semibold text-sky-950">Joyful Learning (Partisipatif &amp; Eksploratif)</span>
                            <span id="labelJoyful" class="font-bold text-sky-800">3.8</span>
                        </div>
                        <input type="range" name="score_joyful" id="inputJoyful" min="1.0" max="4.0" step="0.1" value="3.8"
                               oninput="updateDLCalc()" class="w-full accent-sky-600">
                    </div>
                </div>

                <!-- 3. Catatan Coaching & Tindak Lanjut RTL -->
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
                            Butir Rencana Tindak Lanjut (RTL Coaching Action Item)
                        </label>
                        <textarea name="followup_action" rows="2" placeholder="Contoh: Mengikuti sesi Lesson Study bersama tim MGMP sekolah, atau penyusunan asesmen formatif adaptif..."
                                  class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none leading-relaxed"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tenggat Waktu RTL (Deadline)</label>
                            <input type="date" name="rtl_deadline" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" 
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        </div>
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
        </div>

    </div>
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
