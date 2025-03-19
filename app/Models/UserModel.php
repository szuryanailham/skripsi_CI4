<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users'; // Nama tabel di database
    protected $primaryKey = 'id';    // Primary key tabel

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // Gunakan soft delete (pastikan kolom deleted_at ada!)

    protected $allowedFields = ['name', 'email', 'password', 'phone_number', 'status'];

    // Enable timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // Pastikan kolom ini ada jika soft delete digunakan

    // Validation rules
    protected $validationRules = [
        'name'     => 'required|min_length[3]',
        'email'    => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[6]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email sudah terdaftar.'
        ]
    ];

    protected $skipValidation = false;

    // Callback untuk hash password sebelum insert & update
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * 🔹 Ambil user berdasarkan email
     */
    public function getEmail($email)
    {
        return $this->where("email", $email)->first();
    }

    /**
     * 🔹 Hash password sebelum menyimpan ke database
     */
    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        $password = $data['data']['password'];

        // Hanya hash jika password belum di-hash
        if (!password_get_info($password)['algo']) {
            $data['data']['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        return $data;
    }
}
