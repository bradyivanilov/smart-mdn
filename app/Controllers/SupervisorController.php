<?php

namespace App\Controllers;

use App\Libraries\SupabaseClient;

class SupervisorController extends BaseController
{
    /**
     * Dashboard Kepala Sekolah: Metrik Sekolah, Monitoring Guru & Refleksi Menunggu Review
     */
    public function index()
    {
        $supabase = new SupabaseClient();

        // 1. Data semua guru
        $guruList = $supabase->query('profiles', [
            'role' => 'eq.guru',
            'select' => '*',
            'order' => 'full_name.asc',
        ], true);
        $gurus = (is_array($guruList) && !isset($guruList['error'])) ? $guruList : [];

        // 2. Presensi Guru Hari Ini
        $today = date('Y-m-d');
        $attRows = $supabase->query('attendances', [
            'attendance_date' => 'eq.' . $today,
            'select' => '*,profiles(full_name,nip,subject_specialty)',
        ], true);
        $attendancesToday = (is_array($attRows) && !isset($attRows['error'])) ? $attRows : [];

        // 3. Jurnal Aktivitas Menunggu Verifikasi
        $pendingActivities = $supabase->query('teacher_activities', [
            'verification_status' => 'eq.pending',
            'select' => '*,profiles(full_name,nip)',
            'order' => 'activity_date.desc',
            'limit' => 10,
        ], true);

        // 4. Refleksi Menunggu Feedback Kepala Sekolah
        $pendingReflections = $supabase->query('teacher_reflections', [
            'principal_feedback' => 'is.null',
            'select' => '*,profiles(full_name,nip,subject_specialty)',
            'order' => 'reflection_date.desc',
            'limit' => 10,
        ], true);

        // 5. Riwayat Supervisi KBM yang sudah dilakukan
        $supervisions = $supabase->query('kbm_supervisions', [
            'select' => '*,profiles!teacher_id(full_name,nip)',
            'order' => 'supervision_date.desc',
            'limit' => 5,
        ], true);

        return view('supervisor/index', [
            'title' => 'Dashboard Supervisi Kepala Sekolah - SMART MADANI',
            'totalGurus' => count($gurus),
            'attendancesToday' => is_array($attendancesToday) ? $attendancesToday : [],
            'pendingActivities' => is_array($pendingActivities) ? $pendingActivities : [],
            'pendingReflections' => is_array($pendingReflections) ? $pendingReflections : [],
            'supervisions' => is_array($supervisions) ? $supervisions : [],
        ]);
    }

    /**
     * Halaman Monitoring Seluruh Guru
     */
    public function teachers()
    {
        $supabase = new SupabaseClient();
        $rows = $supabase->query('profiles', [
            'role' => 'eq.guru',
            'select' => '*',
            'order' => 'full_name.asc',
        ], true);

        return view('supervisor/teachers', [
            'title' => 'Daftar Guru & Asesmen - SMART MADANI',
            'teachers' => is_array($rows) ? $rows : [],
        ]);
    }

    /**
     * Form Asesmen & Supervisi Klinis KBM Guru
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

        return view('supervisor/supervision_form', [
            'title' => 'Formulir Supervisi KBM Deep Learning',
            'teacher' => $profile[0],
        ]);
    }

    /**
     * Simpan Skor Rubrik Supervisi (Pedagogik, Kepribadian, Sosial, Profesional & Deep Learning)
     */
    public function storeSupervision()
    {
        $supervisorId = session()->get('user_id');
        $teacherId = $this->request->getPost('teacher_id');

        $pedagogic = (float) $this->request->getPost('score_pedagogic');
        $personality = (float) $this->request->getPost('score_personality');
        $social = (float) $this->request->getPost('score_social');
        $professional = (float) $this->request->getPost('score_professional');
        $deepLearning = (float) $this->request->getPost('deep_learning_score');
        $notes = trim((string) $this->request->getPost('coaching_notes'));
        $className = trim((string) $this->request->getPost('class_name'));
        $subject = trim((string) $this->request->getPost('subject'));

        $supabase = new SupabaseClient();
        $res = $supabase->insert('kbm_supervisions', [
            'teacher_id' => $teacherId,
            'supervisor_id' => $supervisorId,
            'supervision_date' => date('Y-m-d'),
            'class_name' => $className,
            'subject' => $subject,
            'score_pedagogic' => $pedagogic,
            'score_personality' => $personality,
            'score_social' => $social,
            'score_professional' => $professional,
            'deep_learning_score' => $deepLearning,
            'coaching_notes' => $notes,
        ], true);

        if (isset($res['error']) && $res['error']) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan supervisi: ' . ($res['message'] ?? ''));
        }

        return redirect()->to(base_url('supervisor'))->with('success', 'Asesmen supervisi KBM guru berhasil disimpan!');
    }

    /**
     * Verifikasi Jurnal Aktivitas Guru
     */
    public function verifyActivity($id)
    {
        $status = $this->request->getPost('status') ?: 'approved'; // approved or revision
        $notes = trim((string) $this->request->getPost('verification_notes')) ?: null;
        $supervisorId = session()->get('user_id');

        $supabase = new SupabaseClient();
        $supabase->update('teacher_activities', [
            'id' => 'eq.' . $id,
        ], [
            'verification_status' => $status,
            'verified_by' => $supervisorId,
            'verification_notes' => $notes,
            'updated_at' => date('c'),
        ], true);

        return redirect()->back()->with('success', 'Status jurnal aktivitas diperbarui menjadi ' . strtoupper($status));
    }

    /**
     * Berikan Catatan / Feedback Refleksi Guru
     */
    public function feedbackReflection($id)
    {
        $feedback = trim((string) $this->request->getPost('principal_feedback'));
        $supervisorId = session()->get('user_id');

        if (empty($feedback)) {
            return redirect()->back()->with('error', 'Catatan umpan balik tidak boleh kosong.');
        }

        $supabase = new SupabaseClient();
        $supabase->update('teacher_reflections', [
            'id' => 'eq.' . $id,
        ], [
            'principal_feedback' => $feedback,
            'reviewed_by' => $supervisorId,
            'reviewed_at' => date('c'),
            'updated_at' => date('c'),
        ], true);

        return redirect()->back()->with('success', 'Umpan balik refleksi berhasil dikirim ke guru!');
    }
}
