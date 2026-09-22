<?php

namespace App\Controllers;

use App\Libraries\SupabaseClient;

class AttendanceController extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        $supabase = new SupabaseClient();

        $today = date('Y-m-d');

        // Query kehadiran hari ini
        $todayRows = $supabase->query('attendances', [
            'user_id' => 'eq.' . $userId,
            'attendance_date' => 'eq.' . $today,
            'select' => '*',
            'limit' => 1,
        ]);
        $todayAttendance = (!empty($todayRows) && is_array($todayRows) && !isset($todayRows['error'])) ? $todayRows[0] : null;

        // Query riwayat kehadiran 10 hari terakhir
        $historyRows = $supabase->query('attendances', [
            'user_id' => 'eq.' . $userId,
            'select' => '*',
            'order' => 'attendance_date.desc',
            'limit' => 10,
        ]);
        $history = (!empty($historyRows) && is_array($historyRows) && !isset($historyRows['error'])) ? $historyRows : [];

        return view('attendance/index', [
            'title' => 'Smart Attendance - SMART MADANI',
            'todayAttendance' => $todayAttendance,
            'history' => $history,
        ]);
    }

    public function record()
    {
        $userId = session()->get('user_id');
        $supabase = new SupabaseClient();
        $today = date('Y-m-d');

        $type = $this->request->getPost('type'); // 'check_in' or 'check_out'
        $lat = $this->request->getPost('latitude');
        $lng = $this->request->getPost('longitude');
        $notes = trim((string) $this->request->getPost('notes')) ?: 'Presensi Mandiri Guru';
        $photoData = $this->request->getPost('photo_base64');

        // Simpan foto Base64 ke lokal XAMPP (uploads/)
        $photoPath = null;
        if (!empty($photoData) && str_starts_with($photoData, 'data:image')) {
            $parts = explode(',', $photoData);
            if (isset($parts[1])) {
                $decoded = base64_decode($parts[1]);
                $filename = 'attend_' . $userId . '_' . time() . '.jpg';
                $targetDir = FCPATH . 'uploads/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                file_put_contents($targetDir . $filename, $decoded);
                $photoPath = 'uploads/' . $filename;
            }
        }

        // Cek apakah sudah ada catatan hari ini
        $existingRows = $supabase->query('attendances', [
            'user_id' => 'eq.' . $userId,
            'attendance_date' => 'eq.' . $today,
            'select' => '*',
            'limit' => 1,
        ]);
        $existing = (!empty($existingRows) && is_array($existingRows) && !isset($existingRows['error'])) ? $existingRows[0] : null;

        $currentTime = date('H:i:s');
        $status = 'hadir';
        if ($currentTime > '07:15:00' && $type === 'check_in') {
            $status = 'terlambat';
        }

        if ($existing) {
            if ($type === 'check_out') {
                $updateRes = $supabase->update('attendances', [
                    'id' => 'eq.' . $existing['id'],
                ], [
                    'check_out_time' => $currentTime,
                    'notes' => ($existing['notes'] ?? '') . ' | Pulang: ' . $notes,
                    'updated_at' => date('c'),
                ]);

                if (isset($updateRes['error']) && $updateRes['error']) {
                    return redirect()->to(base_url('attendance'))->with('error', 'Gagal mencatat pulang: ' . ($updateRes['message'] ?? 'Error Supabase'));
                }

                return redirect()->to(base_url('attendance'))->with('success', 'Presensi pulang berhasil dicatat!');
            } else {
                return redirect()->to(base_url('attendance'))->with('error', 'Anda sudah melakukan presensi masuk hari ini.');
            }
        } else {
            $insertData = [
                'user_id' => $userId,
                'attendance_date' => $today,
                'check_in_time' => $currentTime,
                'latitude_in' => $lat ? (float) $lat : null,
                'longitude_in' => $lng ? (float) $lng : null,
                'photo_in_url' => $photoPath,
                'status' => $status,
                'notes' => $notes,
            ];

            $insertRes = $supabase->insert('attendances', $insertData);

            if (isset($insertRes['error']) && $insertRes['error']) {
                return redirect()->to(base_url('attendance'))->with('error', 'Gagal mencatat presensi: ' . ($insertRes['message'] ?? 'Error Supabase'));
            }

            return redirect()->to(base_url('attendance'))->with('success', 'Presensi masuk berhasil dicatat! Status: ' . strtoupper($status));
        }
    }
}
