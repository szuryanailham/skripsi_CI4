<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        // Cek Bearer Token
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return service('response')
                ->setJSON(['message' => 'Unauthorized - Token not provided'])
                ->setStatusCode(401);
        }

        $token = $matches[1];

        try {
            $secretKey = getenv('JWT_SECRET') ?: 'my_super_secret_key';
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            // Pastikan ada role dan harus admin
            if (!isset($decoded->status) || strtolower($decoded->status) !== 'admin') {
                return service('response')
                    ->setJSON(['message' => 'Forbidden - Admin access only'])
                    ->setStatusCode(403);
            }

            service('request')->decodedUser = $decoded;

        } catch (\Exception $e) {
            return service('response')
                ->setJSON(['message' => 'Unauthorized - Invalid token', 'error' => $e->getMessage()])
                ->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak dipakai
    }
}
