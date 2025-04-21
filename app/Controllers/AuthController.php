<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ModelOtentikasi;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use ResponseTrait;

class AuthController extends ResourceController
{

    
    public function login()
    {
        $validation = Services::validation();
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($validation->getErrors(), 400);
        }

        $model = new ModelOtentikasi();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $data = $model->where('email', $email)->first();

        if (!$data) {
            return $this->failNotFound("Email tidak terdaftar.");
        }
        if (!password_verify($password, $data['password'])) {
            return $this->fail("Password tidak sesuai.", 401);
        }

        $key = getenv('JWT_SECRET');
        $payload = [
            'iss' => 'server_api',
            'aud' => 'users',
            'sub' => $data['id'],
            'email' => $data['email'],
            'iat' => time(),
            'exp' => time() + 3600 // Expire dalam 1 jam
        ];
        $token = JWT::encode($payload, $key, 'HS256');

        $user = [
            'id' => $data['id'],
            'email' => $data['email'],
            'name' => $data['name'] ?? null, // jika ada kolom name
        ];

        return $this->respond([
            'message' => 'Login berhasil',
            'user' => $user,
            'access_token' => $token
        ]);
    }

    public function register()
    {
        $validation = Services::validation();
    
        $rules = [
            'name'     => 'required|min_length[3]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]'
        ];
    
        $messages = [
            'name' => [
                'required'    => 'Nama wajib diisi',
                'min_length'  => 'Nama minimal 3 karakter'
            ],
            'email' => [
                'required'    => 'Email wajib diisi',
                'valid_email' => 'Format email tidak valid',
                'is_unique'   => 'Email sudah digunakan'
            ],
            'password' => [
                'required'    => 'Password wajib diisi',
                'min_length'  => 'Password minimal 6 karakter'
            ]
        ];
    
        if (!$this->validate($rules, $messages)) {
            return $this->fail($validation->getErrors(), 400);
        }
    
        $model = new ModelOtentikasi();
        $data = [
            'name'     => $this->request->getVar('name'),
            'email'    => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
        ];
    
        $model->insert($data);
    
        // Ambil data user yang baru saja disimpan
        $user = $model->where('email', $data['email'])->first();
    
        $key = getenv('JWT_SECRET');
        $payload = [
            'iss'   => 'server_api',
            'aud'   => 'users',
            'sub'   => $user['id'],
            'email' => $user['email'],
            'iat'   => time(),
            'exp'   => time() + 3600 // Expire dalam 1 jam
        ];
        $token = JWT::encode($payload, $key, 'HS256');
    
        return $this->respond([
            'message'       => 'Registrasi berhasil',
            'access_token'  => $token,
            'user'          => [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email']
            ]
        ]);
    }
    


public function logout()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);

        if (!$token) {
            return $this->failUnauthorized('Token tidak ditemukan.');
        }

        // Simpan token ke tabel blacklist (opsional, kalau kamu pakai sistem itu)
        $blacklistModel = new \App\Models\BlacklistTokenModel();
        $blacklistModel->insert([
            'token' => $token,
            'expired_at' => date('Y-m-d H:i:s', time() + 3600), // contoh expired 1 jam
        ]);

        return $this->respond([
            'message' => 'Logout berhasil',
        ]);
    }


}
