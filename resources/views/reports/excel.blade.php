<table>
    <thead>
        <tr>
            <th colspan="6" style="font-weight: bold; font-size: 14px;">Laporan Transaksi Gudang</th>
        </tr>
        <tr>
            <td colspan="6">Periode: {{ $startDate }} s/d {{ $endDate }}</td>
        </tr>
        <tr></tr> <tr style="background-color: #eeeeee; font-weight: bold;">
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Produk</th>
            <th>Tipe</th>
            <th>Jumlah</th>
            <th>User</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $log)
        <tr>
            <td>{{ $log->created_at->format('d/m/Y') }}</td>
            <td>{{ $log->created_at->format('H:i') }}</td>
            <td>{{ $log->product->name }}</td>
            <td>{{ $log->type == 'in' ? 'Masuk' : 'Keluar' }}</td>
            <td>{{ $log->quantity }}</td>
            <td>{{ $log->user ? $log->user->name : 'System' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>