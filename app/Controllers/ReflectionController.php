<?php

namespace App\Controllers;

use App\Libraries\SupabaseClient;

class ReflectionController extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        $supabase = new SupabaseClient();

        // Refleksi guru
        $refRows = $supabase->query('teacher_reflections', [
            'user_id' => 'eq.' . $userId,
            'select' => '*',
            'order' => 'reflection_date.desc,id.desc',
        ]);
        $reflections = (!empty($refRows) && is_array($refRows) && !isset($refRows['error'])) ? $refRows : [];

        // Ambil observasi rekan sejawat di mana guru ini adalah host/guru yang diobservasi
        $peerNotes = $supabase->query('peer_observations', [
            'host_teacher_id' => 'eq.' . $userId,
            'select' => '*,profiles!peer_observations_observer_teacher_id_fkey(full_name,nip)',
            'order' => 'observation_date.desc',
            'limit' => 5,
        ], true);

        return view('reflection/index', [
            'title' => 'Our Refleksi - Kemendikdasmen Framework',
            'reflections' => $reflections,
            'peerNotes' => is_array($peerNotes) && !isset($peerNotes['error']) ? $peerNotes : [],
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

        // Daftar rekan guru untuk opsi peer mentor (Level 4: Pengimbas)
        $teacherRows = $supabase->query('profiles', [
            'role' => 'eq.guru',
            'id' => 'neq.' . $userId,
            'select' => 'id,full_name,nip,subject_specialty',
            'order' => 'full_name.asc',
        ], true);
        $peers = is_array($teacherRows) && !isset($teacherRows['error']) ? $teacherRows : [];

        return view('reflection/create', [
            'title' => 'Tulis Refleksi KBM Deep Learning',
            'activities' => $activities,
            'peers' => $peers,
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
        $peerMentorId = $this->request->getPost('peer_mentor_id') ?: null;
        $evidenceUrl = trim((string) $this->request->getPost('evidence_url')) ?: null;

        if (empty($situationAnalysis) || empty($challengeIdentification) || empty($actionPlan)) {
            return redirect()->back()->withInput()->with('error', 'Semua pertanyaan pemandu refleksi wajib diisi.');
        }

        $payload = [
            'user_id' => $userId,
            'activity_id' => $activityId ? (int) $activityId : null,
            'reflection_date' => $reflectionDate,
            'situation_analysis' => $situationAnalysis,
            'challenge_identification' => $challengeIdentification,
            'action_plan' => $actionPlan,
            'competency_level' => $competencyLevel ?: 1,
            'peer_mentor_id' => $peerMentorId,
            'evidence_url' => $evidenceUrl,
        ];

        $res = $supabase->insert('teacher_reflections', $payload);

        // Fallback jika kolom baru belum ada di Supabase
        if (isset($res['error']) && $res['error']) {
            unset($payload['peer_mentor_id'], $payload['evidence_url']);
            $res = $supabase->insert('teacher_reflections', $payload);
        }

        if (isset($res['error']) && $res['error']) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan refleksi: ' . ($res['message'] ?? 'Error Supabase'));
        }

        return redirect()->to(base_url('reflection'))->with('success', 'Jurnal Refleksi Kemendikdasmen berhasil disimpan!');
    }

    /**
     * Modul Peer Observation / Lesson Study Antarguru
     */
    public function peerObservations()
    {
        $userId = session()->get('user_id');
        $supabase = new SupabaseClient();

        // 1. Rekan guru lain yang bisa diobservasi
        $guruList = $supabase->query('profiles', [
            'role' => 'eq.guru',
            'id' => 'neq.' . $userId,
            'select' => 'id,full_name,nip,subject_specialty',
            'order' => 'full_name.asc',
        ], true);

        // 2. Observasi yang pernah saya lakukan (sebagai observer)
        $myObservations = $supabase->query('peer_observations', [
            'observer_teacher_id' => 'eq.' . $userId,
            'select' => '*,profiles!peer_observations_host_teacher_id_fkey(full_name,nip,subject_specialty)',
            'order' => 'observation_date.desc',
        ], true);

        // 3. Catatan observasi yang saya terima dari rekan lain (sebagai host)
        $receivedObservations = $supabase->query('peer_observations', [
            'host_teacher_id' => 'eq.' . $userId,
            'select' => '*,profiles!peer_observations_observer_teacher_id_fkey(full_name,nip,subject_specialty)',
            'order' => 'observation_date.desc',
        ], true);

        return view('reflection/peer_observation', [
            'title' => 'Peer Observation & Lesson Study - SMART MADANI',
            'gurus' => is_array($guruList) && !isset($guruList['error']) ? $guruList : [],
            'myObservations' => is_array($myObservations) && !isset($myObservations['error']) ? $myObservations : [],
            'receivedObservations' => is_array($receivedObservations) && !isset($receivedObservations['error']) ? $receivedObservations : [],
        ]);
    }

    /**
     * Simpan Lembar Observasi Teman Sejawat
     */
    public function storePeerObservation()
    {
        $observerId = session()->get('user_id');
        $hostTeacherId = $this->request->getPost('host_teacher_id');
        $observationDate = $this->request->getPost('observation_date') ?: date('Y-m-d');
        $className = trim((string) $this->request->getPost('class_name'));
        $subject = trim((string) $this->request->getPost('subject'));
        $mindfulNotes = trim((string) $this->request->getPost('mindful_notes'));
        $meaningfulNotes = trim((string) $this->request->getPost('meaningful_notes'));
        $joyfulNotes = trim((string) $this->request->getPost('joyful_notes'));
        $constructiveFeedback = trim((string) $this->request->getPost('constructive_feedback'));

        if (empty($hostTeacherId) || empty($constructiveFeedback) || empty($className) || empty($subject)) {
            return redirect()->back()->withInput()->with('error', 'Guru yang diobservasi, kelas, mapel, dan rekomendasi konstruktif wajib diisi.');
        }

        $supabase = new SupabaseClient();
        $payload = [
            'host_teacher_id' => $hostTeacherId,
            'observer_teacher_id' => $observerId,
            'observation_date' => $observationDate,
            'class_name' => $className,
            'subject' => $subject,
            'mindful_notes' => $mindfulNotes ?: null,
            'meaningful_notes' => $meaningfulNotes ?: null,
            'joyful_notes' => $joyfulNotes ?: null,
            'constructive_feedback' => $constructiveFeedback,
            'status' => 'submitted',
        ];

        $res = $supabase->insert('peer_observations', $payload, true);

        if (isset($res['error']) && $res['error']) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan observasi rekan: ' . ($res['message'] ?? 'Periksa tabel peer_observations'));
        }

        return redirect()->to(base_url('reflection/peer-observation'))->with('success', 'Hasil observasi teman sejawat berhasil disimpan & dikirim ke rekan guru!');
    }
}
