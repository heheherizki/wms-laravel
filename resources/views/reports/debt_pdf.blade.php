<!DOCTYPE html>
<html>
<head>
    <title>Laporan Hutang Supplier</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .right { text-align: right; }
        .center { text-align: center; }
        .total-row { background-color: #e6e6e6; font-weight: bold; }
        .danger { color: #d32f2f; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px; color: #888; text-align: center; border-top: 1px solid #ddd; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Hutang Dagang (Accounts Payable)</h1>
        <p>PT. WMS GUDANG MAKMUR</p>
        <p>Dicetak Per Tanggal: {{ date('d F Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 35%">Nama Supplier</th>
                <th style="width: 15%">Kontak</th>
                <th style="width: 10%">Jml PO</th>
                <th style="width: 10%">Termin</th>
                <th style="width: 25%">Sisa Hutang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $index => $supplier)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>
                    <b>{{ $supplier->name }}</b>
                </td>
                <td>{{ $supplier->phone ?? '-' }}</td>
                <td class="center">{{ $supplier->purchases->count() }}</td>
                <td class="center">{{ $supplier->term_days }} Hari</td>
                <td class="right danger">
                    Rp {{ number_format($supplier->total_debt, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="right">TOTAL KEWAJIBAN PERUSAHAAN</td>
                <td class="right danger">Rp {{ number_format($grandTotalDebt, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="font-size: 10px; color: #555; margin-top: 10px;">
        <strong>Catatan:</strong>
        <br>
        - Laporan ini mencakup semua tagihan dari supplier yang statusnya Belum Lunas (Unpaid) atau Parsial.
        <br>
        - Harap memprioritaskan pembayaran untuk supplier dengan termin pendek.
    </div>

    <div class="footer">
        Dicetak oleh System WMS | Halaman <span class="page-number"></span>
    </div>

</body>
</html>