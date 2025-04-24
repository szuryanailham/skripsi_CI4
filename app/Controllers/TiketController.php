<?php

namespace App\Controllers;

use App\Models\OrderModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class TiketController extends BaseController
{
    // public function download($id)
    // {
    //     $orderModel = new OrderModel();
    //     $order = $orderModel->getOrderWithEvent($id);

    //     if (!$order) {
    //         return $this->response->setStatusCode(404)->setJSON([
    //             'success' => false,
    //             'message' => 'Data order tidak ditemukan.',
    //         ]);
    //     }

    //     // Render HTML view
    //     $html = view('pdf/tiket', ['order' => $order]);

    //     // Inisialisasi Dompdf
    //     $options = new Options();
    //     $options->set('isHtml5ParserEnabled', true);
    //     $dompdf = new Dompdf($options);

    //     $dompdf->loadHtml($html);
    //     $dompdf->setPaper('A4', 'portrait');
    //     $dompdf->render();

    //     // Simpan ke file
    //     $fileName = 'tiket-' . $order['id'] . '.pdf';
    //     $filePath = WRITEPATH . 'uploads/tiket/' . $fileName;

    //     file_put_contents($filePath, $dompdf->output());

    //     return $this->response->setJSON([
    //         'success' => true,
    //         'message' => 'Tiket berhasil dibuat.',
    //         'download_url' => base_url('writable/uploads/tiket/' . $fileName),
    //     ]);
    // }

    public function download($ticketId)
    {
    
        $ticket = [
            'id' => $ticketId,
            'name' => 'Ilham Suryana',
            'event' => 'Konser Musik 2025',
            'date' => '2025-06-30',
            'seat' => 'A12',
        ];

        // Render view ke HTML
        $html = view('ticket_pdf', ['ticket' => $ticket]);

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
            ->setHeader('Content-Disposition', 'attachment; filename="tiket_' . $ticketId . '.pdf"')
            ->setBody($dompdf->output());
    }
}
