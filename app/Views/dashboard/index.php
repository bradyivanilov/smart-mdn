<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-5 max-w-xl mx-auto pb-10">

    <!-- Hero Card Guru -->
    <div class="bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 text-white rounded-3xl p-5 shadow-sm relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-sky-400/10 rounded-full blur-2xl"></div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="<?= $user['avatar_url'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80' ?>" 
                     alt="Avatar" class="w-12 h-12 rounded-2xl border-2 border-sky-300 object-cover shadow">
                <div>
                    <h2 class="text-sm font-bold text-white leading-tight"><?= esc($user['full_name']) ?></h2>
                    <span class="text-[11px] text-sky-200 block mt-0.5"><?= esc($user['subject_specialty']) ?></span>
                </div>
            </div>
            <a href="<?= base_url('portfolio/' . $user['nip']) ?>" target="_blank"
               class="px-2.5 py-1.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-[11px] font-bold text-sky-200 flex items-center gap-1 transition">
                <i data-lucide="share-2" class="w-3.5 h-3.5"></i>
                <span>e-CV</span>
            </a>
        </div>

        <!-- Attendance Quick Action -->
        <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full <?= !empty($todayAttendance) ? 'bg-emerald-400' : 'bg-amber-400 animate-ping' ?>"></span>
                <span class="text-slate-200 text-[11px]">
                    <?= !empty($todayAttendance) ? 'Presensi Masuk: ' . substr($todayAttendance['check_in_time'], 0, 5) : 'Belum presensi hari ini' ?>
                </span>
            </div>
            <a href="<?= base_url('attendance') ?>" 
               class="px-3 py-1 bg-sky-400 hover:bg-sky-300 text-blue-950 rounded-xl font-bold text-[11px] shadow transition flex items-center gap-1">
                <i data-lucide="map-pin" class="w-3 h-3"></i>
                <span><?= empty($todayAttendance) ? 'Presensi Sekarang' : 'Cek Status' ?></span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid (Aktivitas, Modul, Refleksi) -->
    <div class="grid grid-cols-3 gap-3">
        <a href="<?= base_url('activity') ?>" class="bg-white p-3.5 rounded-2xl border border-slate-200 text-center shadow-sm hover:border-blue-300 transition">
            <span class="block text-xl font-black text-blue-700"><?= $totalActivities ?></span>
            <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider mt-0.5 block">Jurnal KBM</span>
        </a>
        <a href="<?= base_url('creativity') ?>" class="bg-white p-3.5 rounded-2xl border border-slate-200 text-center shadow-sm hover:border-blue-300 transition">
            <span class="block text-xl font-black text-sky-600"><?= $totalCreativities ?></span>
            <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider mt-0.5 block">Karya Modul</span>
        </a>
        <a href="<?= base_url('reflection') ?>" class="bg-white p-3.5 rounded-2xl border border-slate-200 text-center shadow-sm hover:border-blue-300 transition">
            <span class="block text-xl font-black text-indigo-700"><?= $totalReflections ?></span>
            <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider mt-0.5 block">Refleksi</span>
        </a>
    </div>

    <!-- PUSAT AKSI CEPAT GURU (MOBILE & DESKTOP ACCESSIBLE) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
        <a href="<?= base_url('attendance') ?>" class="p-3 bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl shadow-xs transition flex flex-col items-center text-center gap-1.5 group">
            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center group-hover:scale-105 transition">
                <i data-lucide="camera" class="w-4 h-4"></i>
            </div>
            <strong class="text-slate-800 text-xs">Presensi GPS</strong>
            <span class="text-[10px] text-slate-400">Swafoto Kamera</span>
        </a>

        <a href="<?= base_url('activity/create') ?>" class="p-3 bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl shadow-xs transition flex flex-col items-center text-center gap-1.5 group">
            <div class="w-8 h-8 rounded-xl bg-blue-700 text-white flex items-center justify-center group-hover:scale-105 transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
            </div>
            <strong class="text-slate-800 text-xs">Isi Jurnal KBM</strong>
            <span class="text-[10px] text-slate-400">4 Kompetensi</span>
        </a>

        <a href="<?= base_url('reflection/peer-observation') ?>" class="p-3 bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl shadow-xs transition flex flex-col items-center text-center gap-1.5 group">
            <div class="w-8 h-8 rounded-xl bg-purple-600 text-white flex items-center justify-center group-hover:scale-105 transition">
                <i data-lucide="eye" class="w-4 h-4"></i>
            </div>
            <strong class="text-slate-800 text-xs">Peer Observe</strong>
            <span class="text-[10px] text-slate-400">Lesson Study</span>
        </a>

        <a href="<?= base_url('portfolio/' . $user['nip']) ?>" target="_blank" class="p-3 bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl shadow-xs transition flex flex-col items-center text-center gap-1.5 group">
            <div class="w-8 h-8 rounded-xl bg-indigo-700 text-white flex items-center justify-center group-hover:scale-105 transition">
                <i data-lucide="printer" class="w-4 h-4"></i>
            </div>
            <strong class="text-slate-800 text-xs">Cetak e-CV</strong>
            <span class="text-[10px] text-slate-400">Dokumen PKB</span>
        </a>
    </div>

    <!-- Deep Learning Matrix Indicator (Radar Chart Analytics - PRD Section 5.1) -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="compass" class="w-4 h-4 text-blue-700"></i>
                    <span>Deep Learning Matrix Indicator</span>
                </h3>
                <span class="text-[10px] text-slate-400">Keseimbangan 3 Pilar Kemendikdasmen</span>
            </div>
            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-full border border-blue-200">
                Skor Realtime
            </span>
        </div>

        <div class="relative aspect-square max-w-[280px] mx-auto">
            <canvas id="deepLearningRadar"></canvas>
        </div>

        <div class="grid grid-cols-3 gap-2 mt-4 pt-3 border-t border-slate-100 text-center text-xs">
            <div class="p-2 bg-blue-50 rounded-xl">
                <span class="text-[10px] font-semibold text-blue-700 block">Mindful</span>
                <span class="text-sm font-black text-blue-900"><?= $radarScores['mindful'] ?>%</span>
            </div>
            <div class="p-2 bg-indigo-50 rounded-xl">
                <span class="text-[10px] font-semibold text-indigo-700 block">Meaningful</span>
                <span class="text-sm font-black text-indigo-900"><?= $radarScores['meaningful'] ?>%</span>
            </div>
            <div class="p-2 bg-sky-50 rounded-xl">
                <span class="text-[10px] font-semibold text-sky-700 block">Joyful</span>
                <span class="text-sm font-black text-sky-900"><?= $radarScores['joyful'] ?>%</span>
            </div>
        </div>
    </div>

    <!-- Status Refleksi Terakhir & Supervisi Kepala Sekolah -->
    <?php if (!empty($latestReflection)): ?>
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="sparkles" class="w-4 h-4 text-indigo-600"></i>
                    <span>Refleksi KBM Terakhir</span>
                </span>
                <span class="text-[11px] text-slate-400">
                    <?= date('d M Y', strtotime($latestReflection['reflection_date'])) ?>
                </span>
            </div>

            <div class="p-3 bg-slate-50 rounded-2xl text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-700">Self-Assessment Guru:</span>
                    <span class="font-bold text-blue-700">Level <?= $latestReflection['competency_level'] ?></span>
                </div>
                <p class="text-slate-600 italic">"<?= esc(mb_strimwidth($latestReflection['situation_analysis'], 0, 120, '...')) ?>"</p>
                <?php if (!empty($latestReflection['principal_feedback'])): ?>
                    <div class="pt-1.5 border-t border-slate-200 text-blue-800">
                        <strong class="text-[10px] uppercase block">Feedback Kepala Sekolah:</strong>
                        <span class="text-[11px]"><?= esc($latestReflection['principal_feedback']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
    const ctx = document.getElementById('deepLearningRadar').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Mindful Learning', 'Meaningful Learning', 'Joyful Learning'],
            datasets: [{
                label: 'Indeks Deep Learning',
                data: [
                    <?= $radarScores['mindful'] ?>, 
                    <?= $radarScores['meaningful'] ?>, 
                    <?= $radarScores['joyful'] ?>
                ],
                fill: true,
                backgroundColor: 'rgba(30, 64, 175, 0.2)',
                borderColor: '#1e40af',
                pointBackgroundColor: '#0284c7',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#0284c7',
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                r: {
                    min: 0,
                    max: 100,
                    ticks: {
                        stepSize: 25,
                        display: false
                    },
                    pointLabels: {
                        font: {
                            size: 11,
                            weight: '600'
                        },
                        color: '#334155'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
<?= $this->endSection() ?>
