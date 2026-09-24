<?php

namespace App\Controllers;

use App\Libraries\SupabaseClient;

class ActivityController extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        $supabase = new SupabaseClient();

        $rows = $supabase->query('teacher_activities', [
            'user_id' => 'eq.' . $userId,
            'select' => '*',
            'order' => 'activity_date.desc,id.desc',
        ]);
        $activities = (!empty($rows) && is_array($rows) && !isset($rows['error'])) ? $rows : [];

        return view('activity/index', [
            'title' => 'Our Activity - Jurnal 4 Kompetensi',
            'activities' => $activities,
        ]);
    }

    public function create()
    {
        return view('activity/create', [
            'title' => 'Catat Jurnal KBM Deep Learning Baru',
        ]);
    }

    public function store()
    {
        $userId = session()->get('user_id');
        $supabase = new SupabaseClient();

        $activityDate = $this->request->getPost('activity_date') ?: date('Y-m-d');
        $competencyType = $this->request->getPost('competency_type');
        $title = trim((string) $this->request->getPost('title'));
        $description = trim((string) $this->request->getPost('description'));
        $deepLearningPillar = $this->request->getPost('deep_learning_pillar') ?: 'integrated';

        // 3 Parameter Pedagogis Baru
        $learningObjective = trim((string) $this->request->getPost('learning_objective'));
        $differentiationStrategy = trim((string) $this->request->getPost('differentiation_strategy'));
        $contextualProblem = trim((string) $this->request->getPost('contextual_problem'));

        if (empty($title) || empty($description) || empty($competencyType)) {
            return redirect()->back()->withInput()->with('error', 'Judul, kompetensi, dan deskripsi aktivitas wajib diisi.');
        }

        // Handle File upload lokal XAMPP
        $file = $this->request->getFile('evidence_file');
        $filePath = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $targetDir = FCPATH . 'uploads/activities/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $file->move($targetDir, $newName);
            $filePath = 'uploads/activities/' . $newName;
        }

        $payload = [
            'user_id' => $userId,
            'activity_date' => $activityDate,
            'competency_type' => $competencyType,
            'title' => $title,
            'description' => $description,
            'deep_learning_pillar' => $deepLearningPillar,
            'learning_objective' => $learningObjective ?: null,
            'differentiation_strategy' => $differentiationStrategy ?: null,
            'contextual_problem' => $contextualProblem ?: null,
            'evidence_file_url' => $filePath,
            'verification_status' => 'pending',
        ];

        $res = $supabase->insert('teacher_activities', $payload);

        // Fallback jika kolom baru belum ada di Supabase
        if (isset($res['error']) && $res['error']) {
            unset($payload['learning_objective'], $payload['differentiation_strategy'], $payload['contextual_problem']);
            $res = $supabase->insert('teacher_activities', $payload);
        }

        if (isset($res['error']) && $res['error']) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan aktivitas: ' . ($res['message'] ?? 'Error Supabase'));
        }

        return redirect()->to(base_url('activity'))->with('success', 'Jurnal aktivitas KBM berhasil disimpan dan masuk ke antrean verifikasi Kepala Sekolah!');
    }
}
