<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto pb-14 space-y-4">
    <div class="flex items-center gap-2 mb-2">
        <a href="<?= base_url('activity') ?>" class="p-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-base font-bold text-slate-800">Catat Jurnal Aktivitas KBM Deep Learning</h2>
            <p class="text-xs text-slate-500">Integrasi 4 Kompetensi Guru &amp; Paradigma Kemendikdasmen</p>
        </div>
    </div>

    <!-- Panduan Interaktif Pilar Deep Learning -->
    <div id="pillarGuideBox" class="p-4 bg-gradient-to-r from-blue-900 to-indigo-950 text-white rounded-3xl shadow-sm space-y-2">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-sky-400 animate-pulse"></span>
            <h3 id="guideTitle" class="text-xs font-bold uppercase tracking-wider text-sky-200">Panduan Pilar: Mindful Learning</h3>
        </div>
        <p id="guideDesc" class="text-xs text-slate-200 leading-relaxed">
            Fokus pada kesadaran tujuan belajar, keterlibatan aktif peserta didik, dan diferensiasi pendekatan untuk memenuhi kebutuhan emosional &amp; kognitif yang beragam.
        </p>
    </div>

    <form action="<?= base_url('activity/store') ?>" method="post" enctype="multipart/form-data" 
          class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-5">
        <?= csrf_field() ?>

        <!-- Tanggal & Kompetensi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal KBM</label>
                <input type="date" name="activity_date" value="<?= date('Y-m-d') ?>" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kategori Kompetensi</label>
                <select name="competency_type" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="pedagogik">Pedagogik (KBM &amp; Asesmen)</option>
                    <option value="kepribadian">Kepribadian (Keteladanan &amp; Budaya)</option>
                    <option value="sosial">Sosial (Komunikasi Wali Murid / Sejawat)</option>
                    <option value="profesional">Profesional (Pengembangan Keilmuan/AI)</option>
                </select>
            </div>
        </div>

        <!-- Pilar Deep Learning Selector -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Pilar Deep Learning Kemendikdasmen</label>
            <div class="grid grid-cols-3 gap-2.5 text-xs">
                <label class="flex flex-col items-center gap-1.5 p-3 border rounded-2xl cursor-pointer hover:bg-blue-50 border-slate-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/70 transition">
                    <input type="radio" name="deep_learning_pillar" value="mindful" checked onchange="updateGuide('mindful')" class="text-blue-600">
                    <span class="font-bold text-slate-800">Mindful</span>
                    <span class="text-[10px] text-slate-500 text-center">Fokus &amp; Diferensiasi</span>
                </label>
                <label class="flex flex-col items-center gap-1.5 p-3 border rounded-2xl cursor-pointer hover:bg-indigo-50 border-slate-200 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/70 transition">
                    <input type="radio" name="deep_learning_pillar" value="meaningful" onchange="updateGuide('meaningful')" class="text-indigo-600">
                    <span class="font-bold text-slate-800">Meaningful</span>
                    <span class="text-[10px] text-slate-500 text-center">Relevansi Riil</span>
                </label>
                <label class="flex flex-col items-center gap-1.5 p-3 border rounded-2xl cursor-pointer hover:bg-sky-50 border-slate-200 has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50/70 transition">
                    <input type="radio" name="deep_learning_pillar" value="joyful" onchange="updateGuide('joyful')" class="text-sky-600">
                    <span class="font-bold text-slate-800">Joyful</span>
                    <span class="text-[10px] text-slate-500 text-center">Partisipatif &amp; Seru</span>
                </label>
            </div>
        </div>

        <!-- Judul Aktivitas -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Judul Aktivitas / Materi KBM</label>
            <input type="text" name="title" required placeholder="Contoh: Eksplorasi Eksponensial dengan Media Simulasi Pertumbuhan Bakteri"
                   class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <!-- 3 Parameter Pedagogis Baru (Deep Learning Scaffold) -->
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3.5">
            <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>Dimensi Pedagogis Terstruktur (Deep Learning)</span>
            </h4>

            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Capaian &amp; Tujuan Spesifik Pembelajaran (Learning Objective)</label>
                <input type="text" name="learning_objective" placeholder="Contoh: Siswa mampu memodelkan fungsi kuadrat dalam kasus lintasan bola basket"
                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Strategi Pembelajaran Berdiferensiasi (Mindful)</label>
                <input type="text" name="differentiation_strategy" placeholder="Contoh: Pembagian LKPD bertingkat (scaffolding) &amp; pilihan produk analisis"
                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Studi Kasus Kontekstual Dunia Nyata (Meaningful)</label>
                <input type="text" name="contextual_problem" placeholder="Contoh: Mengaitkan materi inflasi dengan harga kebutuhan pokok di pasar lokal"
                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>
        </div>

        <!-- Narasi Aktivitas Lengkap -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alur Interaksi &amp; Pelaksanaan Kelas</label>
            <textarea name="description" rows="4" required placeholder="Jelaskan pembukaan kelas, respon aktif murid, dinamika kelompok, dan kesimpulan KBM..."
                      class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none leading-relaxed"></textarea>
        </div>

        <!-- Bukti Dokumen -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Bukti Dokumen / Foto Kegiatan (Opsional)</label>
            <input type="file" name="evidence_file" accept=".jpg,.jpeg,.png,.pdf"
                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <span class="text-[10px] text-slate-400 block mt-1">Mendukung format JPG, PNG, atau PDF (Maks 5MB)</span>
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-xl text-xs shadow-md transition flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>Simpan Jurnal &amp; Ajukan ke Kepala Sekolah</span>
            </button>
        </div>
    </form>
</div>

<script>
    const guides = {
        mindful: {
            title: "Panduan Pilar: Mindful Learning",
            desc: "Fokus pada kesadaran penuh terhadap tujuan belajar, keterlibatan aktif peserta didik, dan diferensiasi pendekatan untuk memenuhi kebutuhan emosional & kognitif murid."
        },
        meaningful: {
            title: "Panduan Pilar: Meaningful Learning",
            desc: "Menghubungkan materi secara konkret dengan realitas kehidupan nyata, problem solving kontekstual, dan interkoneksi disiplin ilmu yang relevan bagi masa depan siswa."
        },
        joyful: {
            title: "Panduan Pilar: Joyful Learning",
            desc: "Menciptakan iklim kelas yang aman, eksploratif, minim tekanan toksik, serta menumbuhkan rasa ingin tahu alamiah melalui diskusi interaktif dan gamifikasi."
        }
    };

    function updateGuide(pillar) {
        if (guides[pillar]) {
            document.getElementById('guideTitle').innerText = guides[pillar].title;
            document.getElementById('guideDesc').innerText = guides[pillar].desc;
        }
    }
</script>
<?= $this->endSection() ?>
