<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Retur Pembelian #{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        
        /* HEADER */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
        }
        .company-address {
            font-size: 10px;
            color: #555;
        }
        .doc-title {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
            color: #d32f2f; /* Warna Merah untuk Retur */
            text-transform: uppercase;
        }
        .doc-subtitle {
            text-align: right;
            font-size: 10px;
            font-weight: bold;
        }

        /* INFO SECTIONS */
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .info-table td {
            vertical-align: top;
            padding: 2px;
        }
        .info-label {
            font-weight: bold;
            width: 100px;
        }
        .supplier-box {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        /* ITEMS TABLE */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #888;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #eee;
            color: #000;
            text-transform: uppercase;
            font-size: 10px;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* FOOTER & SIGNATURE */
        .reason-box {
            margin-top: 10px;
            padding: 8px;
            border: 1px dashed #888;
            font-style: italic;
            background: #fff;
        }
        
        .footer-table {
            width: 100%;
            margin-top: 50px;
            text-align: center;
        }
        .sign-box {
            height: 70px;
        }
        .sign-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .sign-title {
            font-size: 9px;
            color: #666;
        }

        /* UTILS */
        .print-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #aaa;
            text-align: center;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="60%">
                <div class="company-name">PT. WMS GUDANG MAKMUR</div>
                <div class="company-address">
                    Jl. Pergudangan Industri No. 88, Jakarta Selatan<br>
                    Telp: (021) 555-6789 | Email: admin@wmsgudang.com
                </div>
            </td>
            <td width="40%" style="vertical-align: bottom;">
                <div class="doc-title">NOTA RETUR PEMBELIAN</div>
                <div class="doc-subtitle">( PURCHASE RETURN / DEBIT NOTE )</div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="55%">
                <div style="margin-bottom: 4px; font-weight:bold;">Kepada Yth (Supplier):</div>
                <div class="supplier-box">
                    <strong>{{ $return->purchase->supplier->name }}</strong><br>
                    {{ $return->purchase->supplier->address ?? 'Alamat tidak tersedia' }}<br>
                    Telp: {{ $return->purchase->supplier->phone ?? '-' }}
                </div>
            </td>
            <td width="5%"></td>
            <td width="40%">
                <table width="100%">
                    <tr>
                        <td class="info-label">No. Retur</td>
                        <td>: <strong>RET-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal</td>
                        <td>: {{ date('d F Y', strtotime($return->date)) }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Ref. PO</td>
                        <td>: {{ $return->purchase->po_number }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Status</td>
                        <td>: {{ strtoupper($return->status) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="font-weight: bold; font-size: 10px;">ALASAN PENGEMBALIAN:</div>
    <div class="reason-box">
        "{{ $return->reason }}"
    </div>
    <br>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode (SKU)</th>
                <th width="40%">Nama Barang / Deskripsi</th>
                <th width="10%">Satuan</th>
                <th width="10%">Qty Retur</th>
                <th width="20%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($return->details as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->product->sku }}</td>
                <td>{{ $item->product->name }}</td>
                <td class="text-center">{{ $item->product->unit }}</td>
                <td class="text-center font-bold">{{ $item->quantity }}</td>
                <td>{{ $item->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="font-size: 10px; margin-top: 5px;">
        <strong>Catatan Penting:</strong>
        <ul>
            <li>Mohon barang tersebut diterima dan diperiksa kondisinya.</li>
            <li>Dokumen ini berfungsi sebagai bukti pengurangan hutang (Debit Note) atau permintaan penggantian barang.</li>
        </ul>
    </div>

    <table class="footer-table">
        <tr>
            <td width="33%">
                Disiapkan Oleh,
                <div class="sign-box"></div>
                <div class="sign-name">{{ $return->user->name }}</div>
                <div class="sign-title">Gudang / Logistik</div>
            </td>
            <td width="33%">
                Disetujui Oleh,
                <div class="sign-box"></div>
                <div class="sign-name">( ........................... )</div>
                <div class="sign-title">Manager Operasional</div>
            </td>
            <td width="33%">
                Diterima Oleh,
                <div class="sign-box"></div>
                <div class="sign-name">( ........................... )</div>
                <div class="sign-title">{{ $return->purchase->supplier->name }}</div>
            </td>
        </tr>
    </table>

    <div class="print-footer">
        Dicetak otomatis oleh Sistem WMS pada {{ date('d-m-Y H:i') }} | Halaman 1
    </div>

</body>
</html>