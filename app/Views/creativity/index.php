<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-4 max-w-2xl mx-auto pb-12">

    <!-- Header bar -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Our Creativity</h2>
            <p class="text-xs text-slate-500">
                <?= session()->get('role') === 'kepala_sekolah' ? 'Kurasi & Telaah Modul Ajar Deep Learning Guru' : 'Repositori Modul Ajar & Inovasi Deep Learning' ?>
            </p>
        </div>

        <?php if (session()->get('role') === 'guru'): ?>
            <a href="<?= base_url('creativity/create') ?>" 
               class="bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold py-2 px-3.5 rounded-xl shadow transition flex items-center gap-1.5">
                <i data-lucide="upload" class="w-4 h-4"></i>
                <span>Unggah Karya</span>
            </a>
        <?php else: ?>
            <span class="px-3 py-1 bg-amber-100 text-amber-900 border border-amber-300 rounded-xl text-xs font-bold flex items-center gap-1.5">
                <i data-lucide="shield-check" class="w-4 h-4 text-amber-700"></i>
                <span>Mode Kurasi Kepala Sekolah</span>
            </span>
        <?php endif; ?>
    </div>

    <!-- Feed Karya Guru -->
    <div class="space-y-4">
        <?php if (empty($creativities)): ?>
            <div class="bg-white rounded-3xl p-10 text-center border border-slate-200">
                <i data-lucide="sparkles" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-slate-600">Belum ada karya atau modul ajar yang diunggah</p>
                <p class="text-xs text-slate-400 mt-1">Guru dapat mengunggah modul ajar, video, atau LKPD dari dashboard guru.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($creativities as $item): ?>
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm transition hover:shadow-md space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <!-- Category Badge -->
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                            <?= str_replace('_', ' ', esc($item['category'])) ?>
                        </span>

                        <!-- Deep Learning Focus Badge -->
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                            Fokus: <?= ucfirst(esc($item['deep_learning_focus'] ?? 'all')) ?>
                        </span>

                        <?php if (!empty($item['is_featured'])): ?>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300 flex items-center gap-1">
                                <i data-lucide="award" class="w-3 h-3 text-amber-600"></i>
                                <span>Karya Terpilih Kurikulum</span>
                            </span>
                        <?php endif; ?>
                    </div>

                    <span class="text-[11px] text-slate-400">
                        <?= !empty($item['created_at']) ? date('d M Y', strtotime($item['created_at'])) : '' ?>
                    </span>
                </div>

                <div>
                    <h3 class="font-bold text-base text-slate-800 leading-snug">
                        <?= esc($item['title']) ?>
                    </h3>

                    <?php if (!empty($item['profiles']['full_name'])): ?>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5 flex items-center gap-1">
                            <span>Oleh:</span>
                            <strong class="text-blue-700 font-semibold"><?= esc($item['profiles']['full_name']) ?></strong>
                        </p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($item['description'])): ?>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        <?= nl2br(esc($item['description'])) ?>
                    </p>
                <?php endif; ?>

                <!-- Video Embed if exists -->
                <?php if (!empty($item['video_embed_url'])): ?>
                    <div class="rounded-2xl overflow-hidden aspect-video bg-black border border-slate-200">
                        <iframe class="w-full h-full" src="<?= esc($item['video_embed_url']) ?>" 
                                title="Video Media Pembelajaran" frameborder="0" allowfullscreen></iframe>
                    </div>
                <?php endif; ?>

                <!-- Attachment file if exists -->
                <?php if (!empty($item['file_attachment_url'])): ?>
                    <div>
                        <a href="<?= base_url($item['file_attachment_url']) ?>" target="_blank"
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-blue-700 hover:bg-blue-50 transition">
                            <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
                            <span>Unduh Modul / LKPD Pembelajaran</span>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Catatan Kurasi Kepala Sekolah jika ada -->
                <?php if (!empty($item['curator_notes'])): ?>
                    <div class="p-3 bg-amber-50/70 border border-amber-200 rounded-2xl text-xs text-amber-950">
                        <strong class="block text-[10px] uppercase font-bold text-amber-900 mb-0.5">Catatan Kurasi Kepala Sekolah:</strong>
                        <p class="italic">"<?= esc($item['curator_notes']) ?>"</p>
                    </div>
                <?php endif; ?>

                <!-- Bagian Bawah: Apresiasi (Guru) ATAU Form Kurasi (Kepala Sekolah) -->
                <?php if (session()->get('role') === 'kepala_sekolah'): ?>
                    <!-- KEPALA SEKOLAH: Form Penilaian & Kurasi Modul -->
                    <div class="pt-3 border-t border-slate-200">
                        <form action="<?= base_url('supervisor/curate-creativity/' . $item['id']) ?>" method="post" class="space-y-2">
                            <?= csrf_field() ?>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                Instrumen Kurasi Modul &amp; Kelayakan Bahan Ajar
                            </label>
                            <input type="text" name="curator_notes" 
                                   value="<?= esc($item['curator_notes'] ?? '') ?>"
                                   placeholder="Tuliskan catatan kelayakan modul atau masukan untuk guru..."
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                            <input type="hidden" name="is_featured" value="<?= !empty($item['is_featured']) ? '0' : '1' ?>">
                            
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-[11px] text-slate-400">
                                    Status saat ini: <strong class="<?= !empty($item['is_featured']) ? 'text-amber-600' : 'text-slate-600' ?>"><?= !empty($item['is_featured']) ? 'Karya Terpilih' : 'Reguler' ?></strong>
                                </span>
                                <button type="submit" 
                                        class="px-4 py-2 <?= !empty($item['is_featured']) ? 'bg-slate-700 hover:bg-slate-800 text-white' : 'bg-amber-500 hover:bg-amber-600 text-slate-950' ?> rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow">
                                    <i data-lucide="award" class="w-3.5 h-3.5"></i>
                                    <span><?= !empty($item['is_featured']) ? 'Batalkan Status Terpilih' : 'Sematkan Karya Terpilih' ?></span>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- GURU: Tombol Apresiasi Like -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <form action="<?= base_url('creativity/like/' . $item['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="flex items-center gap-1.5 text-slate-500 hover:text-rose-600 transition">
                                <i data-lucide="heart" class="w-4 h-4 text-rose-500 fill-rose-50"></i>
                                <span class="font-bold text-slate-700"><?= $item['likes_count'] ?? 0 ?></span>
                                <span class="text-[11px] text-slate-400">Apresiasi</span>
                            </button>
                        </form>

                        <div class="text-[11px] text-slate-400 flex items-center gap-1">
                            <i data-lucide="check-check" class="w-3.5 h-3.5 text-blue-600"></i>
                            <span>Terverifikasi Kurikulum</span>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>
