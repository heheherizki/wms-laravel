<!DOCTYPE html>
<html>
<head>
    <title>Laporan Arus Kas</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        
        .section-header { 
            background-color: #eee; 
            padding: 5px; 
            font-weight: bold; 
            margin-top: 15px; 
            border-bottom: 1px solid #ccc;
        }
        .row { width: 100%; margin-bottom: 5px; margin-top: 5px; }
        .label { padding-left: 15px; }
        .value { text-align: right; font-family: monospace; }
        .total-row { font-weight: bold; border-top: 1px solid #000; padding-top: 5px; }
        
        .grand-total {
            margin-top: 20px;
            border: 2px solid #000;
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Arus Kas</h1>
        <p>Periode: {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}</p>
    </div>

    {{-- INFLOW --}}
    <div class="section-header">ARUS KAS MASUK</div>
    <table class="row">
        <tr>
            <td class="label">Penerimaan Pelanggan</td>
            <td class="value">Rp {{ number_format($cashInSales, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Pemasukan Lain-lain</td>
            <td class="value">Rp {{ number_format($cashInOthers, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td class="label">Total Masuk</td>
            <td class="value">Rp {{ number_format($totalIn, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- OUTFLOW --}}
    <div class="section-header">ARUS KAS KELUAR</div>
    <table class="row">
        <tr>
            <td class="label">Pembayaran Supplier</td>
            <td class="value">(Rp {{ number_format($cashOutPurchase, 0, ',', '.') }})</td>
        </tr>
        <tr>
            <td class="label">Biaya Operasional</td>
            <td class="value">(Rp {{ number_format($cashOutExpenses, 0, ',', '.') }})</td>
        </tr>
        <tr class="total-row">
            <td class="label">Total Keluar</td>
            <td class="value">(Rp {{ number_format($totalOut, 0, ',', '.') }})</td>
        </tr>
    </table>

    {{-- NET --}}
    <table class="row grand-total">
        <tr>
            <td>KENAIKAN / (PENURUNAN) BERSIH KAS</td>
            <td class="value">Rp {{ number_format($netCashFlow, 0, ',', '.') }}</td>
        </tr>
    </table>

</body>
</html>