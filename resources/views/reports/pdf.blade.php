<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .badge-in { color: green; font-weight: bold; }
        .badge-out { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TRANSAKSI GUDANG</h1>
        <p>Periode: {{ date('d F Y', strtotime($startDate)) }} - {{ date('d F Y', strtotime($endDate)) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>SKU</th>
                <th>Tipe</th>
                <th>Jml</th>
                <th>User</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $log)
            <tr>
                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $log->product->name }}</td>
                <td>{{ $log->product->sku }}</td>
                <td class="{{ $log->type == 'in' ? 'badge-in' : 'badge-out' }}">
                    {{ $log->type == 'in' ? 'Masuk' : 'Keluar' }}
                </td>
                <td>{{ $log->quantity }}</td>
                <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                <td>{{ $log->reference }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>