<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OrderModel;
use App\Models\UserModel;
use App\Models\EventModel;
use Dompdf\Dompdf;
use Exception;

class OrderController extends ResourceController
{
    protected $modelName = OrderModel::class;
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
                'status'  => 200,
                'message' => 'Data order berhasil diambil',
                'data'    => $orders
            ]);
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
                'status'  => 200,
                'message' => 'Data order berhasil ditemukan',
                'data'    => $order
            ]);
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
                'user_id'      => 'required|integer',
                'event_id'     => 'required|integer',
                'total_amount' => 'required|decimal',
                'status'       => 'required|in_list[pending,paid,canceled]',
                'paid'         => 'required|integer'
            ])) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            // Insert data
            if (!$this->model->insert($data)) {
                return $this->fail($this->model->errors());
            }

            $data['id'] = $this->model->getInsertID(); // Menyertakan ID baru jika dibutuhkan

            return $this->respondCreated([
                'status'  => 201,
                'message' => 'Order berhasil dibuat',
                'data'    => $data
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
                'status'  => 200,
                'message' => 'Order berhasil diperbarui',
                'data'    => $data
            ]);
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
                'status'  => 200,
                'message' => 'Order berhasil dihapus'
            ]);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    // PUT: Verifikasi order sebagai "paid"
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

            $data = $this->request->getRawInput();
            if (empty($data)) {
                return $this->failValidationErrors('Data tidak boleh kosong');
            }

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

    // GET: Ambil data invoice lengkap berdasarkan order ID
    public function getInvoiceData($id = null)
    {
        try {
            $orderModel = new OrderModel();
            $userModel  = new UserModel();
            $eventModel = new EventModel();

            $order = $orderModel->find($id);
            if (!$order) {
                return $this->failNotFound('Order tidak ditemukan');
            }

            $user = $userModel->find($order['user_id']);
            if (!$user) {
                return $this->failNotFound('Pengguna tidak ditemukan');
            }

            $event = $eventModel->find($order['event_id']);
            if (!$event) {
                return $this->failNotFound('Event tidak ditemukan');
            }

            return $this->respond([
                'status'  => true,
                'message' => 'Data invoice berhasil diambil',
                'data'    => [
                    'order' => $order,
                    'user'  => $user,
                    'event' => $event
                ]
            ]);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    public function downloadInvoice($id)
{
    

    $orderModel = new OrderModel();
    $userModel  = new UserModel();
    $eventModel = new EventModel();

    $order = $orderModel->find($id);
    if (!$order) return $this->failNotFound('Order tidak ditemukan');

    $user = $userModel->find($order['user_id']);
    $event = $eventModel->find($order['event_id']);

    // Siapkan HTML dari view
    $html = view('pdf/order_ticket', [
        'order' => $order,
        'user'  => $user,
        'event' => $event
    ]);

    // Generate PDF
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Download file
    return $this->response->setHeader('Content-Type', 'application/pdf')
                          ->setHeader('Content-Disposition', 'attachment;filename="invoice-'.$order['order_number'].'.pdf"')
                          ->setBody($dompdf->output());
}


 public function uploadProof($id)
    {
        helper(['form', 'filesystem']);

        // Validasi
        $validationRule = [
            'proof_image' => [
                'label' => 'Bukti Pembayaran',
                'rules' => 'uploaded[proof_image]|is_image[proof_image]|max_size[proof_image,2048]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $orderModel = new OrderModel();
        $order = $orderModel->find((int)$id);

        if (!$order) {
            return $this->failNotFound('Order tidak ditemukan.');
        }

        $file = $this->request->getFile('proof_image');

        // Simpan file ke public/uploads/proofs
        $newName = $file->getRandomName();
        $file->move('uploads/proofs', $newName);

        // Update data order
        $orderModel->update($id, [
            'proof_image' => 'uploads/proofs/' . $newName
        ]);

        return $this->respond([
            'message' => 'Bukti pembayaran berhasil diunggah.',
            'proof_image' => base_url('uploads/proofs/' . $newName),
        ]);
    }
}
