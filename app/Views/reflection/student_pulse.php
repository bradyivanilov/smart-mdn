<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Refleksi Suara Murid' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { background-color: #f0fdf4; font-family: system-ui, sans-serif; }
    </style>
</head>
<body class="p-4 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-3xl p-6 shadow-md border border-slate-200 max-w-sm w-full">
        <div class="text-center mb-5">
            <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center text-white mx-auto shadow-md mb-2">
                <i data-lucide="sparkles" class="w-6 h-6"></i>
            </div>
            <h2 class="text-base font-bold text-slate-800">Refleksi Suara Murid</h2>
            <p class="text-xs text-slate-500 mt-0.5">Survei 3 Pilar Deep Learning (Anonim & Cepat)</p>
            <?php if (!empty($teacher)): ?>
                <div class="mt-2 inline-block px-3 py-1 bg-emerald-50 text-emerald-800 rounded-full text-[11px] font-semibold">
                    Guru: <?= esc($teacher['full_name']) ?>
                </div>
            <?php endif; ?>
        </div>

        <form action="<?= base_url('pulse/store') ?>" method="post" class="space-y-4 text-xs">
            <?= csrf_field() ?>
            <input type="hidden" name="teacher_id" value="<?= esc($teacherId) ?>">

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Mata Pelajaran</label>
                    <input type="text" name="subject_name" required placeholder="Contoh: Matematika" 
                           value="<?= esc($teacher['subject_specialty'] ?? '') ?>"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Kelas</label>
                    <input type="text" name="grade_class" required placeholder="Contoh: X-1" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>
            </div>

            <!-- 1. Joyful -->
            <div class="p-3 bg-sky-50 rounded-2xl border border-sky-100">
                <label class="font-bold text-sky-950 block mb-1">1. Joyful Learning (Perasaanmu)</label>
                <p class="text-[11px] text-sky-700 mb-2">Apakah pembelajaran hari ini menyenangkan dan membuatmu penasaran?</p>
                <div class="flex justify-between items-center text-slate-600">
                    <span class="text-[10px]">Membosankan</span>
                    <div class="flex gap-2">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <label class="flex flex-col items-center">
                                <input type="radio" name="joyful_score" value="<?= $i ?>" <?= $i==5 ? 'checked' : '' ?> class="text-sky-600">
                                <span class="text-[10px] mt-0.5"><?= $i ?></span>
                            </label>
                        <?php endfor; ?>
                    </div>
                    <span class="text-[10px]">Sangat Seru!</span>
                </div>
            </div>

            <!-- 2. Meaningful -->
            <div class="p-3 bg-indigo-50 rounded-2xl border border-indigo-100">
                <label class="font-bold text-indigo-950 block mb-1">2. Meaningful Learning (Manfaat)</label>
                <p class="text-[11px] text-indigo-700 mb-2">Apakah kamu paham manfaat materi ini dalam kehidupan sehari-hari?</p>
                <div class="flex justify-between items-center text-slate-600">
                    <span class="text-[10px]">Tidak Paham</span>
                    <div class="flex gap-2">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <label class="flex flex-col items-center">
                                <input type="radio" name="meaningful_score" value="<?= $i ?>" <?= $i==5 ? 'checked' : '' ?> class="text-indigo-600">
                                <span class="text-[10px] mt-0.5"><?= $i ?></span>
                            </label>
                        <?php endfor; ?>
                    </div>
                    <span class="text-[10px]">Sangat Nyata</span>
                </div>
            </div>

            <!-- 3. Mindful -->
            <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-100">
                <label class="font-bold text-emerald-950 block mb-1">3. Mindful Learning (Keterlibatan)</label>
                <p class="text-[11px] text-emerald-700 mb-2">Apakah pendapat dan suaramu didengarkan selama pelajaran?</p>
                <div class="flex justify-between items-center text-slate-600">
                    <span class="text-[10px]">Diabaikan</span>
                    <div class="flex gap-2">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <label class="flex flex-col items-center">
                                <input type="radio" name="mindful_score" value="<?= $i ?>" <?= $i==5 ? 'checked' : '' ?> class="text-emerald-600">
                                <span class="text-[10px] mt-0.5"><?= $i ?></span>
                            </label>
                        <?php endfor; ?>
                    </div>
                    <span class="text-[10px]">Sangat Didengar</span>
                </div>
            </div>

            <div>
                <label class="font-bold text-slate-700 block mb-1">Pesan / Masukan Singkat untuk Gurumu</label>
                <textarea name="student_note" rows="2" placeholder="Tulis hal yang paling kamu sukai atau yang ingin diperbaiki..."
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
            </div>

            <button type="submit" 
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                <i data-lucide="send" class="w-4 h-4"></i>
                <span>Kirim Refleksi Suara Murid</span>
            </button>
        </form>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
