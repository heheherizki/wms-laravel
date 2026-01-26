<!DOCTYPE html>
<html>
<head>
    <title>RMA #{{ $return->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; vertical-align: top; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .items-table th { background-color: #f2f2f2; }
        .footer { margin-top: 40px; text-align: right; }
        .signature-box { display: inline-block; width: 150px; text-align: center; margin-top: 40px; }
        .line { border-top: 1px solid #000; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">FORMULIR RETUR BARANG (RMA)</div>
        <div>{{ config('app.name', 'WMS Gudang') }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>No. RMA</strong></td>
            <td width="35%">: #{{ $return->id }}</td>
            <td width="15%"><strong>Tanggal</strong></td>
            <td width="35%">: {{ date('d F Y', strtotime($return->date)) }}</td>
        </tr>
        <tr>
            <td><strong>Ref. SO</strong></td>
            <td>: {{ $return->salesOrder->so_number }}</td>
            <td><strong>Customer</strong></td>
            <td>: {{ $return->salesOrder->customer->name }}</td>
        </tr>
        <tr>
            <td><strong>Status</strong></td>
            <td>: {{ strtoupper($return->status) }}</td>
            <td><strong>Admin</strong></td>
            <td>: {{ $return->user->name }}</td>
        </tr>
    </table>

    <div style="margin-bottom: 10px; font-style: italic; border: 1px dashed #ccc; padding: 5px;">
        <strong>Alasan Retur:</strong> {{ $return->reason }}
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Kode Barang (SKU)</th>
                <th>Nama Produk</th>
                <th width="15%" style="text-align:center;">Qty Retur</th>
                <th width="10%">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($return->details as $index => $item)
            <tr>
                <td style="text-align:center;">{{ $index + 1 }}</td>
                <td>{{ $item->product->sku }}</td>
                <td>{{ $item->product->name }}</td>
                <td style="text-align:center; font-weight:bold;">{{ $item->quantity }}</td>
                <td>{{ $item->product->unit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <div>Disetujui Oleh,</div>
            <div class="line">Kepala Gudang</div>
        </div>
        <div class="signature-box" style="margin-left: 20px;">
            <div>Diserahkan Oleh,</div>
            <div class="line">Customer / Supir</div>
        </div>
    </div>
</body>
</html>