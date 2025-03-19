<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Services;

class FilterJwt implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('JWT_SECRET'); // Pastikan ada di `.env`
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return Services::response()
                ->setJSON(['status' => 401, 'message' => 'Unauthorized'])
                ->setStatusCode(401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $request->user = $decoded; // Simpan user di request untuk digunakan di Controller
        } catch (\Exception $e) {
            return Services::response()
                ->setJSON(['status' => 401, 'message' => 'Token invalid atau kadaluarsa'])
                ->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu menangani response
    }
}
