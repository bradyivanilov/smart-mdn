<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-5xl mx-auto pb-14">

    <!-- Header & Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 no-print">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Rekapitulasi Presensi &amp; Jam Mengajar Bulanan</h2>
            <p class="text-xs text-slate-500">Monitoring Kepatuhan Kehadiran &amp; Akumulasi Beban Kerja Guru</p>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold shadow flex items-center gap-1.5 transition">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak / PDF</span>
            </button>
            <a href="<?= base_url('supervisor/attendance-recap?export=csv&month=' . $selectedMonth . '&year=' . $selectedYear) ?>" 
               class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow flex items-center gap-1.5 transition">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Ekspor CSV</span>
            </a>
        </div>
    </div>

    <!-- Filter Form Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm no-print">
        <form action="<?= base_url('supervisor/attendance-recap') ?>" method="get" class="flex flex-wrap items-center gap-3 text-xs">
            <div class="flex items-center gap-2">
                <label class="font-bold text-slate-700">Bulan:</label>
                <select name="month" class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl font-semibold">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $selectedMonth == $m ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label class="font-bold text-slate-700">Tahun:</label>
                <select name="year" class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl font-semibold">
                    <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                        <option value="<?= $y ?>" <?= $selectedYear == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <button type="submit" class="px-4 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl font-bold transition flex items-center gap-1">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                <span>Terapkan Filter</span>
            </button>
        </form>
    </div>

    <!-- Tabel Rekap Presensi Dewan Guru -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Laporan Kehadiran Dewan Guru</h3>
                <span class="text-xs text-slate-500">Periode: <?= date('F Y', mktime(0, 0, 0, $selectedMonth, 10, $selectedYear)) ?></span>
            </div>
            <span class="text-xs font-bold text-blue-700"><?= count($recap) ?> Pendidik Terdata</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-2.5 px-3">Nama Guru / NIP</th>
                        <th class="py-2.5 px-3">Mata Pelajaran</th>
                        <th class="py-2.5 px-3 text-center">Hadir</th>
                        <th class="py-2.5 px-3 text-center">Terlambat</th>
                        <th class="py-2.5 px-3 text-center">Izin/Dinas</th>
                        <th class="py-2.5 px-3 text-center">Total Jam</th>
                        <th class="py-2.5 px-3 text-right">Kepatuhan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($recap)): ?>
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">Tidak ada data kehadiran pada periode ini.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($recap as $r): ?>
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-3">
                                <a href="<?= base_url('supervisor/teacher/' . $r['id']) ?>" class="font-bold text-slate-800 hover:text-blue-700 block">
                                    <?= esc($r['full_name']) ?>
                                </a>
                                <span class="text-[10px] text-slate-400 font-mono">NIP: <?= esc($r['nip']) ?></span>
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                <?= esc($r['subject_specialty'] ?: '-') ?>
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-emerald-700">
                                <?= $r['total_present'] ?>
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-amber-600">
                                <?= $r['total_late'] ?>
                            </td>
                            <td class="py-3 px-3 text-center text-slate-600">
                                <?= $r['total_permit'] ?>
                            </td>
                            <td class="py-3 px-3 text-center font-black text-slate-800">
                                <?= $r['total_teaching_hours'] ?> Jam
                            </td>
                            <td class="py-3 px-3 text-right">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[11px]
                                    <?= ($r['compliance_rate'] >= 85) ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                    <?= $r['compliance_rate'] ?>%
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
