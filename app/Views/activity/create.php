<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-xl mx-auto pb-10">
    <div class="flex items-center gap-2 mb-4">
        <a href="<?= base_url('activity') ?>" class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <h2 class="text-base font-bold text-slate-800">Catat Jurnal Aktivitas KBM</h2>
    </div>

    <form action="<?= base_url('activity/store') ?>" method="post" enctype="multipart/form-data" 
          class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-4">
        <?= csrf_field() ?>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal KBM</label>
                <input type="date" name="activity_date" value="<?= date('Y-m-d') ?>" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kategori Kompetensi</label>
                <select name="competency_type" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="pedagogik">Pedagogik</option>
                    <option value="kepribadian">Kepribadian</option>
                    <option value="sosial">Sosial</option>
                    <option value="profesional">Profesional</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Pilar Deep Learning Kemendikdasmen</label>
            <div class="grid grid-cols-3 gap-2 text-xs">
                <label class="flex items-center gap-2 p-2 border rounded-xl cursor-pointer hover:bg-blue-50 border-slate-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50">
                    <input type="radio" name="deep_learning_pillar" value="mindful" checked class="text-blue-600">
                    <span class="font-medium text-slate-700">Mindful</span>
                </label>
                <label class="flex items-center gap-2 p-2 border rounded-xl cursor-pointer hover:bg-indigo-50 border-slate-200 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                    <input type="radio" name="deep_learning_pillar" value="meaningful" class="text-indigo-600">
                    <span class="font-medium text-slate-700">Meaningful</span>
                </label>
                <label class="flex items-center gap-2 p-2 border rounded-xl cursor-pointer hover:bg-sky-50 border-slate-200 has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50">
                    <input type="radio" name="deep_learning_pillar" value="joyful" class="text-sky-600">
                    <span class="font-medium text-slate-700">Joyful</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Judul Aktivitas / Materi KBM</label>
            <input type="text" name="title" required placeholder="Contoh: Eksplorasi Eksponensial dengan Media Simulasi Bakteri"
                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Deskripsi & Diferensiasi Pembelajaran</label>
            <textarea name="description" rows="4" required placeholder="Jelaskan alur interaksi kelas, respon aktif peserta didik, dan bagaimana diferensiasi diterapkan..."
                      class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none leading-relaxed"></textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Bukti Dokumen / Foto Kegiatan (Opsional)</label>
            <input type="file" name="evidence_file" accept=".jpg,.jpeg,.png,.pdf"
                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <span class="text-[10px] text-slate-400 block mt-1">Mendukung format JPG, PNG, atau PDF (Maks 5MB)</span>
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2.5 px-4 rounded-xl text-xs shadow-md transition flex items-center justify-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Simpan Jurnal Aktivitas</span>
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
