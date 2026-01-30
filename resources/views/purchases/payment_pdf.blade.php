<!DOCTYPE html>
<html>
<head>
    <title>Payment Voucher</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; color: #666; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px; vertical-align: top; }
        .label { font-weight: bold; width: 120px; }
        
        .box-amount { 
            background-color: #f3f4f6; 
            padding: 10px; 
            border: 1px solid #ddd; 
            font-size: 16px; 
            font-weight: bold; 
            text-align: right; 
            margin-bottom: 5px;
        }
        .terbilang { 
            font-style: italic; 
            font-size: 11px; 
            background: #eee; 
            padding: 5px; 
            border-left: 4px solid #666; 
        }

        .signatures { width: 100%; margin-top: 40px; text-align: center; }
        .signatures td { width: 33%; }
        .sign-box { height: 60px; }
        .sign-name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Bukti Pengeluaran Kas</h1>
        {{-- Ganti dengan Nama PT Anda --}}
        <p>PT. WMS GUDANG MAKMUR</p> 
        <p>Jl. Gudang No. 123, Jakarta Selatan</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">No. Referensi</td>
            <td>: #PAY-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td class="label">Tanggal</td>
            <td>: {{ date('d F Y', strtotime($payment->date)) }}</td>
        </tr>
        <tr>
            <td class="label">Dibayarkan Kepada</td>
            <td>: <strong>{{ $payment->purchase->supplier->name }}</strong></td>
            <td class="label">Metode Bayar</td>
            <td>: {{ $payment->payment_method }}</td>
        </tr>
        <tr>
            <td class="label">Keterangan</td>
            <td colspan="3">: Pembayaran PO No. {{ $payment->purchase->po_number }} <br> 
               <span style="font-size:10px; color:#666;">({{ $payment->notes }})</span>
            </td>
        </tr>
    </table>

    <div class="box-amount">
        Rp {{ number_format($payment->amount, 0, ',', '.') }}
    </div>
    <div class="terbilang">
        Terbilang: "{{ trim($terbilang) }}"
    </div>

    <table class="signatures">
        <tr>
            <td>
                Dibuat Oleh,
                <div class="sign-box"></div>
                <div class="sign-name">{{ $payment->user->name }}</div>
                <div style="font-size:10px">Admin Keuangan</div>
            </td>
            <td>
                Disetujui Oleh,
                <div class="sign-box"></div>
                <div class="sign-name">( ........................... )</div>
                <div style="font-size:10px">Manager Keuangan</div>
            </td>
            <td>
                Diterima Oleh,
                <div class="sign-box"></div>
                <div class="sign-name">{{ $payment->purchase->supplier->name }}</div>
                <div style="font-size:10px">Supplier / Vendor</div>
            </td>
        </tr>
    </table>

</body>
</html>