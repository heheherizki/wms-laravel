<!DOCTYPE html>
<html>
<head>
    <title>Cetak Barcode - {{ $product->name }}</title>
    <style>
        body { font-family: sans-serif; }
        
        /* Container Grid agar label berjejer rapi */
        .container {
            width: 100%;
            display: table; /* Pengganti Flexbox untuk DomPDF */
            border-spacing: 10px; /* Jarak antar stiker */
        }
        
        /* Kotak Stiker */
        .label-sticker {
            display: inline-block; /* Agar berjejer ke samping */
            width: 200px;         /* Lebar stiker */
            height: 120px;        /* Tinggi stiker */
            border: 2px dashed #333; /* Garis putus-putus untuk panduan potong */
            padding: 10px;
            text-align: center;
            margin-bottom: 15px;
            margin-right: 15px;
            vertical-align: top;
        }

        .product-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            height: 30px; /* Batasi tinggi nama biar gak nabrak */
            overflow: hidden;
        }

        .barcode-img {
            width: 90%;
            height: 40px;
            margin: 5px auto;
        }

        .sku-text {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
            font-family: monospace;
        }

        .meta {
            font-size: 10px;
            color: #555;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    
    @for($i = 0; $i < $jumlahCetak; $i++)
        <div class="label-sticker">
            <div class="product-name">{{ Str::limit($product->name, 40) }}</div>
            
            <img src="data:image/png;base64,{{ $barcodeBase64 }}" class="barcode-img">
            
            <div class="sku-text">{{ $product->sku }}</div>
            
            <div class="meta">
                {{ $product->category }} | Rak: {{ $product->rack_location ?? '-' }}
            </div>
        </div>
    @endfor

</body>
</html>