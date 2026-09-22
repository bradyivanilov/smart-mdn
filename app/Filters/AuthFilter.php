<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $isLoggedIn = $session->get('is_logged_in');
        $expiresAt = $session->get('token_expires_at');

        if (!$isLoggedIn || !$session->get('access_token')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek jika JWT sudah kadaluarsa
        if ($expiresAt && time() >= $expiresAt) {
            $session->destroy();
            return redirect()->to(base_url('login'))->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
