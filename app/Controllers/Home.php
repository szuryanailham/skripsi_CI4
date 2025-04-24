<?php

namespace App\Controllers;

use App\Models\EventModel;
use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function events(): ResponseInterface
    {
        $eventModel = new EventModel();
        $events = $eventModel->findAll(); // ambil semua data event

        return $this->response->setJSON($events);
    }
}
