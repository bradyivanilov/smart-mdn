<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-md mx-auto mt-4 mb-12">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-700 text-white font-black text-xl shadow-md mb-2">
                SM
            </div>
            <h2 class="text-lg font-bold text-slate-800">Pendaftaran Akun Guru</h2>
            <p class="text-xs text-slate-500 mt-0.5">Sistem Manajemen Aktivitas, Refleksi, dan Talenta Guru</p>
        </div>

        <form action="<?= base_url('register') ?>" method="post" class="space-y-3.5">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">NIP</label>
                    <input type="text" name="nip" value="<?= old('nip') ?>" required
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"
                           placeholder="Contoh: 19850115...">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">No. WhatsApp</label>
                    <input type="text" name="phone_number" value="<?= old('phone_number') ?>"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"
                           placeholder="0812xxxxxxxx">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Peran / Role Akun</label>
                <select name="role" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="guru">Guru (Pendidik)</option>
                    <option value="kepala_sekolah">Kepala Sekolah / Supervisor</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap & Gelar</label>
                <input type="text" name="full_name" value="<?= old('full_name') ?>" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"
                       placeholder="Contoh: Budi Santoso, S.Pd.">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mata Pelajaran & Fase</label>
                <input type="text" name="subject_specialty" value="<?= old('subject_specialty') ?>" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"
                       placeholder="Contoh: Matematika (Fase E)">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email Aktif</label>
                <input type="email" name="email" value="<?= old('email') ?>" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"
                       placeholder="nama@sekolah.sch.id">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi (Min. 6 Karakter)</label>
                <input type="password" name="password" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"
                       placeholder="••••••••">
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2.5 px-4 rounded-xl text-xs shadow-md transition flex items-center justify-center gap-2">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                    <span>Daftarkan Akun Guru</span>
                </button>
            </div>
        </form>

        <div class="mt-5 pt-3 border-t border-slate-100 text-center">
            <span class="text-xs text-slate-500">Sudah memiliki akun?</span>
            <a href="<?= base_url('login') ?>" class="text-xs font-bold text-blue-700 hover:underline ml-1">
                Masuk di sini
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
