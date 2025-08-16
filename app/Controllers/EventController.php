<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\EventModel;
use Exception;
class EventController extends ResourceController
{
    protected $modelName = 'App\Models\EventModel';
    protected $format    = 'json';

    // GET: Ambil semua event
    public function index()
    {
        try {
            $events = $this->model->findAll();
            if (empty($events)) {
                return $this->failNotFound('Tidak ada event yang ditemukan');
            }
            return $this->respond(['status' => true, 'data' => $events], 200);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // GET: Ambil event berdasarkan ID
    public function show($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationErrors('ID tidak boleh kosong');
            }

            $event = $this->model->find($id);
            if (!$event) {
                return $this->failNotFound('Event tidak ditemukan');
            }

            return $this->respond(['status' => true, 'data' => $event], 200);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // POST: Tambah event baru
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);

            if (!$this->validate([
                'title'        => 'required|min_length[3]|max_length[100]',
                'slug'        => 'required|alpha_dash|min_length[3]|max_length[100]|is_unique[events.slug]',
                'date'        => 'required|valid_date',
                'location'    => 'required|min_length[3]|max_length[255]',
                'description' => 'required|min_length[10]',
                'price'       => 'required|numeric',
            ])) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            if (!$this->model->insert($data)) {
                return $this->fail($this->model->errors());
            }

            return $this->respondCreated([
                'status'  => true,
                'message' => 'Event berhasil dibuat',
                'data'    => $data
            ]);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // PUT: Update event berdasarkan ID
    public function update($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationErrors('ID tidak boleh kosong');
            }

            $data = $this->request->getJSON(true);
            if (!$this->model->find($id)) {
                return $this->failNotFound('Event tidak ditemukan');
            }

            if (!$this->model->update($id, $data)) {
                return $this->fail($this->model->errors());
            }

            return $this->respond([
                'status'  => true,
                'message' => 'Event berhasil diperbarui',
                'data'    => $data
            ]);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // DELETE: Hapus event berdasarkan ID
    public function delete($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationErrors('ID tidak boleh kosong');
            }

            if (!$this->model->find($id)) {
                return $this->failNotFound('Event tidak ditemukan');
            }

            if (!$this->model->delete($id)) {
                return $this->fail('Gagal menghapus event');
            }

            return $this->respondDeleted([
                'status'  => true,
                'message' => 'Event berhasil dihapus'
            ]);
        } catch (Exception $e) {
            return $this->failServerError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id = null)
    {
        $eventModel = new EventModel();
        $event = $eventModel->find($id);

        if (!$event) {
            return redirect()->to('/events')->with('error', 'Event tidak ditemukan.');
        }

        return view('events/edit', ['event' => $event]);
    }
}
