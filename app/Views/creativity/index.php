<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-4 max-w-xl mx-auto pb-10">

    <!-- Header bar -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Our Creativity</h2>
            <p class="text-xs text-slate-500">Repositori Modul Ajar & Inovasi Deep Learning</p>
        </div>
        <a href="<?= base_url('creativity/create') ?>" 
           class="bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold py-2 px-3.5 rounded-xl shadow transition flex items-center gap-1.5">
            <i data-lucide="upload" class="w-4 h-4"></i>
            <span>Unggah Karya</span>
        </a>
    </div>

    <!-- Feed Karya Guru -->
    <div class="space-y-4">
        <?php if (empty($creativities)): ?>
            <div class="bg-white rounded-3xl p-8 text-center border border-slate-200">
                <i data-lucide="sparkles" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-slate-600">Belum ada karya atau modul ajar</p>
                <p class="text-xs text-slate-400 mt-1">Jadilah yang pertama mengunggah modul ajar atau media inovasi Anda.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($creativities as $item): ?>
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm transition hover:shadow-md">
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
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1">
                                <i data-lucide="award" class="w-3 h-3 text-amber-600"></i>
                                <span>Karya Terpilih</span>
                            </span>
                        <?php endif; ?>
                    </div>

                    <span class="text-[11px] text-slate-400">
                        <?= !empty($item['created_at']) ? date('d M Y', strtotime($item['created_at'])) : '' ?>
                    </span>
                </div>

                <h3 class="font-bold text-base text-slate-800 mt-2 leading-snug">
                    <?= esc($item['title']) ?>
                </h3>

                <!-- Info Pembuat Karya bila ada join profile -->
                <?php if (!empty($item['profiles']['full_name'])): ?>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                        Oleh: <span class="text-blue-700 font-semibold"><?= esc($item['profiles']['full_name']) ?></span>
                    </p>
                <?php endif; ?>

                <?php if (!empty($item['description'])): ?>
                    <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                        <?= nl2br(esc($item['description'])) ?>
                    </p>
                <?php endif; ?>

                <!-- Video Embed if exists -->
                <?php if (!empty($item['video_embed_url'])): ?>
                    <div class="mt-3 rounded-2xl overflow-hidden aspect-video bg-black border border-slate-200">
                        <iframe class="w-full h-full" src="<?= esc($item['video_embed_url']) ?>" 
                                title="Video Media Pembelajaran" frameborder="0" allowfullscreen></iframe>
                    </div>
                <?php endif; ?>

                <!-- Attachment file if exists -->
                <?php if (!empty($item['file_attachment_url'])): ?>
                    <div class="mt-3">
                        <a href="<?= base_url($item['file_attachment_url']) ?>" target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-blue-700 hover:bg-blue-50 transition">
                            <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
                            <span>Unduh Modul / LKPD Pembelajaran</span>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
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
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>
