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

        return view('reflection/index', [
            'title' => 'Our Refleksi - Kemendikdasmen Framework',
            'reflections' => $reflections,
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
}
