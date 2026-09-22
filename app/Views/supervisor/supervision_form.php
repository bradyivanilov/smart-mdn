<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-xl mx-auto pb-12">
    <div class="flex items-center gap-2 mb-4">
        <a href="<?= base_url('supervisor/teachers') ?>" class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-base font-bold text-slate-800">Supervisi & Asesmen Klinis KBM</h2>
            <p class="text-xs text-slate-500">Guru: <strong class="text-blue-700"><?= esc($teacher['full_name']) ?></strong> (NIP: <?= esc($teacher['nip']) ?>)</p>
        </div>
    </div>

    <form action="<?= base_url('supervisor/store-supervision') ?>" method="post" 
          class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="teacher_id" value="<?= esc($teacher['id']) ?>">

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Rombel / Kelas</label>
                <input type="text" name="class_name" required placeholder="Contoh: X-1"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mata Pelajaran</label>
                <input type="text" name="subject" required value="<?= esc($teacher['subject_specialty']) ?>"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>
        </div>

        <!-- Rubrik Penilaian 4 Kompetensi Guru (Skor 1.0 - 4.0) -->
        <div class="space-y-3 pt-2">
            <h3 class="font-bold text-xs text-blue-900 uppercase tracking-wider border-b border-blue-100 pb-1">
                Rubrik Standar Kompetensi Guru (Kemendikdasmen 1.0 - 4.0)
            </h3>

            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="font-semibold text-slate-700">1. Kompetensi Pedagogik (Kesiapan Modul & Diferensiasi)</span>
                    <span id="labelPedagogic" class="font-bold text-blue-700">3.5</span>
                </div>
                <input type="range" name="score_pedagogic" min="1.0" max="4.0" step="0.1" value="3.5"
                       oninput="document.getElementById('labelPedagogic').innerText = this.value"
                       class="w-full accent-blue-700">
            </div>

            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="font-semibold text-slate-700">2. Kompetensi Kepribadian (Keteladanan & Budaya 5S)</span>
                    <span id="labelPersonality" class="font-bold text-blue-700">3.8</span>
                </div>
                <input type="range" name="score_personality" min="1.0" max="4.0" step="0.1" value="3.8"
                       oninput="document.getElementById('labelPersonality').innerText = this.value"
                       class="w-full accent-blue-700">
            </div>

            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="font-semibold text-slate-700">3. Kompetensi Sosial (Komunikasi Positif & Empati Siswa)</span>
                    <span id="labelSocial" class="font-bold text-blue-700">3.6</span>
                </div>
                <input type="range" name="score_social" min="1.0" max="4.0" step="0.1" value="3.6"
                       oninput="document.getElementById('labelSocial').innerText = this.value"
                       class="w-full accent-blue-700">
            </div>

            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="font-semibold text-slate-700">4. Kompetensi Profesional (Penguasaan Keilmuan Terkini)</span>
                    <span id="labelProfessional" class="font-bold text-blue-700">3.7</span>
                </div>
                <input type="range" name="score_professional" min="1.0" max="4.0" step="0.1" value="3.7"
                       oninput="document.getElementById('labelProfessional').innerText = this.value"
                       class="w-full accent-blue-700">
            </div>
        </div>

        <!-- Skor Deep Learning -->
        <div class="p-3.5 bg-sky-50/70 border border-sky-200 rounded-2xl space-y-2">
            <div class="flex justify-between text-xs">
                <span class="font-bold text-sky-950">Indeks Pelaksanaan Deep Learning (Mindful-Meaningful-Joyful)</span>
                <span id="labelDL" class="font-bold text-sky-800 text-sm">3.8</span>
            </div>
            <input type="range" name="deep_learning_score" min="1.0" max="4.0" step="0.1" value="3.8"
                   oninput="document.getElementById('labelDL').innerText = this.value"
                   class="w-full accent-sky-600">
            <p class="text-[11px] text-sky-700">Menilai sejauh mana murid terlibat sadar, materi kontekstual dunia nyata, dan suasana kelas menggembirakan.</p>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Catatan Coaching & Rekomendasi Perbaikan</label>
            <textarea name="coaching_notes" rows="4" required placeholder="Tuliskan catatan apresiasi, kekuatan KBM guru, dan hal yang perlu ditingkatkan secara konstruktif..."
                      class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none leading-relaxed"></textarea>
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2.5 px-4 rounded-xl text-xs shadow-md transition flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>Terbitkan Hasil Supervisi KBM</span>
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
