<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OrderModel;
use App\Models\UserModel;
use App\Models\EventModel;
use Exception;

class OrderController extends ResourceController
{
    protected $modelName = 'App\Models\OrderModel';
    protected $format    = 'json';

    // GET: Ambil semua order
    public function index()
    {
        try {
            $orders = $this->model->findAll();

            if (empty($orders)) {
                return $this->failNotFound('Tidak ada order yang ditemukan');
            }

            return $this->respond([
                'status' => 200,
                'message' => 'Data order berhasil diambil',
                'data' => $orders
            ], 200);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    // GET: Ambil order berdasarkan ID
    public function show($id = null)
    {
        try {
            $order = $this->model->find($id);

            if (!$order) {
                return $this->failNotFound('Order tidak ditemukan');
            }

            return $this->respond([
                'status' => 200,
                'message' => 'Data order berhasil ditemukan',
                'data' => $order
            ], 200);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    // POST: Tambah order baru
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);

            // Validasi input
            if (!$this->validate([
                'order_number' => 'required|is_unique[orders.order_number]',
                'user_id' => 'required|integer',
                'event_id' => 'required|integer',
                'total_amount' => 'required|decimal',
                'status' => 'required|in_list[pending,paid,canceled]',
                'paid' => 'required|integer'
            ])) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            if (!$this->model->insert($data)) {
                return $this->fail($this->model->errors());
            }

            return $this->respondCreated([
                'status' => 201,
                'message' => 'Order berhasil dibuat',
                'data' => $data
            ]);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    // PUT: Update order berdasarkan ID
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);

            if (!$this->model->find($id)) {
                return $this->failNotFound('Order tidak ditemukan');
            }

            if (!$this->model->update($id, $data)) {
                return $this->fail($this->model->errors());
            }

            return $this->respond([
                'status' => 200,
                'message' => 'Order berhasil diperbarui',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    // DELETE: Hapus order berdasarkan ID
    public function delete($id = null)
    {
        try {
            if (!$this->model->find($id)) {
                return $this->failNotFound('Order tidak ditemukan');
            }

            if (!$this->model->delete($id)) {
                return $this->failServerError('Gagal menghapus order');
            }

            return $this->respondDeleted([
                'status' => 200,
                'message' => 'Order berhasil dihapus'
            ]);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    public function verifyOrder($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationErrors('ID tidak boleh kosong');
            }
    
            $order = $this->model->find($id);
            if (!$order) {
                return $this->failNotFound('Order tidak ditemukan');
            }
    
            if ($order['status'] === 'paid') {
                return $this->fail('Order sudah berstatus paid');
            }
    
            // Ambil data dari request (fix untuk PUT)
            $data = $this->request->getRawInput();
            if (empty($data)) {
                return $this->failValidationErrors('Data tidak boleh kosong');
            }
    
            // Update status order ke "paid"
            $updateData = ['status' => 'paid'];
            if (!$this->model->update($id, $updateData)) {
                return $this->fail('Gagal memperbarui order');
            }
    
            return $this->respond([
                'status'  => true,
                'message' => 'Order berhasil diverifikasi sebagai paid',
                'data'    => ['id' => $id, 'status' => 'paid']
            ]);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    public function getInvoiceData($id)
    {
        $orderModel = new OrderModel();
        $userModel  = new UserModel();
        $eventModel = new EventModel();

        // Ambil data order berdasarkan ID
        $order = $orderModel->find($id);
        if (!$order) {
            return $this->failNotFound('Order tidak ditemukan');
        }

        // Ambil data pengguna terkait
        $user = $userModel->find($order['user_id']);
        if (!$user) {
            return $this->failNotFound('Pengguna tidak ditemukan');
        }

        // Ambil data event terkait
        $event = $eventModel->find($order['event_id']);
        if (!$event) {
            return $this->failNotFound('Event tidak ditemukan');
        }

        // Kembalikan data dalam format JSON
        return $this->respond([
            'status' => true,
            'message' => 'Data invoice berhasil diambil',
            'data' => [
                'order' => $order,
                'user'  => $user,
                'event' => $event
            ]
        ]);
    }
    

}
