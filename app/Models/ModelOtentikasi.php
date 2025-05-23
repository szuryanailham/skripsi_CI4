<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class ModelOtentikasi extends Model
{
    protected $primayKey = "id";

    protected $table = 'users';
    protected $allowedFields = ['name', 'email', 'password'];

    function getEmail($email)
    {
        $builder = $this->table("otentikasi");
        $data = $builder->where("email", $email)->first();
        if (!$data) {
            throw new Exception("Data otentikasi tidak ditemukan");
        }
        return $data;
    }
}