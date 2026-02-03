<!DOCTYPE html>
<html>
<head>
    <title>Statement of Account - {{ $selectedSupplier->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        
        .meta-table { width: 100%; margin-bottom: 20px; }
        .meta-table td { vertical-align: top; }
        
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data th, table.data td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        table.data th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .bg-gray { background-color: #eee; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Statement of Account</h1>
        <p>PT. WMS GUDANG MAKMUR</p>
    </div>

    <table class="meta-table">
        <tr>
            <td width="60%">
                <strong>Kepada Yth:</strong><br>
                {{ $selectedSupplier->name }}<br>
                {{ $selectedSupplier->address }}<br>
                {{ $selectedSupplier->phone }}
            </td>
            <td width="40%" class="right">
                <strong>Periode:</strong><br>
                {{ date('d M Y', strtotime($request->start_date)) }} s/d {{ date('d M Y', strtotime($request->end_date)) }}
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="15%">Tanggal</th>
                <th width="20%">Referensi</th>
                <th width="25%">Keterangan</th>
                <th width="13%">Tagihan</th>
                <th width="13%">Bayar/Retur</th>
                <th width="14%">Saldo</th>
            </tr>
        </thead>
        <tbody>
            {{-- SALDO AWAL --}}
            <tr class="bg-gray">
                <td colspan="5" class="right bold">SALDO AWAL (OPENING BALANCE)</td>
                <td class="right bold">{{ number_format($openingBalance, 0, ',', '.') }}</td>
            </tr>

            @php $runningBalance = $openingBalance; @endphp

            @foreach($statement as $row)
                @php 
                    $runningBalance += ($row['credit'] - $row['debit']); 
                @endphp
                <tr>
                    <td style="text-align:center">{{ date('d/m/Y', strtotime($row['date'])) }}</td>
                    <td>{{ $row['ref'] }}</td>
                    <td>{{ $row['type'] }}</td>
                    <td class="right">{{ $row['credit'] > 0 ? number_format($row['credit'], 0, ',', '.') : '-' }}</td>
                    <td class="right">{{ $row['debit'] > 0 ? number_format($row['debit'], 0, ',', '.') : '-' }}</td>
                    <td class="right bold">{{ number_format($runningBalance, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-gray">
                <td colspan="5" class="right bold">SALDO AKHIR (ENDING BALANCE)</td>
                <td class="right bold">{{ number_format($endingBalance, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="font-size: 10px; color: #555; margin-top: 20px;">
        * Dokumen ini dicetak otomatis oleh sistem pada {{ date('d-m-Y H:i') }}.
        <br>
        * Mohon konfirmasi jika terdapat perbedaan saldo.
    </div>

</body>
</html>