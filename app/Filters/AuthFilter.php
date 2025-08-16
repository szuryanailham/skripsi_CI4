<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
     $authHeader = $request->getHeaderLine('Authorization');

        // Cek header Authorization ada atau tidak
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return service('response')->setJSON([
                'message' => 'Unauthorized - Token not provided'
            ])->setStatusCode(401);
        }

        $token = $matches[1]; // Ambil token dari Bearer

        try {
            // Verifikasi token (pastikan secret sama dengan yang dipakai saat generate token)
            $secretKey = getenv('JWT_SECRET') ?: 'my_super_secret_key';
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            // Simpan data user ke request agar bisa dipakai di Controller
            $request->userData = $decoded;
            session()->set('authUser', $decoded);
        } catch (\Exception $e) {
            return service('response')->setJSON([
                'message' => 'Unauthorized - Invalid token',
                'error'   => $e->getMessage()
            ])->setStatusCode(401);
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
