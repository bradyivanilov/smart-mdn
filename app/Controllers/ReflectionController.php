<?php

namespace App\Controllers;

use App\Libraries\SupabaseClient;

class ReflectionController extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        $supabase = new SupabaseClient();

        // 1. Refleksi guru
        $refRows = $supabase->query('teacher_reflections', [
            'user_id' => 'eq.' . $userId,
            'select' => '*',
            'order' => 'reflection_date.desc,id.desc',
        ]);
        $reflections = (!empty($refRows) && is_array($refRows) && !isset($refRows['error'])) ? $refRows : [];

        // 2. Feedback dari murid (5 terbaru)
        $fbRows = $supabase->query('student_feedbacks', [
            'teacher_id' => 'eq.' . $userId,
            'select' => '*',
            'order' => 'created_at.desc',
            'limit' => 5,
        ]);
        $feedbacks = (!empty($fbRows) && is_array($fbRows) && !isset($fbRows['error'])) ? $fbRows : [];

        return view('reflection/index', [
            'title' => 'Our Refleksi - Kemendikdasmen Framework',
            'reflections' => $reflections,
            'feedbacks' => $feedbacks,
        ]);
    }

    public function create()
    {
        $userId = session()->get('user_id');
        $supabase = new SupabaseClient();

        $actRows = $supabase->query('teacher_activities', [
            'user_id' => 'eq.' . $userId,
            'select' => 'id,title,activity_date',
            'order' => 'activity_date.desc',
        ]);
        $activities = (!empty($actRows) && is_array($actRows) && !isset($actRows['error'])) ? $actRows : [];

        return view('reflection/create', [
            'title' => 'Tulis Refleksi KBM Deep Learning',
            'activities' => $activities,
        ]);
    }

    public function store()
    {
        $userId = session()->get('user_id');
        $supabase = new SupabaseClient();

        $activityId = $this->request->getPost('activity_id') ?: null;
        $reflectionDate = $this->request->getPost('reflection_date') ?: date('Y-m-d');
        $situationAnalysis = trim((string) $this->request->getPost('situation_analysis'));
        $challengeIdentification = trim((string) $this->request->getPost('challenge_identification'));
        $actionPlan = trim((string) $this->request->getPost('action_plan'));
        $competencyLevel = (int) $this->request->getPost('competency_level');

        if (empty($situationAnalysis) || empty($challengeIdentification) || empty($actionPlan)) {
            return redirect()->back()->withInput()->with('error', 'Semua pertanyaan pemandu refleksi wajib diisi.');
        }

        $res = $supabase->insert('teacher_reflections', [
            'user_id' => $userId,
            'activity_id' => $activityId ? (int) $activityId : null,
            'reflection_date' => $reflectionDate,
            'situation_analysis' => $situationAnalysis,
            'challenge_identification' => $challengeIdentification,
            'action_plan' => $actionPlan,
            'competency_level' => $competencyLevel ?: 1,
        ]);

        if (isset($res['error']) && $res['error']) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan refleksi: ' . ($res['message'] ?? 'Error Supabase'));
        }

        return redirect()->to(base_url('reflection'))->with('success', 'Jurnal Refleksi berhasil disimpan!');
    }

    // Endpoint publik murid untuk Student Feedback Pulse (Refleksi Suara Murid via QR Code)
    public function studentPulse($teacherId)
    {
        $supabase = new SupabaseClient();
        // Cek apakah guru valid
        $teacherRows = $supabase->query('profiles', [
            'id' => 'eq.' . $teacherId,
            'select' => 'id,full_name,subject_specialty',
            'limit' => 1,
        ], true);

        $teacher = (!empty($teacherRows) && is_array($teacherRows) && !isset($teacherRows['error'])) ? $teacherRows[0] : null;

        return view('reflection/student_pulse', [
            'title' => 'Suara Murid - SMART MADANI',
            'teacherId' => $teacherId,
            'teacher' => $teacher,
        ]);
    }

    public function storeStudentPulse()
    {
        $supabase = new SupabaseClient();

        $teacherId = $this->request->getPost('teacher_id');
        $subjectName = trim((string) $this->request->getPost('subject_name'));
        $gradeClass = trim((string) $this->request->getPost('grade_class'));
        $joyfulScore = (int) $this->request->getPost('joyful_score');
        $meaningfulScore = (int) $this->request->getPost('meaningful_score');
        $mindfulScore = (int) $this->request->getPost('mindful_score');
        $studentNote = trim((string) $this->request->getPost('student_note'));

        if (empty($teacherId) || empty($joyfulScore) || empty($meaningfulScore) || empty($mindfulScore)) {
            return redirect()->back()->withInput()->with('error', 'Lengkapi skor evaluasi pembelajaran.');
        }

        // Simpan via service role (akses publik siswa)
        $supabase->insert('student_feedbacks', [
            'teacher_id' => $teacherId,
            'subject_name' => $subjectName,
            'grade_class' => $gradeClass,
            'joyful_score' => $joyfulScore,
            'meaningful_score' => $meaningfulScore,
            'mindful_score' => $mindfulScore,
            'student_note' => $studentNote,
        ], true);

        return view('reflection/student_pulse_success', [
            'title' => 'Terima Kasih - Suara Murid Tersimpan',
        ]);
    }
}
