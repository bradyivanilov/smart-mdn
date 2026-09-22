<?php

namespace App\Controllers;

use App\Libraries\SupabaseClient;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }
        return view('auth/login', [
            'title' => 'Masuk Akun Guru - SMART MADANI',
        ]);
    }

    public function processLogin()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        if (empty($email) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Email dan password wajib diisi.');
        }

        $supabase = new SupabaseClient();
        $authResponse = $supabase->signIn($email, $password);

        if (isset($authResponse['error']) && $authResponse['error']) {
            $msg = $authResponse['message'] ?? 'Email atau password salah!';
            return redirect()->back()->withInput()->with('error', $msg);
        }

        $accessToken = $authResponse['access_token'] ?? null;
        $user = $authResponse['user'] ?? null;

        if (!$accessToken || !$user) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses autentikasi.');
        }

        $userId = $user['id'];
        $expiresIn = $authResponse['expires_in'] ?? 3600;

        // Ambil data profile dari Supabase table profiles
        $profileClient = new SupabaseClient($accessToken);
        $profiles = $profileClient->query('profiles', [
            'id' => 'eq.' . $userId,
            'select' => '*',
        ]);

        $profileData = (is_array($profiles) && !empty($profiles[0])) ? $profiles[0] : null;

        $fullName = $profileData['full_name'] ?? ($user['user_metadata']['full_name'] ?? 'Guru');
        $nip = $profileData['nip'] ?? ($user['user_metadata']['nip'] ?? '');
        $role = $profileData['role'] ?? 'guru';
        $subject = $profileData['subject_specialty'] ?? ($user['user_metadata']['subject_specialty'] ?? '');
        $avatarUrl = $profileData['avatar_url'] ?? null;

        session()->set([
            'is_logged_in' => true,
            'access_token' => $accessToken,
            'token_expires_at' => time() + $expiresIn,
            'user_id' => $userId,
            'email' => $user['email'],
            'nip' => $nip,
            'full_name' => $fullName,
            'role' => $role,
            'subject_specialty' => $subject,
            'avatar_url' => $avatarUrl,
        ]);

        return redirect()->to(base_url('dashboard'))->with('success', 'Selamat datang, ' . $fullName . '!');
    }

    public function register()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }
        return view('auth/register', [
            'title' => 'Pendaftaran Akun Guru - SMART MADANI',
        ]);
    }

    public function processRegister()
    {
        $nip = trim((string) $this->request->getPost('nip'));
        $fullName = trim((string) $this->request->getPost('full_name'));
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $subject = trim((string) $this->request->getPost('subject_specialty'));
        $phone = trim((string) $this->request->getPost('phone_number'));

        if (empty($nip) || empty($fullName) || empty($email) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'NIP, Nama Lengkap, Email, dan Password wajib diisi.');
        }

        if (strlen($password) < 6) {
            return redirect()->back()->withInput()->with('error', 'Password minimal 6 karakter.');
        }

        $supabase = new SupabaseClient();

        // 1. Cek NIP sudah terdaftar di profiles (via service role)
        $existingNip = $supabase->query('profiles', [
            'nip' => 'eq.' . $nip,
            'select' => 'id',
        ], true);

        if (is_array($existingNip) && !empty($existingNip[0])) {
            return redirect()->back()->withInput()->with('error', 'NIP sudah terdaftar di sistem.');
        }

        // 2. Daftar via Supabase Admin API (auto-confirm email)
        $role = trim((string) $this->request->getPost('role')) ?: 'guru';
        if (!in_array($role, ['guru', 'kepala_sekolah'])) {
            $role = 'guru';
        }

        $signUpResponse = $supabase->adminCreateUser($email, $password, [
            'nip' => $nip,
            'full_name' => $fullName,
            'role' => $role,
            'subject_specialty' => $subject,
            'phone_number' => $phone,
        ], true);

        if (isset($signUpResponse['error']) && $signUpResponse['error']) {
            return redirect()->back()->withInput()->with('error', 'Pendaftaran gagal: ' . ($signUpResponse['message'] ?? 'Kesalahan API'));
        }

        $newUserId = $signUpResponse['id'] ?? ($signUpResponse['user']['id'] ?? null);

        // 3. Pastikan row profile terisi
        if ($newUserId) {
            $supabase->insert('profiles', [
                'id' => $newUserId,
                'nip' => $nip,
                'full_name' => $fullName,
                'role' => $role,
                'subject_specialty' => $subject,
                'phone_number' => $phone,
            ], true);
        }

        return redirect()->to(base_url('login'))->with('success', 'Akun ' . ($role === 'kepala_sekolah' ? 'Kepala Sekolah' : 'Guru') . ' berhasil dibuat dan langsung aktif! Silakan login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Berhasil logout.');
    }
}
