<?php

namespace App\Controllers;

use App\Libraries\SupabaseClient;

class DashboardController extends BaseController
{
    public function index()
    {
        // Jika kepala sekolah, redirect langsung ke portal supervisi
        if (session()->get('role') === 'kepala_sekolah') {
            return redirect()->to(base_url('supervisor'));
        }

        $userId = session()->get('user_id');
        $supabase = new SupabaseClient();

        // 1. Profil Guru
        $profileRows = $supabase->query('profiles', [
            'id' => 'eq.' . $userId,
            'select' => '*',
            'limit' => 1,
        ]);
        $user = (!empty($profileRows) && is_array($profileRows) && !isset($profileRows['error'])) ? $profileRows[0] : [
            'id' => $userId,
            'nip' => session()->get('nip'),
            'full_name' => session()->get('full_name'),
            'subject_specialty' => session()->get('subject_specialty'),
            'avatar_url' => session()->get('avatar_url'),
            'bio' => null,
        ];

        // Update session jika profil berubah
        if (!empty($user['full_name'])) {
            session()->set('full_name', $user['full_name']);
        }
        if (!empty($user['avatar_url'])) {
            session()->set('avatar_url', $user['avatar_url']);
        }

        // 2. Presensi Hari Ini
        $today = date('Y-m-d');
        $attRows = $supabase->query('attendances', [
            'user_id' => 'eq.' . $userId,
            'attendance_date' => 'eq.' . $today,
            'select' => '*',
            'limit' => 1,
        ]);
        $todayAttendance = (!empty($attRows) && is_array($attRows) && !isset($attRows['error'])) ? $attRows[0] : null;

        // 3. Metrik Total
        $totalActivities = $supabase->count('teacher_activities', ['user_id' => 'eq.' . $userId]);
        $totalCreativities = $supabase->count('teacher_creativities', ['user_id' => 'eq.' . $userId]);
        $totalReflections = $supabase->count('teacher_reflections', ['user_id' => 'eq.' . $userId]);

        // 4. Hitung Indeks Deep Learning Radar (Mindful, Meaningful, Joyful)
        // Berdasarkan aktivitas KBM guru dan skor asesmen supervisi Kepala Sekolah
        $actRows = $supabase->query('teacher_activities', [
            'user_id' => 'eq.' . $userId,
            'select' => 'deep_learning_pillar,verification_status',
        ]);
        $actList = (!empty($actRows) && is_array($actRows) && !isset($actRows['error'])) ? $actRows : [];

        $mindfulCount = 0;
        $meaningfulCount = 0;
        $joyfulCount = 0;
        foreach ($actList as $a) {
            $p = $a['deep_learning_pillar'] ?? '';
            $weight = ($a['verification_status'] === 'approved') ? 12 : 7;
            if ($p === 'mindful') $mindfulCount += $weight;
            elseif ($p === 'meaningful') $meaningfulCount += $weight;
            elseif ($p === 'joyful') $joyfulCount += $weight;
            elseif ($p === 'integrated') {
                $mindfulCount += $weight;
                $meaningfulCount += $weight;
                $joyfulCount += $weight;
            }
        }

        // Ambil nilai dari supervisi klinis Kepala Sekolah
        $supervisionRows = $supabase->query('kbm_supervisions', [
            'teacher_id' => 'eq.' . $userId,
            'select' => 'deep_learning_score,score_pedagogic,score_personality,score_social,score_professional',
            'order' => 'supervision_date.desc',
            'limit' => 5,
        ], true);
        $supList = (!empty($supervisionRows) && is_array($supervisionRows) && !isset($supervisionRows['error'])) ? $supervisionRows : [];

        $supBonus = 0;
        if (!empty($supList)) {
            $avgDL = array_sum(array_column($supList, 'deep_learning_score')) / count($supList);
            $supBonus = round(($avgDL / 4.0) * 40); // hingga 40 poin dari supervisi
        }

        // Skor akhir dinamis (0 - 100)
        $mindfulIndex = ($mindfulCount > 0 || $supBonus > 0) ? min(100, $mindfulCount + $supBonus) : 0;
        $meaningfulIndex = ($meaningfulCount > 0 || $supBonus > 0) ? min(100, $meaningfulCount + $supBonus) : 0;
        $joyfulIndex = ($joyfulCount > 0 || $supBonus > 0) ? min(100, $joyfulCount + $supBonus) : 0;

        // 5. Refleksi Terakhir & Catatan Kepala Sekolah
        $latestRefRows = $supabase->query('teacher_reflections', [
            'user_id' => 'eq.' . $userId,
            'select' => '*',
            'order' => 'reflection_date.desc,id.desc',
            'limit' => 1,
        ]);
        $latestReflection = (!empty($latestRefRows) && is_array($latestRefRows) && !isset($latestRefRows['error'])) ? $latestRefRows[0] : null;

        return view('dashboard/index', [
            'title' => 'Dashboard Guru - SMART MADANI',
            'user' => $user,
            'todayAttendance' => $todayAttendance,
            'totalActivities' => $totalActivities,
            'totalCreativities' => $totalCreativities,
            'totalReflections' => $totalReflections,
            'radarScores' => [
                'mindful' => $mindfulIndex,
                'meaningful' => $meaningfulIndex,
                'joyful' => $joyfulIndex,
            ],
            'latestReflection' => $latestReflection,
        ]);
    }

    public function profile()
    {
        $userId = session()->get('user_id');
        $supabase = new SupabaseClient();

        $profileRows = $supabase->query('profiles', [
            'id' => 'eq.' . $userId,
            'select' => '*',
            'limit' => 1,
        ]);
        $user = (!empty($profileRows) && is_array($profileRows) && !isset($profileRows['error'])) ? $profileRows[0] : [
            'id' => $userId,
            'nip' => session()->get('nip'),
            'full_name' => session()->get('full_name'),
            'subject_specialty' => session()->get('subject_specialty'),
            'email' => session()->get('email'),
            'phone_number' => null,
            'role' => session()->get('role'),
            'avatar_url' => session()->get('avatar_url'),
            'bio' => null,
        ];

        return view('profile/index', [
            'title' => 'Profil Pengguna - SMART MADANI',
            'user' => $user,
        ]);
    }

    // e-CV Portofolio Digital Publik (PRD Section 5.5)
    public function publicPortfolio($nip)
    {
        $supabase = new SupabaseClient();

        // Cari user by NIP via service role
        $profileRows = $supabase->query('profiles', [
            'nip' => 'eq.' . $nip,
            'select' => '*',
            'limit' => 1,
        ], true);

        if (empty($profileRows) || !is_array($profileRows) || isset($profileRows['error'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Guru dengan NIP ' . esc($nip) . ' tidak ditemukan.');
        }

        $user = $profileRows[0];

        $creativities = $supabase->query('teacher_creativities', [
            'user_id' => 'eq.' . $user['id'],
            'select' => '*',
            'order' => 'created_at.desc',
        ], true);

        $reflections = $supabase->query('teacher_reflections', [
            'user_id' => 'eq.' . $user['id'],
            'select' => '*',
            'order' => 'reflection_date.desc',
            'limit' => 5,
        ], true);

        return view('profile/portfolio', [
            'title' => 'Digital Teaching Portfolio Card - ' . $user['full_name'],
            'user' => $user,
            'creativities' => is_array($creativities) ? $creativities : [],
            'reflections' => is_array($reflections) ? $reflections : [],
        ]);
    }
}
