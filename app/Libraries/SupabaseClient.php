<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;

class SupabaseClient
{
    protected string $url;
    protected string $anonKey;
    protected string $serviceKey;
    protected ?string $userToken = null;

    public function __construct(?string $userToken = null)
    {
        $this->url = rtrim(env('SUPABASE_URL', ''), '/');
        $this->anonKey = env('SUPABASE_ANON_KEY', '');
        $this->serviceKey = env('SUPABASE_SERVICE_KEY', '');
        $this->userToken = $userToken ?? session()->get('access_token');
    }

    /**
     * Set user JWT token manually
     */
    public function setToken(string $token): self
    {
        $this->userToken = $token;
        return $this;
    }

    /**
     * Supabase Auth: Sign Up with email & password
     */
    public function signUp(string $email, string $password, array $userData = []): array
    {
        $endpoint = $this->url . '/auth/v1/signup';
        $payload = [
            'email' => $email,
            'password' => $password,
            'data' => $userData,
        ];

        return $this->request('POST', $endpoint, $payload, false);
    }

    /**
     * Supabase Admin Auth: Create user with auto-confirmed email (bypass email confirmation)
     */
    public function adminCreateUser(string $email, string $password, array $userData = [], bool $autoConfirm = true): array
    {
        $endpoint = $this->url . '/auth/v1/admin/users';
        $payload = [
            'email' => $email,
            'password' => $password,
            'email_confirm' => $autoConfirm,
            'user_metadata' => $userData,
        ];

        return $this->request('POST', $endpoint, $payload, true);
    }

    /**
     * Supabase Auth: Sign In with email & password
     */
    public function signIn(string $email, string $password): array
    {
        $endpoint = $this->url . '/auth/v1/token?grant_type=password';
        $payload = [
            'email' => $email,
            'password' => $password,
        ];

        return $this->request('POST', $endpoint, $payload, false);
    }

    /**
     * PostgREST: Query table (GET)
     * e.g. query('profiles', ['select' => '*', 'nip' => 'eq.123', 'order' => 'created_at.desc'])
     */
    public function query(string $table, array $params = [], bool $useServiceRole = false): array
    {
        $queryString = http_build_query($params);
        $endpoint = $this->url . '/rest/v1/' . $table . ($queryString ? '?' . $queryString : '');

        return $this->request('GET', $endpoint, null, $useServiceRole);
    }

    /**
     * PostgREST: Insert record(s) (POST)
     */
    public function insert(string $table, array $data, bool $useServiceRole = false): array
    {
        $endpoint = $this->url . '/rest/v1/' . $table;
        return $this->request('POST', $endpoint, $data, $useServiceRole, ['Prefer' => 'return=representation']);
    }

    /**
     * PostgREST: Update record(s) (PATCH)
     * e.g. update('profiles', ['id' => 'eq.xxx'], ['full_name' => 'New Name'])
     */
    public function update(string $table, array $filters, array $data, bool $useServiceRole = false): array
    {
        $queryString = http_build_query($filters);
        $endpoint = $this->url . '/rest/v1/' . $table . '?' . $queryString;

        return $this->request('PATCH', $endpoint, $data, $useServiceRole, ['Prefer' => 'return=representation']);
    }

    /**
     * PostgREST: Delete record(s) (DELETE)
     */
    public function delete(string $table, array $filters, bool $useServiceRole = false): array
    {
        $queryString = http_build_query($filters);
        $endpoint = $this->url . '/rest/v1/' . $table . '?' . $queryString;

        return $this->request('DELETE', $endpoint, null, $useServiceRole);
    }

    /**
     * Helper: Count rows matching filters
     */
    public function count(string $table, array $filters = [], bool $useServiceRole = false): int
    {
        $filters['select'] = 'count';
        $queryString = http_build_query($filters);
        $endpoint = $this->url . '/rest/v1/' . $table . '?' . $queryString;

        $response = $this->requestRaw('HEAD', $endpoint, null, $useServiceRole, ['Prefer' => 'count=exact']);
        $contentRange = $response['headers']['Content-Range'] ?? ($response['headers']['content-range'] ?? null);

        if ($contentRange) {
            // e.g. "0-24/42" or "* / 42"
            $parts = explode('/', is_array($contentRange) ? $contentRange[0] : $contentRange);
            return isset($parts[1]) ? (int) $parts[1] : 0;
        }

        // Fallback: regular GET count
        $rows = $this->query($table, array_merge($filters, ['select' => 'id']), $useServiceRole);
        return is_array($rows) ? count($rows) : 0;
    }

    /**
     * Send HTTP request using curl/CURLRequest
     */
    protected function request(string $method, string $url, ?array $body = null, bool $useServiceRole = false, array $extraHeaders = []): array
    {
        $res = $this->requestRaw($method, $url, $body, $useServiceRole, $extraHeaders);
        $data = json_decode($res['body'], true);

        if ($res['status'] >= 400) {
            $msg = $data['msg'] ?? ($data['message'] ?? ($data['error_description'] ?? ($data['error'] ?? 'Supabase API Error (' . $res['status'] . ')')));
            log_message('error', "Supabase Error [{$res['status']}] {$url}: " . json_encode($data));
            return ['error' => true, 'status' => $res['status'], 'message' => $msg, 'raw' => $data];
        }

        return is_array($data) ? $data : ['data' => $data, 'status' => $res['status']];
    }

    protected function requestRaw(string $method, string $url, ?array $body = null, bool $useServiceRole = false, array $extraHeaders = []): array
    {
        $client = \Config\Services::curlrequest([
            'http_errors' => false,
            'timeout' => 15,
        ]);

        $token = $useServiceRole ? $this->serviceKey : ($this->userToken ?? $this->anonKey);

        $headers = array_merge([
            'apikey' => $this->anonKey,
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ], $extraHeaders);

        $options = ['headers' => $headers];
        if ($body !== null) {
            $options['json'] = $body;
        }

        try {
            $response = $client->request($method, $url, $options);
            return [
                'status' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => $response->getBody(),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 500,
                'headers' => [],
                'body' => json_encode(['error' => $e->getMessage()]),
            ];
        }
    }
}
