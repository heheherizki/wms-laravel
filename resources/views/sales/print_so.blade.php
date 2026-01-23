<!DOCTYPE html>
<html>
<head>
    <title>Picking List {{ $order->so_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">PICKING LIST (PERSIAPAN BARANG)</div>
        <div>No. SO: {{ $order->so_number }} | Tgl: {{ date('d/m/Y', strtotime($order->date)) }}</div>
        <div>Customer: {{ $order->customer->name }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>SKU</th>
                <th>Lokasi Rak</th>
                <th>Qty Order</th>
                <th>Cek Fisik (V)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->details as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->product->sku }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $item->product->rack_location ?? '-' }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td></td> </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 50px;">
        <div style="float: right; width: 150px; text-align: center;">
            <p>Petugas Gudang,</p>
            <br><br><br>
            <p>(....................)</p>
        </div>
    </div>
</body>
</html>