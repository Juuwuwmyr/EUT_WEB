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
            max-width: 80mm;
            margin: 0 auto;
            background: #fff;
            padding: 10mm;
        }
        .qr-label {
            text-align: center;
            margin: 15mm 0;
            border: 1px dashed #999;
            padding: 8mm;
            page-break-inside: avoid;
        }
        .table-number {
            font-size: 24px;
            font-weight: bold;
            margin: 5mm 0;
            text-transform: uppercase;
        }
        .qr-code {
            display: flex;
            justify-content: center;
            margin: 5mm 0;
            min-height: 60mm;
        }
        .qr-code canvas,
        .qr-code svg {
            max-width: 100% !important;
            max-height: 60mm !important;
        }
        .subtitle {
            font-size: 10px;
            color: #666;
            margin-top: 3mm;
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .print-container {
                max-width: 100%;
                padding: 0;
                margin: 0;
            }
            .qr-label {
                page-break-inside: avoid;
            }
        }
        .no-print {
            text-align: center;
            padding: 20px;
            background: #f0f0f0;
            border-radius: 8px;
            margin-bottom: 20px;
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
        // Generate all QR codes
        @foreach($tables as $tableNum)
        new QRCode(document.getElementById('qr-{{ $tableNum }}'), {
            text: 'TABLE {{ $tableNum }}',
            width: 200,
            height: 200,
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
