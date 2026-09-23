<?php

namespace App\Controllers;

use App\Libraries\SupabaseClient;

class SupervisorController extends BaseController
{
    /**
     * Dashboard Kepala Sekolah:
     * - Ringkasan kehadiran dewan guru hari ini
     * - Antrean jurnal KBM menunggu review (Verifikasi Jurnal)
     * - Antrean refleksi menunggu umpan balik coaching
     * - Riwayat supervisi KBM klinis terkini
     */
    public function index()
    {
        $supabase = new SupabaseClient();

        // 1. Data dewan guru aktif
        $guruList = $supabase->query('profiles', [
            'role' => 'eq.guru',
            'select' => '*',
            'order' => 'full_name.asc',
        ], true);
        $gurus = (is_array($guruList) && !isset($guruList['error'])) ? $guruList : [];
        $totalGurus = count($gurus);

        // 2. Presensi Guru Hari Ini & Pemilahan
        $today = date('Y-m-d');
        $attRows = $supabase->query('attendances', [
            'attendance_date' => 'eq.' . $today,
            'select' => '*,profiles(id,full_name,nip,subject_specialty)',
        ], true);
        $attendancesToday = (is_array($attRows) && !isset($attRows['error'])) ? $attRows : [];

        $attendedUserIds = array_column($attendancesToday, 'user_id');
        $unattendedGurus = [];
        foreach ($gurus as $g) {
            if (!in_array($g['id'], $attendedUserIds, true)) {
                $unattendedGurus[] = $g;
            }
        }

        // 3. Jurnal Aktivitas Menunggu Verifikasi
        $pendingActivities = $supabase->query('teacher_activities', [
            'verification_status' => 'eq.pending',
            'select' => '*,profiles(id,full_name,nip,subject_specialty)',
            'order' => 'activity_date.desc,id.desc',
            'limit' => 15,
        ], true);

        // 4. Refleksi Menunggu Umpan Balik / Coaching Feedback
        $pendingReflections = $supabase->query('teacher_reflections', [
            'principal_feedback' => 'is.null',
            'select' => '*,profiles(id,full_name,nip,subject_specialty)',
            'order' => 'reflection_date.desc,id.desc',
            'limit' => 15,
        ], true);

        // 5. Riwayat Asesmen Supervisi KBM Klinis
        $supervisions = $supabase->query('kbm_supervisions', [
            'select' => '*,profiles!teacher_id(full_name,nip,subject_specialty)',
            'order' => 'supervision_date.desc,id.desc',
            'limit' => 6,
        ], true);

        return view('supervisor/index', [
            'title' => 'Dashboard Supervisi Kepala Sekolah - SMART MADANI',
            'totalGurus' => $totalGurus,
            'attendancesToday' => is_array($attendancesToday) ? $attendancesToday : [],
            'unattendedGurus' => $unattendedGurus,
            'pendingActivities' => is_array($pendingActivities) ? $pendingActivities : [],
            'pendingReflections' => is_array($pendingReflections) ? $pendingReflections : [],
            'supervisions' => is_array($supervisions) ? $supervisions : [],
        ]);
    }

