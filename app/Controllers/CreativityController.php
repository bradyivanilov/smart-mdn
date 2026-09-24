<?php

namespace App\Controllers;

use App\Libraries\SupabaseClient;

class CreativityController extends BaseController
{
    public function index()
    {
        $supabase = new SupabaseClient();

        // Feed publik: ambil semua karya terbaru (Explicit foreign key user_id agar tidak ambigu dengan curated_by)
        $rows = $supabase->query('teacher_creativities', [
            'select' => '*,profiles!teacher_creativities_user_id_fkey(full_name,avatar_url)',
            'order' => 'is_featured.desc,created_at.desc',
        ]);

        if (!is_array($rows) || isset($rows['error']) || isset($rows['code'])) {
            $rows = $supabase->query('teacher_creativities', [
                'select' => '*',
                'order' => 'is_featured.desc,created_at.desc',
            ]);
            $creativities = is_array($rows) && !isset($rows['error']) ? $rows : [];
        } else {
            $creativities = $rows;
        }

        return view('creativity/index', [
            'title' => 'Our Creativity - E-Library & Modul Ajar',
            'creativities' => $creativities,
        ]);
    }

    public function create()
    {
        return view('creativity/create', [
            'title' => 'Unggah Karya & Modul Ajar',
        ]);
    }

    public function store()
    {
        $userId = session()->get('user_id');
        $supabase = new SupabaseClient();

        $title = trim((string) $this->request->getPost('title'));
        $category = $this->request->getPost('category');
        $deepLearningFocus = $this->request->getPost('deep_learning_focus') ?: 'all';
        $description = trim((string) $this->request->getPost('description'));
        $videoEmbedUrl = trim((string) $this->request->getPost('video_embed_url')) ?: null;

        if (empty($title) || empty($category)) {
            return redirect()->back()->withInput()->with('error', 'Judul dan format karya wajib diisi.');
        }

        // File upload lokal XAMPP
        $file = $this->request->getFile('file_attachment');
        $filePath = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $targetDir = FCPATH . 'uploads/creativities/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $file->move($targetDir, $newName);
            $filePath = 'uploads/creativities/' . $newName;
        }

        $res = $supabase->insert('teacher_creativities', [
            'user_id' => $userId,
            'title' => $title,
            'category' => $category,
            'deep_learning_focus' => $deepLearningFocus,
            'description' => $description,
            'file_attachment_url' => $filePath,
            'video_embed_url' => $videoEmbedUrl,
            'is_featured' => false,
            'likes_count' => 0,
        ]);

        if (isset($res['error']) && $res['error']) {
            return redirect()->back()->withInput()->with('error', 'Gagal mempublikasikan karya: ' . ($res['message'] ?? 'Error Supabase'));
        }

        return redirect()->to(base_url('creativity'))->with('success', 'Karya pedagogis berhasil dipublikasikan!');
    }

    public function like($id)
    {
        $supabase = new SupabaseClient();

        // Ambil data karya saat ini
        $rows = $supabase->query('teacher_creativities', [
            'id' => 'eq.' . $id,
            'select' => 'id,likes_count',
            'limit' => 1,
        ]);

        if (!empty($rows) && is_array($rows) && !isset($rows['error'])) {
            $current = $rows[0];
            $newCount = ((int) ($current['likes_count'] ?? 0)) + 1;

            // Update likes count via service role
            $supabase->update('teacher_creativities', [
                'id' => 'eq.' . $id,
            ], [
                'likes_count' => $newCount,
            ], true);
        }

        return redirect()->back()->with('success', 'Apresiasi karya tercatat!');
    }
}
