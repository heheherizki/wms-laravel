<!DOCTYPE html>
<html>
<head>
    <title>Surat Jalan {{ $shipment->shipment_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        
        /* Header */
        .header { width: 100%; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .company-name { font-size: 20px; font-weight: bold; color: #1a202c; }
        .doc-title { float: right; font-size: 24px; font-weight: bold; color: #555; border: 2px solid #555; padding: 5px 15px; border-radius: 5px; }
        
        /* Info Tables */
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { vertical-align: top; }
        .box { background: #f8f9fa; padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-height: 100px; }
        .label { font-weight: bold; font-size: 10px; text-transform: uppercase; color: #777; margin-bottom: 3px; display: block; }
        
        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background: #2d3748; color: #fff; padding: 8px; text-align: left; font-size: 11px; text-transform: uppercase; }
        .items-table td { border-bottom: 1px solid #eee; padding: 10px 8px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        /* Signature Area */
        .footer { margin-top: 50px; width: 100%; page-break-inside: avoid; }
        .signature-box { width: 25%; float: left; text-align: center; margin-right: 5%; }
        .sign-line { border-bottom: 1px solid #333; margin-top: 60px; margin-bottom: 5px; }
        
        /* Footer Note */
        .note { margin-top: 20px; font-size: 10px; font-style: italic; color: #666; }
    </style>
</head>
<body>

    <div class="header">
        <span class="doc-title">SURAT JALAN</span>
        <div class="company-name">WMS GUDANG SAYA</div>
        <div>Jl. Teknologi No. 123, Jakarta Selatan</div>
        <div>Telp: (021) 555-9999</div>
    </div>

    <table class="info-table">
        <tr>
            <td width="55%" style="padding-right: 15px;">
                <div class="box">
                    <span class="label">Penerima / Tujuan:</span>
                    <div class="font-bold" style="font-size: 14px; margin-bottom: 5px;">{{ $shipment->salesOrder->customer->name }}</div>
                    <div>{{ $shipment->salesOrder->customer->address ?? 'Alamat tidak tersedia' }}</div>
                    <div style="margin-top: 5px;">Telp: {{ $shipment->salesOrder->customer->phone }}</div>
                </div>
            </td>
            <td width="45%" style="padding-left: 15px;">
                <table width="100%" style="margin-top: 5px;">
                    <tr>
                        <td class="label">NO. SURAT JALAN</td>
                        <td class="font-bold text-right">{{ $shipment->shipment_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">REFERENSI SO</td>
                        <td class="font-bold text-right">{{ $shipment->salesOrder->so_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">TANGGAL KIRIM</td>
                        <td class="font-bold text-right">{{ date('d M Y', strtotime($shipment->date)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">EKSPE / SUPIR</td>
                        <td class="font-bold text-right">{{ $shipment->notes ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="20%">Kode Barang</th>
                <th width="45%">Nama Barang</th>
                <th width="15%">Satuan</th>
                <th width="15%" class="text-center">Qty Dikirim</th>
                </tr>
        </thead>
        <tbody>
            @foreach($shipment->details as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->product->sku }}</td>
                <td>
                    <div class="font-bold">{{ $item->product->name }}</div>
                </td>
                <td>{{ $item->product->unit }}</td>
                <td class="text-center font-bold" style="font-size: 14px;">{{ $item->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="note">
        Catatan:
        <ul>
            <li>Mohon diperiksa kembali keadaan barang saat diterima.</li>
            <li>Komplain setelah surat jalan ditandatangani tidak dapat kami layani.</li>
        </ul>
    </div>

    <div class="footer">
        <div class="signature-box">
            <div>Pengirim / Gudang,</div>
            <div class="sign-line"></div>
            <div>{{ $shipment->user->name }}</div>
        </div>
        <div class="signature-box">
            <div>Supir / Ekspedisi,</div>
            <div class="sign-line"></div>
            
        </div>
        <div class="signature-box">
            <div>Penerima,</div>
            <div class="sign-line"></div>
            
        </div>
    </div>

</body>
</html>