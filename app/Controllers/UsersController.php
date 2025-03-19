<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OrderModel;
use App\Models\UserModel;
use App\Models\EventModel;
use Exception;

class UsersController extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    // GET: Ambil semua order
    public function index()
    {
        try {
            $users = $this->model->findAll();

            if (empty($users)) {
                return $this->failNotFound('Tidak ada order yang ditemukan');
            }

            return $this->respond([
                'status' => 200,
                'message' => 'Data users berhasil diambil',
                'data' => $users
            ], 200);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }
    
}
