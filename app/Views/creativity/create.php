<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-xl mx-auto pb-10">
    <div class="flex items-center gap-2 mb-4">
        <a href="<?= base_url('creativity') ?>" class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <h2 class="text-base font-bold text-slate-800">Unggah Inovasi / Modul Ajar</h2>
    </div>

    <form action="<?= base_url('creativity/store') ?>" method="post" enctype="multipart/form-data" 
          class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-4">
        <?= csrf_field() ?>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Judul Modul / Media Kreatif</label>
            <input type="text" name="title" required placeholder="Contoh: Modul Ajar Gamifikasi Aljabar Linier Berbasis Proyek"
                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Format Karya</label>
                <select name="category" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="modul_ajar">Modul Ajar / RPP</option>
                    <option value="video_pembelajaran">Video Pembelajaran</option>
                    <option value="lkpd">LKPD Kontekstual</option>
                    <option value="media_interaktif">Media Interaktif / Game Edukasi</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Fokus Deep Learning</label>
                <select name="deep_learning_focus" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="all">Terintegrasi (Semua)</option>
                    <option value="mindful">Mindful Learning</option>
                    <option value="meaningful">Meaningful Learning</option>
                    <option value="joyful">Joyful Learning</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Singkat & Petunjuk Penggunaan</label>
            <textarea name="description" rows="3" placeholder="Jelaskan capaian pembelajaran, fase murid, serta panduan praktis menerapkan modul ini..."
                      class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"></textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Berkas Modul / LKPD (PDF/DOCX/PPTX)</label>
            <input type="file" name="file_attachment" accept=".pdf,.docx,.pptx,.zip"
                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">URL Embed Video Pembelajaran (Opsional)</label>
            <input type="url" name="video_embed_url" placeholder="https://www.youtube.com/embed/..."
                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2.5 px-4 rounded-xl text-xs shadow-md transition flex items-center justify-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Terbitkan ke Repositori</span>
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
