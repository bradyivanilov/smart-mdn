<?php

namespace App\Controllers;

use App\Libraries\SupabaseClient;

class SupervisorController extends BaseController
{
    /**
     * Dashboard Kepala Sekolah:
     * - Executive Summary & Indikator Sekolah
     * - Antrean Verifikasi Jurnal KBM Guru
     * - Antrean Umpan Balik Refleksi Guru
     * - Kurasi Karya & Modul Ajar Terbaru
     * - Riwayat Asesmen Supervisi Klinis
     */
    public function index()
    {
        $supabase = new SupabaseClient();

        // 1. Data seluruh dewan guru aktif
        $guruList = $supabase->query('profiles', [
            'role' => 'eq.guru',
            'select' => '*',
            'order' => 'full_name.asc',
        ], true);
        $gurus = (is_array($guruList) && !isset($guruList['error'])) ? $guruList : [];
        $totalGurus = count($gurus);

        // 2. Presensi Guru Hari Ini & Daftar yang Belum Presensi
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

        // 3. Jurnal Aktivitas Menunggu Verifikasi (Explicit fkey agar tidak ambigu)
        $pendingActivities = $supabase->query('teacher_activities', [
            'verification_status' => 'eq.pending',
            'select' => '*,profiles!teacher_activities_user_id_fkey(id,full_name,nip,subject_specialty)',
            'order' => 'activity_date.desc,id.desc',
            'limit' => 15,
        ], true);

        if (!is_array($pendingActivities) || isset($pendingActivities['error']) || isset($pendingActivities['code'])) {
            $pendingActivities = $supabase->query('teacher_activities', [
                'verification_status' => 'eq.pending',
                'select' => '*',
                'order' => 'activity_date.desc,id.desc',
                'limit' => 15,
            ], true);
            if (is_array($pendingActivities) && !isset($pendingActivities['error'])) {
                $guruMap = array_column($gurus, null, 'id');
                foreach ($pendingActivities as &$pa) {
                    if (is_array($pa)) {
                        $pa['profiles'] = $guruMap[$pa['user_id']] ?? null;
                    }
                }
                unset($pa);
            } else {
                $pendingActivities = [];
            }
        }

        // 4. Refleksi Menunggu Umpan Balik / Coaching Feedback (Explicit fkey)
        $pendingReflections = $supabase->query('teacher_reflections', [
            'principal_feedback' => 'is.null',
            'select' => '*,profiles!teacher_reflections_user_id_fkey(id,full_name,nip,subject_specialty)',
            'order' => 'reflection_date.desc,id.desc',
            'limit' => 15,
        ], true);

        if (!is_array($pendingReflections) || isset($pendingReflections['error']) || isset($pendingReflections['code'])) {
            $pendingReflections = $supabase->query('teacher_reflections', [
                'principal_feedback' => 'is.null',
                'select' => '*',
                'order' => 'reflection_date.desc,id.desc',
                'limit' => 15,
            ], true);
            if (is_array($pendingReflections) && !isset($pendingReflections['error'])) {
                $guruMap = array_column($gurus, null, 'id');
                foreach ($pendingReflections as &$pr) {
                    if (is_array($pr)) {
                        $pr['profiles'] = $guruMap[$pr['user_id']] ?? null;
                    }
                }
                unset($pr);
            } else {
                $pendingReflections = [];
            }
        }

        // 5. Karya & Modul Ajar Terbaru yang Butuh Kurasi (Explicit fkey)
        $recentCreativities = $supabase->query('teacher_creativities', [
            'select' => '*,profiles!teacher_creativities_user_id_fkey(id,full_name,nip)',
            'order' => 'created_at.desc',
            'limit' => 6,
        ], true);

        // 6. Riwayat Asesmen Supervisi KBM Klinis
        $supervisions = $supabase->query('kbm_supervisions', [
            'select' => '*,profiles!teacher_id(full_name,nip,subject_specialty)',
            'order' => 'supervision_date.desc,id.desc',
            'limit' => 8,
        ], true);

        // Fallback jika tabel kbm_supervisions belum dibuat di Supabase
        $supervisionTableReady = true;
        if (isset($supervisions['error']) && ($supervisions['status'] ?? 0) === 404) {
            $supervisionTableReady = false;
            $supervisions = [];
        }

        return view('supervisor/index', [
            'title' => 'Portal Supervisi Klinis Kepala Sekolah - SMART MADANI',
            'totalGurus' => $totalGurus,
            'attendancesToday' => is_array($attendancesToday) ? $attendancesToday : [],
            'unattendedGurus' => $unattendedGurus,
            'pendingActivities' => is_array($pendingActivities) && !isset($pendingActivities['error']) ? $pendingActivities : [],
            'pendingReflections' => is_array($pendingReflections) && !isset($pendingReflections['error']) ? $pendingReflections : [],
            'recentCreativities' => is_array($recentCreativities) && !isset($recentCreativities['error']) ? $recentCreativities : [],
            'supervisions' => is_array($supervisions) && !isset($supervisions['error']) ? $supervisions : [],
            'supervisionTableReady' => $supervisionTableReady,
        ]);
    }

