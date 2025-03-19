<?php

use App\Models\ModelOtentikasi;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getJWT($otentikasiHeader)
{
    if (is_null($otentikasiHeader) || !str_contains($otentikasiHeader, 'Bearer ')) {
        throw new Exception("Otentikasi JWT Gagal: Token tidak ditemukan");
    }

    $tokenParts = explode(" ", $otentikasiHeader);
    if (count($tokenParts) !== 2) {
        throw new Exception("Otentikasi JWT Gagal: Format token tidak valid");
    }

    return $tokenParts[1]; // Mengembalikan token JWT
}

function validateJWT($encodedToken)
{
    $key = getenv('JWT_SECRET_KEY');

    try {
        $decodedToken = JWT::decode($encodedToken, new Key($key, 'HS256'));

        $modelOtentikasi = new ModelOtentikasi();
        $user = $modelOtentikasi->getEmail($decodedToken->email);

        if (!$user) {
            throw new Exception("Token tidak valid atau user tidak ditemukan");
        }

        return $decodedToken; // Mengembalikan hasil decode untuk digunakan
    } catch (Exception $e) {
        throw new Exception("Token tidak valid: " . $e->getMessage());
    }
}

function createJWT($email)
{
    $waktuRequest = time();
    $waktuToken = getenv('JWT_TIME_TO_LIVE'); // Pastikan nilai ini diatur di .env
    $waktuExpired = $waktuRequest + (int) $waktuToken;

    $payload = [
        'email' => $email,
        'iat' => $waktuRequest, // Waktu saat token dibuat
        'exp' => $waktuExpired   // Waktu expired token
    ];

    $jwt = JWT::encode($payload, getenv('JWT_SECRET_KEY'), 'HS256');

    return $jwt;
}
