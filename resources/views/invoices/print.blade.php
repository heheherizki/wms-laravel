<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        
        /* Header & Title */
        .header { width: 100%; margin-bottom: 30px; border-bottom: 2px solid #2b6cb0; padding-bottom: 10px; }
        .company-name { font-size: 20px; font-weight: bold; color: #2b6cb0; }
        .invoice-title { float: right; font-size: 28px; font-weight: bold; color: #b0c4de; letter-spacing: 2px; }
        
        /* Info Boxes */
        .info-table { width: 100%; margin-bottom: 30px; }
        .info-table td { vertical-align: top; }
        .box-bill-to { background-color: #f7fafc; padding: 15px; border-radius: 8px; border-left: 4px solid #2b6cb0; }
        .label { font-size: 10px; text-transform: uppercase; color: #718096; font-weight: bold; margin-bottom: 5px; display: block; }
        
        /* Data Table */
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .details-table th { background-color: #2b6cb0; color: white; padding: 10px; text-align: left; text-transform: uppercase; font-size: 11px; }
        .details-table td { border-bottom: 1px solid #e2e8f0; padding: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-gray { background-color: #f7fafc; }
        
        /* Totals Section */
        .total-section { width: 40%; float: right; }
        .total-table { width: 100%; border-collapse: collapse; }
        .total-table td { padding: 8px; font-size: 13px; }
        .grand-total { background-color: #2b6cb0; color: white; font-weight: bold; font-size: 14px; }
        
        /* Payment Info */
        .payment-info { margin-top: 50px; padding: 15px; background-color: #f0fff4; border: 1px dashed #48bb78; border-radius: 8px; width: 50%; }
        .payment-title { font-weight: bold; color: #2f855a; margin-bottom: 5px; font-size: 13px; }
        
        /* Footer */
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #a0aec0; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <span class="invoice-title">INVOICE</span>
        <div class="company-name">WMS GUDANG SAYA</div>
        <div>Jl. Teknologi No. 123, Jakarta Selatan</div>
        <div>Telp: (021) 555-9999 | Email: finance@wms.com</div>
    </div>

    <table class="info-table">
        <tr>
            <td width="55%" style="padding-right: 20px;">
                <div class="box-bill-to">
                    <span class="label">TAGIHAN KEPADA:</span>
                    <div class="font-bold" style="font-size: 14px; color: #2d3748;">{{ $invoice->salesOrder->customer->name }}</div>
                    <div style="margin-top: 5px;">{{ $invoice->salesOrder->customer->address ?? 'Alamat tidak tersedia' }}</div>
                    <div>Telp: {{ $invoice->salesOrder->customer->phone }}</div>
                </div>
            </td>
            <td width="45%" style="padding-left: 20px;">
                <table width="100%">
                    <tr>
                        <td class="label">NO. INVOICE</td>
                        <td class="font-bold text-right">{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">REF. SURAT JALAN</td>
                        <td class="font-bold text-right">{{ $invoice->shipment->shipment_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">TANGGAL</td>
                        <td class="font-bold text-right">{{ date('d M Y', strtotime($invoice->date)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">JATUH TEMPO</td>
                        <td class="font-bold text-right" style="color: #e53e3e;">
                            {{ date('d M Y', strtotime($invoice->due_date)) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="40%">Deskripsi Barang</th>
                <th width="15%" class="text-center">Qty</th>
                <th width="20%" class="text-right">Harga Satuan</th>
                <th width="20%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->details as $index => $item)
            <tr class="{{ $loop->even ? 'bg-gray' : '' }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <div class="font-bold">{{ $item->product->name }}</div>
                    <div style="font-size: 10px; color: #718096;">SKU: {{ $item->product->sku }}</div>
                </td>
                <td class="text-center">{{ $item->quantity }} {{ $item->product->unit }}</td>
                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div>
        <div style="float: left; width: 50%;">
            <div class="payment-info">
                <div class="payment-title">INFO PEMBAYARAN</div>
                <div>Silakan transfer pembayaran ke:</div>
                <div style="margin-top: 5px; font-weight: bold;">BANK BCA</div>
                <div>No. Rek: 123-456-7890</div>
                <div>A.n: PT WMS GUDANG SAYA</div>
            </div>
        </div>

        <div class="total-section">
            <table class="total-table">
                <tr>
                    <td class="text-right font-bold">Subtotal</td>
                    <td class="text-right">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-right font-bold">Pajak (0%)</td>
                    <td class="text-right">Rp 0</td>
                </tr>
                <tr class="grand-total">
                    <td class="text-right" style="padding: 10px;">GRAND TOTAL</td>
                    <td class="text-right" style="padding: 10px;">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        <p>Terima kasih atas kerjasama Anda.</p>
        <p>Invoice ini sah dan diterbitkan secara otomatis oleh sistem.</p>
    </div>

</body>
</html>