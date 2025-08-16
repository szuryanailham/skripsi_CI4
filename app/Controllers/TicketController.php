<?php

namespace App\Controllers;

use App\Models\OrderModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TicketController extends BaseController
{

    public function download($ticketId)
{
    $orderModel = new OrderModel();

    // --- Ambil token dari header Authorization ---
    $authHeader = $this->request->getHeaderLine('Authorization');
    if (!$authHeader) {
        return $this->response->setJSON([
            'status' => 401,
            'message' => 'Token tidak ditemukan'
        ])->setStatusCode(401);
    }

    $token = str_replace('Bearer ', '', $authHeader);
    $secretKey = getenv('JWT_SECRET') ?: 'my_super_secret_key';

    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        $userId = $decoded->sub ?? null;
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 401,
                'message' => 'User ID tidak valid dalam token'
            ])->setStatusCode(401);
        }
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 401,
            'message' => 'Token tidak valid: ' . $e->getMessage()
        ])->setStatusCode(401);
    }

    // --- Ambil data order ---
    $orderData = $orderModel->getOrderWithRelations($ticketId);

    if (!$orderData) {
        return $this->response->setJSON([
            'status' => 404,
            'message' => 'Order tidak ditemukan'
        ])->setStatusCode(404);
    }

    // --- Cek apakah order milik user yang sedang login ---
    if ($orderData['user_id'] != $userId) {
        return $this->response->setJSON([
            'status' => 403,
            'message' => 'Anda tidak berhak mengunduh tiket ini'
        ])->setStatusCode(403);
    }

    // --- Cek status order harus Paid ---
    if (strtolower($orderData['status']) !== 'paid') {
        return $this->response->setJSON([
            'status' => 403,
            'message' => 'Tiket hanya bisa diunduh jika status order sudah Paid'
        ])->setStatusCode(403);
    }

    // Data order
    $order = [
        'order_number'  => $orderData['order_number'],
        'status'        => $orderData['status'],
        'total_amount'  => $orderData['total_amount']
    ];

    // Data user
    $user = [
        'name'  => $orderData['user_name'],
        'email' => $orderData['user_email']
    ];

    // Data event
    $event = [
        'title' => $orderData['event_title'],
        'date'  => $orderData['event_date']
    ];

    // Render view ke HTML
    $html = view('pdf/ticket_pdf', [
        'order' => $order,
        'user'  => $user,
        'event' => $event
    ]);

    // Setup DomPDF
    $options = new Options();
    $options->set('defaultFont', 'Helvetica');
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Outputkan PDF ke browser untuk di-download
    return $this->response
        ->setContentType('application/pdf')
        ->setHeader('Content-Disposition', 'attachment; filename="invoice_' . $ticketId . '.pdf"')
        ->setBody($dompdf->output());
}
}
