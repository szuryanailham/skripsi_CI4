<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ModelOtentikasi;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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

        return $this->respond([
            'message' => 'Login berhasil',
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

        if (!$this->validate($rules)) {
            return $this->fail($validation->getErrors(), 400);
        }

        $model = new ModelOtentikasi();
        $data = [
            'name'     => $this->request->getVar('name'),
            'email'    => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
        ];
        $model->insert($data);

        return $this->respondCreated(['message' => 'Registrasi berhasil']);
    }
}
