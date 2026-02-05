<!DOCTYPE html>
<html>
<head>
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px; color: #666; }
        
        .section-title { 
            font-weight: bold; 
            text-transform: uppercase; 
            border-bottom: 2px solid #333; 
            margin-top: 20px; 
            margin-bottom: 10px; 
            padding-bottom: 5px;
            font-size: 11px;
        }

        .row { width: 100%; margin-bottom: 5px; }
        .row td { padding: 5px 0; }
        .label { text-align: left; }
        .value { text-align: right; font-family: 'Courier New', monospace; }
        
        .subtotal { font-weight: bold; border-top: 1px solid #999; }
        .total-box { 
            margin-top: 30px; 
            border-top: 3px double #000; 
            border-bottom: 3px double #000; 
            padding: 10px 0; 
            font-size: 16px; 
            font-weight: bold;
        }
        
        .red { color: #c0392b; }
        .green { color: #27ae60; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Laba Rugi</h1>
        <p>PT. WMS GUDANG MAKMUR</p>
        <p>Periode: {{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}</p>
    </div>

    {{-- REVENUE --}}
    <div class="section-title">Pendapatan</div>
    <table class="row" width="100%">
        <tr>
            <td class="label">Penjualan Bersih</td>
            <td class="value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- HPP --}}
    <div class="section-title">Harga Pokok Penjualan</div>
    <table class="row" width="100%">
        <tr>
            <td class="label">HPP (Cost of Goods Sold)</td>
            <td class="value red">(Rp {{ number_format($totalCOGS, 0, ',', '.') }})</td>
        </tr>
        <tr class="subtotal">
            <td class="label">LABA KOTOR (GROSS PROFIT)</td>
            <td class="value">Rp {{ number_format($grossProfit, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- EXPENSES --}}
    <div class="section-title">Biaya Operasional</div>
    <table class="row" width="100%">
        @foreach($expenses as $exp)
        <tr>
            <td class="label">{{ $exp->category->name }}</td>
            <td class="value">Rp {{ number_format($exp->total, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr class="subtotal">
            <td class="label">Total Biaya</td>
            <td class="value red">(Rp {{ number_format($totalExpenses, 0, ',', '.') }})</td>
        </tr>
    </table>

    {{-- NET PROFIT --}}
    <table class="row" width="100%">
        <tr class="total-box">
            <td class="label">LABA BERSIH (NET PROFIT)</td>
            <td class="value {{ $netProfit >= 0 ? 'green' : 'red' }}">
                Rp {{ number_format($netProfit, 0, ',', '.') }}
            </td>
        </tr>
    </table>

</body>
</html>