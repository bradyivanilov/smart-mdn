<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-xl mx-auto space-y-5 pb-10">
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm text-center">
        <div class="relative inline-block mx-auto mb-3">
            <img src="<?= $user['avatar_url'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80' ?>" 
                 alt="Avatar" class="w-20 h-20 rounded-3xl border-4 border-blue-100 object-cover shadow-md">
            <span class="absolute bottom-0 right-0 w-5 h-5 bg-emerald-500 border-2 border-white rounded-full"></span>
        </div>

        <h2 class="text-base font-bold text-slate-800"><?= esc($user['full_name']) ?></h2>
        <p class="text-xs text-slate-500 font-mono mt-0.5">NIP: <?= esc($user['nip']) ?></p>
        <span class="inline-block mt-2 px-3 py-1 bg-blue-50 text-blue-800 border border-blue-200 text-xs font-semibold rounded-full">
            <?= esc($user['subject_specialty']) ?>
        </span>

        <p class="text-xs text-slate-600 mt-3 max-w-sm mx-auto leading-relaxed italic">
            "<?= esc($user['bio'] ?? 'Pendidik Deep Learning.') ?>"
        </p>

        <div class="mt-4 pt-4 border-t border-slate-100 flex justify-center gap-3">
            <a href="<?= base_url('portfolio/' . $user['nip']) ?>" target="_blank"
               class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow transition flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                <span>Lihat Portofolio Publik (e-CV)</span>
            </a>
            <a href="<?= base_url('pulse/' . $user['id']) ?>" target="_blank"
               class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                <i data-lucide="qr-code" class="w-4 h-4"></i>
                <span>QR Murid</span>
            </a>
        </div>
    </div>

    <!-- Info Detail Kepegawaian -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3 text-xs">
        <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider mb-2">Informasi Akun</h3>
        <div class="flex justify-between py-2 border-b border-slate-100">
            <span class="text-slate-500">Email Sekolah</span>
            <span class="font-semibold text-slate-700"><?= esc($user['email'] ?? '-') ?></span>
        </div>
        <div class="flex justify-between py-2 border-b border-slate-100">
            <span class="text-slate-500">Nomor WhatsApp</span>
            <span class="font-semibold text-slate-700"><?= esc($user['phone_number'] ?? '-') ?></span>
        </div>
        <div class="flex justify-between py-2">
            <span class="text-slate-500">Hak Akses Sistem</span>
            <span class="font-bold text-blue-700 uppercase"><?= esc($user['role']) ?></span>
        </div>
    </div>

    <!-- Tombol Keluar -->
    <a href="<?= base_url('logout') ?>" 
       class="w-full bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 font-bold py-3 rounded-2xl text-xs flex items-center justify-center gap-2 transition">
        <i data-lucide="log-out" class="w-4 h-4"></i>
        <span>Keluar dari Aplikasi</span>
    </a>
</div>
<?= $this->endSection() ?>
