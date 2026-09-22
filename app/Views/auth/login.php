<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-sm mx-auto mt-6 mb-12">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-700 text-white font-black text-2xl shadow-md mb-3">
                SM
            </div>
            <h2 class="text-xl font-bold text-slate-800">Masuk Akun Guru</h2>
            <p class="text-xs text-slate-500 mt-1">Platform Refleksi & Kinerja Guru Deep Learning</p>
        </div>

        <form action="<?= base_url('login') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email Guru</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input type="email" name="email" value="<?= old('email') ?>" required
                           class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition"
                           placeholder="nama@sekolah.sch.id">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="password" name="password" required
                           class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition"
                           placeholder="••••••••">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2.5 px-4 rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Masuk ke Dashboard Guru</span>
                </button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-100 text-center space-y-2">
            <p class="text-xs text-slate-500">Belum memiliki akun guru?</p>
            <a href="<?= base_url('register') ?>" 
               class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 hover:text-blue-800 hover:underline">
                <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                <span>Daftar Akun Guru Baru</span>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