    /**
     * Manajemen Seluruh Dewan Guru (Pemantauan, Portofolio & Asesmen Supervisi)
     */
    public function teachers()
    {
        $supabase = new SupabaseClient();

        // 1. Ambil seluruh data guru
        $rows = $supabase->query('profiles', [
            'role' => 'eq.guru',
            'select' => '*',
            'order' => 'full_name.asc',
        ], true);
        $teachers = is_array($rows) && !isset($rows['error']) ? $rows : [];

        // 2. Ambil statistik pendukung (presensi hari ini, jumlah jurnal, jumlah refleksi, rata-rata skor supervisi)
        $today = date('Y-m-d');
        $todayAttendances = $supabase->query('attendances', [
            'attendance_date' => 'eq.' . $today,
            'select' => 'user_id,check_in_time,status',
        ], true);
        $attMap = [];
        if (is_array($todayAttendances) && !isset($todayAttendances['error'])) {
            foreach ($todayAttendances as $att) {
                $attMap[$att['user_id']] = $att;
            }
        }

        // Ambil riwayat supervisi untuk hitung rata-rata skor per guru
        $allSupervisions = $supabase->query('kbm_supervisions', [
            'select' => 'teacher_id,deep_learning_score',
        ], true);
        $supMap = [];
        if (is_array($allSupervisions) && !isset($allSupervisions['error'])) {
            foreach ($allSupervisions as $sp) {
                $tId = $sp['teacher_id'];
                if (!isset($supMap[$tId])) {
                    $supMap[$tId] = [];
                }
                $supMap[$tId][] = (float) $sp['deep_learning_score'];
            }
        }

        // Gabungkan data ringkasan ke guru
        foreach ($teachers as &$t) {
            $t['today_attendance'] = $attMap[$t['id']] ?? null;
            if (isset($supMap[$t['id']]) && count($supMap[$t['id']]) > 0) {
                $t['avg_supervision'] = round(array_sum($supMap[$t['id']]) / count($supMap[$t['id']]), 2);
                $t['supervision_count'] = count($supMap[$t['id']]);
            } else {
                $t['avg_supervision'] = null;
                $t['supervision_count'] = 0;
            }
        }
        unset($t);

        return view('supervisor/teachers', [
            'title' => 'Manajemen Dewan Guru - SMART MADANI',
            'teachers' => $teachers,
        ]);
    }

    /**
     * Form Asesmen & Supervisi Klinis KBM Guru (4 Kompetensi + 3 Pilar Deep Learning)
     */
    public function createSupervision($teacherId)
    {
        $supabase = new SupabaseClient();
        $profile = $supabase->query('profiles', [
            'id' => 'eq.' . $teacherId,
            'select' => '*',
            'limit' => 1,
        ], true);

        if (empty($profile) || !isset($profile[0])) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        // Ambil riwayat supervisi terdahulu untuk guru ini
        $history = $supabase->query('kbm_supervisions', [
            'teacher_id' => 'eq.' . $teacherId,
            'select' => '*',
            'order' => 'supervision_date.desc',
            'limit' => 5,
        ], true);

        return view('supervisor/supervision_form', [
            'title' => 'Formulir Supervisi Klinis KBM - SMART MADANI',
            'teacher' => $profile[0],
            'history' => is_array($history) && !isset($history['error']) ? $history : [],
        ]);
    }

