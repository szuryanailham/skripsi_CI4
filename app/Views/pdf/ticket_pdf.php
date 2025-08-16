<!DOCTYPE html>
<html>
<head>
    <title>Invoice Order</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .invoice-box {
            border: 1px solid #eee;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <h2>Invoice Tiket</h2>
        <p><strong>Nomor Order:</strong> <?= esc($order['order_number']) ?></p>
        <p><strong>Status:</strong> <?= esc($order['status']) ?></p>
        <p><strong>Total:</strong> Rp <?= number_format($order['total_amount'], 2, ',', '.') ?></p>
        <hr>
        <h4>Data Pengguna</h4>
        <p><strong>Nama:</strong> <?= esc($user['name']) ?></p>
        <p><strong>Email:</strong> <?= esc($user['email']) ?></p>

        <hr>
        <h4>Detail Event</h4>
        <p><strong>Nama Event:</strong> <?= esc($event['title']) ?></p>
        <p><strong>Tanggal:</strong> <?= esc($event['date']) ?></p>
    </div>
</body>
</html>