    /**
     * Manajemen Seluruh Dewan Guru (Daftar guru lengkap dengan indikator kinerja)
     */
    public function teachers()
    {
        $supabase = new SupabaseClient();

        $rows = $supabase->query('profiles', [
            'role' => 'eq.guru',
            'select' => '*',
            'order' => 'full_name.asc',
        ], true);
        $teachers = is_array($rows) && !isset($rows['error']) ? $rows : [];

        // Presensi hari ini
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

        // Riwayat supervisi untuk agregat skor
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
     * Halaman Detail Audit Kinerja Satu Guru (360° Assessment View)
     * Kepala Sekolah dapat memantau seluruh riwayat presensi, jurnal 4 kompetensi,
     * modul ajar yang diunggah, dan jurnal refleksi sebelum memberi penilaian.
     */
    public function teacherDetail($teacherId)
    {
        $supabase = new SupabaseClient();

        // 1. Profil Guru
        $profileRows = $supabase->query('profiles', [
            'id' => 'eq.' . $teacherId,
            'select' => '*',
            'limit' => 1,
        ], true);

        if (empty($profileRows) || !isset($profileRows[0])) {
            return redirect()->to(base_url('supervisor/teachers'))->with('error', 'Data guru tidak ditemukan.');
        }
        $teacher = $profileRows[0];

        // 2. Riwayat Presensi Guru (15 hari terakhir)
        $attendances = $supabase->query('attendances', [
            'user_id' => 'eq.' . $teacherId,
            'select' => '*',
            'order' => 'attendance_date.desc',
            'limit' => 15,
        ], true);

        // 3. Seluruh Jurnal Aktivitas KBM Guru
        $activities = $supabase->query('teacher_activities', [
            'user_id' => 'eq.' . $teacherId,
            'select' => '*',
            'order' => 'activity_date.desc,id.desc',
        ], true);

        // 4. Seluruh Karya & Modul Ajar Guru
        $creativities = $supabase->query('teacher_creativities', [
            'user_id' => 'eq.' . $teacherId,
            'select' => '*',
            'order' => 'created_at.desc',
        ], true);

        // 5. Seluruh Jurnal Refleksi KBM Guru
        $reflections = $supabase->query('teacher_reflections', [
            'user_id' => 'eq.' . $teacherId,
            'select' => '*',
            'order' => 'reflection_date.desc',
        ], true);

        // 6. Riwayat Supervisi Klinis Guru Ini
        $supervisions = $supabase->query('kbm_supervisions', [
            'teacher_id' => 'eq.' . $teacherId,
            'select' => '*',
            'order' => 'supervision_date.desc',
        ], true);

        return view('supervisor/teacher_detail', [
            'title' => 'Audit Kinerja Guru: ' . $teacher['full_name'],
            'teacher' => $teacher,
            'attendances' => is_array($attendances) && !isset($attendances['error']) ? $attendances : [],
            'activities' => is_array($activities) && !isset($activities['error']) ? $activities : [],
            'creativities' => is_array($creativities) && !isset($creativities['error']) ? $creativities : [],
            'reflections' => is_array($reflections) && !isset($reflections['error']) ? $reflections : [],
            'supervisions' => is_array($supervisions) && !isset($supervisions['error']) ? $supervisions : [],
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

        // 3 Pilar Deep Learning (1.0 - 4.0)
        $mindful = (float) ($this->request->getPost('score_mindful') ?: 3.5);
        $meaningful = (float) ($this->request->getPost('score_meaningful') ?: 3.5);
        $joyful = (float) ($this->request->getPost('score_joyful') ?: 3.5);

        $deepLearning = round(($mindful + $meaningful + $joyful) / 3, 2);

        $coachingNotes = trim((string) $this->request->getPost('coaching_notes'));
        $followupAction = trim((string) $this->request->getPost('followup_action'));

        if (empty($className) || empty($subject) || empty($coachingNotes)) {
            return redirect()->back()->withInput()->with('error', 'Rombel, mata pelajaran, dan catatan coaching wajib diisi.');
        }

        $supabase = new SupabaseClient();

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

        // Fallback jika kolom mindful/meaningful/joyful belum ada di tabel lama
        if (isset($res['error']) && $res['error']) {
            unset($payload['score_mindful'], $payload['score_meaningful'], $payload['score_joyful'], $payload['followup_action']);
            $res = $supabase->insert('kbm_supervisions', $payload, true);
        }

        if (isset($res['error']) && $res['error']) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan supervisi: ' . ($res['message'] ?? 'Pastikan tabel kbm_supervisions sudah dibuat di SQL Editor Supabase.'));
        }

        return redirect()->to(base_url('supervisor/teacher/' . $teacherId))->with('success', 'Asesmen supervisi klinis KBM guru berhasil diterbitkan!');
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
        return redirect()->back()->with('success', 'Jurnal aktivitas KBM guru berhasil ' . $label . '.');
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

    /**
     * Kurasi Modul / Karya Guru (Sematkan Badge "Karya Terpilih" & Catatan Kurasi)
     */
    public function curateCreativity($id)
    {
        $supervisorId = session()->get('user_id');
        $isFeatured = $this->request->getPost('is_featured') === '1';
        $curatorNotes = trim((string) $this->request->getPost('curator_notes'));

        $supabase = new SupabaseClient();
        $res = $supabase->update('teacher_creativities', [
            'id' => 'eq.' . $id,
        ], [
            'is_featured' => $isFeatured,
            'curator_notes' => $curatorNotes,
            'curated_by' => $supervisorId,
            'curated_at' => date('c'),
            'updated_at' => date('c'),
        ], true);

        // Fallback jika kolom curator_notes belum ada di DB Supabase
        if (isset($res['error']) && $res['error']) {
            $supabase->update('teacher_creativities', [
                'id' => 'eq.' . $id,
            ], [
                'is_featured' => $isFeatured,
                'updated_at' => date('c'),
            ], true);
        }

        $statusMsg = $isFeatured ? 'disematkan sebagai Karya Terpilih Kurikulum!' : 'diperbarui status kurasinya.';
        return redirect()->back()->with('success', 'Modul ajar/karya guru berhasil ' . $statusMsg);
    }
}
