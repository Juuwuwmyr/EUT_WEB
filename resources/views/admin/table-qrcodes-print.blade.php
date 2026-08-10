<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table QR Codes - POS Print</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            padding: 20px;
        }
        .print-container {
            /* 58mm thermal printer (typical POS) */
            width: 58mm;
            margin: 0 auto;
            background: #fff;
            padding: 3mm;
        }
        .qr-label {
            text-align: center;
            margin: 1.5mm 0;
            border: 1px dashed #999;
            padding: 2mm;
            page-break-inside: avoid;
        }
        .table-number {
            font-size: 11px;
            font-weight: bold;
            margin: 1mm 0 0.5mm;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .qr-code {
            display: flex;
            justify-content: center;
            margin: 1mm 0;
            width: 100%;
        }
        .qr-code canvas,
        .qr-code svg {
            max-width: 100% !important;
            max-height: 15mm !important;
            width: 15mm !important;
            height: 15mm !important;
        }
        .subtitle {
            font-size: 7px;
            color: #666;
            margin-top: 0.5mm;
            letter-spacing: 0.3px;
        }
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }
            .print-container {
                width: 58mm;
                max-width: 58mm;
                padding: 3mm;
                margin: 0;
                background: none;
            }
            .qr-label {
                margin: 2mm 0;
                padding: 3mm;
                border: none;
                page-break-inside: avoid;
            }
            @page {
                size: 58mm auto;
                margin: 0;
            }
        }
        .no-print {
            text-align: center;
            padding: 20px;
            background: #f0f0f0;
            border-radius: 8px;
            margin-bottom: 20px;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }
        .no-print button {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }
        .btn-print {
            background: #2563eb;
            color: #fff;
        }
        .btn-print:hover {
            background: #1d4ed8;
        }
        .btn-close {
            background: #f3f4f6;
            color: #333;
        }
        .btn-close:hover {
            background: #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            🖨️ Print All Tables to POS
        </button>
        <button class="btn-close" onclick="window.history.back()">
            ← Back
        </button>
    </div>

    <div class="print-container">
        @foreach($tables as $tableNum)
        <div class="qr-label">
            <div class="table-number">TABLE {{ $tableNum }}</div>
            <div class="qr-code" id="qr-{{ $tableNum }}"></div>
            <div class="subtitle">EUT Snack House - Dine In</div>
        </div>
        @endforeach
    </div>

    <script>
        // Generate all QR codes - 15mm size (half of previous)
        @foreach($tables as $tableNum)
        new QRCode(document.getElementById('qr-{{ $tableNum }}'), {
            text: 'https://eut-delivery.duckdns.org/shop?table={{ $tableNum }}',
            width: 120,
            height: 120,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
        @endforeach

        // Auto-print if requested
        if (new URLSearchParams(window.location.search).get('auto') === '1') {
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 500);
            });
        }
    </script>
</body>
</html>
