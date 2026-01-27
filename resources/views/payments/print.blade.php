<!DOCTYPE html>
<html>
<head>
    <title>Kwitansi {{ $payment->payment_number }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .box { border: 2px solid #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 1px dashed #999; padding-bottom: 10px; }
        .title { font-size: 24px; font-weight: bold; text-transform: uppercase; }
        .row { display: flex; margin-bottom: 10px; }
        .label { width: 150px; font-weight: bold; }
        .value { flex: 1; border-bottom: 1px dotted #ccc; }
        .amount-box { margin-top: 20px; padding: 10px; background: #eee; font-size: 20px; font-weight: bold; text-align: right; border: 1px solid #999; }
        .footer { margin-top: 40px; text-align: right; }
    </style>
</head>
<body>
    <div class="box">
        <div class="header">
            <div class="title">BUKTI PEMBAYARAN (KWITANSI)</div>
            <div>{{ config('app.name', 'WMS Gudang') }}</div>
        </div>

        <table width="100%" cellpadding="5">
            <tr>
                <td width="150"><strong>No. Kwitansi</strong></td>
                <td>: {{ $payment->payment_number }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal</strong></td>
                <td>: {{ date('d F Y', strtotime($payment->date)) }}</td>
            </tr>
            <tr>
                <td><strong>Telah Terima Dari</strong></td>
                <td>: {{ $payment->invoice->salesOrder->customer->name }}</td>
            </tr>
            <tr>
                <td><strong>Untuk Pembayaran</strong></td>
                <td>: Invoice No. {{ $payment->invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td><strong>Metode Bayar</strong></td>
                <td>: {{ $payment->payment_method }} {{ $payment->note ? "($payment->note)" : '' }}</td>
            </tr>
        </table>

        <div class="amount-box">
            Rp {{ number_format($payment->amount, 0, ',', '.') }}
        </div>

        <div class="footer">
            <p>Diterima Oleh,</p>
            <br><br><br>
            <p>( {{ $payment->user->name }} )</p>
        </div>
    </div>
</body>
</html>