    /**
     * Simpan Hasil Asesmen Supervisi KBM Klinis
     */
    public function storeSupervision()
    {
        $supervisorId = session()->get('user_id');
        $teacherId = $this->request->getPost('teacher_id');

        $className = trim((string) $this->request->getPost('class_name'));
        $subject = trim((string) $this->request->getPost('subject'));
        $supervisionDate = $this->request->getPost('supervision_date') ?: date('Y-m-d');

        // 4 Pilar Standar Kompetensi Nasional (1.0 - 4.0)
        $pedagogic = (float) $this->request->getPost('score_pedagogic');
        $personality = (float) $this->request->getPost('score_personality');
        $social = (float) $this->request->getPost('score_social');
        $professional = (float) $this->request->getPost('score_professional');

        // 3 Pilar Deep Learning (Mindful, Meaningful, Joyful) (1.0 - 4.0)
        $mindful = (float) ($this->request->getPost('score_mindful') ?: 3.5);
        $meaningful = (float) ($this->request->getPost('score_meaningful') ?: 3.5);
        $joyful = (float) ($this->request->getPost('score_joyful') ?: 3.5);

        // Rata-rata indeks Deep Learning
        $deepLearning = round(($mindful + $meaningful + $joyful) / 3, 2);

        $coachingNotes = trim((string) $this->request->getPost('coaching_notes'));
        $followupAction = trim((string) $this->request->getPost('followup_action'));

        if (empty($className) || empty($subject) || empty($coachingNotes)) {
            return redirect()->back()->withInput()->with('error', 'Rombel, mata pelajaran, dan catatan coaching wajib diisi.');
        }

        $supabase = new SupabaseClient();

        // Data payload lengkap
        $payload = [
            'teacher_id' => $teacherId,
            'supervisor_id' => $supervisorId,
            'supervision_date' => $supervisionDate,
            'class_name' => $className,
            'subject' => $subject,
            'score_pedagogic' => $pedagogic,
            'score_personality' => $personality,
            'score_social' => $social,
            'score_professional' => $professional,
            'score_mindful' => $mindful,
            'score_meaningful' => $meaningful,
            'score_joyful' => $joyful,
            'deep_learning_score' => $deepLearning,
            'coaching_notes' => $coachingNotes,
            'followup_action' => $followupAction,
        ];

        $res = $supabase->insert('kbm_supervisions', $payload, true);

        // Jika kolom baru belum ada di DB Supabase, fallback ke skema dasar
        if (isset($res['error']) && $res['error']) {
            unset($payload['score_mindful'], $payload['score_meaningful'], $payload['score_joyful'], $payload['followup_action']);
            $res = $supabase->insert('kbm_supervisions', $payload, true);
        }

        if (isset($res['error']) && $res['error']) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan supervisi: ' . ($res['message'] ?? 'Periksa tabel kbm_supervisions di Supabase'));
        }

        return redirect()->to(base_url('supervisor'))->with('success', 'Asesmen supervisi klinis KBM guru berhasil diterbitkan!');
    }

    /**
     * Verifikasi Jurnal Aktivitas Guru (Setujui / Revisi dengan Catatan)
     */
    public function verifyActivity($id)
    {
        $status = $this->request->getPost('status') ?: 'approved'; // approved or revision
        $notes = trim((string) $this->request->getPost('verification_notes')) ?: null;
        $supervisorId = session()->get('user_id');

        $supabase = new SupabaseClient();
        $res = $supabase->update('teacher_activities', [
            'id' => 'eq.' . $id,
        ], [
            'verification_status' => $status,
            'verified_by' => $supervisorId,
            'verification_notes' => $notes,
            'updated_at' => date('c'),
        ], true);

        // Fallback jika verification_notes belum ada di tabel
        if (isset($res['error']) && $res['error']) {
            $supabase->update('teacher_activities', [
                'id' => 'eq.' . $id,
            ], [
                'verification_status' => $status,
                'verified_by' => $supervisorId,
                'updated_at' => date('c'),
            ], true);
        }

        $label = ($status === 'approved') ? 'DISETUJUI' : 'DIMINTA REVISI';
        return redirect()->back()->with('success', 'Jurnal aktivitas berhasil ' . $label . '.');
    }

    /**
     * Umpan Balik Refleksi Guru (Coaching Dialog Apresiatif)
     */
    public function feedbackReflection($id)
    {
        $feedback = trim((string) $this->request->getPost('principal_feedback'));
        $supervisorId = session()->get('user_id');

        if (empty($feedback)) {
            return redirect()->back()->with('error', 'Catatan umpan balik coaching tidak boleh kosong.');
        }

        $supabase = new SupabaseClient();
        $res = $supabase->update('teacher_reflections', [
            'id' => 'eq.' . $id,
        ], [
            'principal_feedback' => $feedback,
            'reviewed_by' => $supervisorId,
            'reviewed_at' => date('c'),
            'updated_at' => date('c'),
        ], true);

        if (isset($res['error']) && $res['error']) {
            return redirect()->back()->with('error', 'Gagal mengirim umpan balik: ' . ($res['message'] ?? ''));
        }

        return redirect()->back()->with('success', 'Umpan balik coaching refleksi berhasil dikirim ke guru!');
    }
}
