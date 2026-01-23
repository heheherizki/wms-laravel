<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order {{ $purchase->po_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        
        /* Header */
        .header { width: 100%; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .company-name { font-size: 20px; font-weight: bold; color: #1a202c; }
        .po-title { float: right; font-size: 24px; font-weight: bold; color: #555; }
        
        /* Info Section */
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { vertical-align: top; }
        .box { background: #f8f9fa; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .label { font-weight: bold; font-size: 10px; text-transform: uppercase; color: #777; margin-bottom: 3px; display: block; }
        
        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #2d3748; color: #fff; padding: 8px; text-align: left; font-size: 11px; text-transform: uppercase; }
        .items-table td { border-bottom: 1px solid #eee; padding: 8px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        /* Totals */
        .total-section { width: 40%; float: right; }
        .total-table { width: 100%; border-collapse: collapse; }
        .total-table td { padding: 5px; }
        .grand-total { background: #2d3748; color: #fff; font-weight: bold; }
        
        /* Footer / Signature */
        .footer { margin-top: 50px; width: 100%; }
        .signature-box { width: 30%; float: left; text-align: center; margin-right: 5%; }
        .sign-line { border-bottom: 1px solid #333; margin-top: 50px; margin-bottom: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <span class="po-title">PURCHASE ORDER</span>
        <div class="company-name">WMS GUDANG SAYA</div>
        <div>Jl. Teknologi No. 123, Jakarta Selatan</div>
        <div>Telp: (021) 555-9999 | Email: purchasing@wms.com</div>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%" style="padding-right: 15px;">
                <div class="box">
                    <span class="label">KEPADA SUPPLIER:</span>
                    <div class="font-bold" style="font-size: 14px;">{{ $purchase->supplier->name }}</div>
                    <div>{{ $purchase->supplier->address ?? 'Alamat tidak tersedia' }}</div>
                    <div style="margin-top: 5px;">Telp: {{ $purchase->supplier->phone }}</div>
                </div>
            </td>
            <td width="50%" style="padding-left: 15px;">
                <div class="box">
                    <table width="100%">
                        <tr>
                            <td class="label">NO. PO</td>
                            <td class="font-bold text-right">{{ $purchase->po_number }}</td>
                        </tr>
                        <tr>
                            <td class="label">TANGGAL</td>
                            <td class="font-bold text-right">{{ date('d M Y', strtotime($purchase->date)) }}</td>
                        </tr>
                        <tr>
                            <td class="label">DIBUAT OLEH</td>
                            <td class="font-bold text-right">{{ $purchase->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="label">STATUS</td>
                            <td class="font-bold text-right" style="text-transform: uppercase;">{{ $purchase->status }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="40%">Deskripsi Barang</th>
                <th width="15%" class="text-center">Qty</th>
                <th width="20%" class="text-right">Harga Satuan</th>
                <th width="20%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->details as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <div class="font-bold">{{ $item->product->name }}</div>
                    <div style="font-size: 10px; color: #666;">SKU: {{ $item->product->sku }}</div>
                </td>
                <td class="text-center">{{ $item->quantity }} {{ $item->product->unit }}</td>
                <td class="text-right">Rp {{ number_format($item->buy_price, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table class="total-table">
            <tr class="grand-total">
                <td>GRAND TOTAL</td>
                <td class="text-right">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($purchase->notes)
    <div class="box" style="margin-top: 20px;">
        <span class="label">CATATAN:</span>
        <p style="margin: 0; font-style: italic;">{{ $purchase->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <div class="signature-box">
            <div>Dibuat Oleh,</div>
            <div class="sign-line"></div>
            <div>{{ $purchase->user->name }}</div>
        </div>
        <div class="signature-box">
            <div>Disetujui Oleh,</div>
            <div class="sign-line"></div>
            <div>( Manager Gudang )</div>
        </div>
        <div class="signature-box">
            <div>Diterima Oleh,</div>
            <div class="sign-line"></div>
            <div>{{ $purchase->supplier->name }}</div>
        </div>
    </div>

</body>
</html>