<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('is_logged_in')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = $session->get('role') ?? 'guru';
        $allowedRoles = $arguments ?? ['guru'];

        if (!in_array($userRole, $allowedRoles, true)) {
            if ($userRole === 'kepala_sekolah') {
                return redirect()->to(base_url('supervisor'))->with('error', 'Fitur tersebut khusus untuk akun Guru.');
            }
            return redirect()->to(base_url('dashboard'))->with('error', 'Akses ditolak: Fitur khusus Kepala Sekolah / Supervisor.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
