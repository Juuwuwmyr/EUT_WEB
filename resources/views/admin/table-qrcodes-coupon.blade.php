<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table QR Codes - Coupon Bond Print</title>
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
            /* A4/Coupon Bond: 210mm x 297mm */
            width: 210mm;
            margin: 0 auto;
            background: #fff;
            padding: 10mm;
        }
        .qr-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8mm;
        }
        .qr-label {
            text-align: center;
            border: 1px dashed #999;
            padding: 5mm;
            page-break-inside: avoid;
        }
        .table-number {
            font-size: 12px;
            font-weight: bold;
            margin: 2mm 0 1mm;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .qr-code {
            display: flex;
            justify-content: center;
            margin: 2mm 0;
            width: 100%;
            height: 40mm;
        }
        .qr-code canvas,
        .qr-code svg {
            max-width: 100% !important;
            max-height: 40mm !important;
            width: 40mm !important;
            height: 40mm !important;
        }
        .subtitle {
            font-size: 7px;
            color: #666;
            margin-top: 1mm;
            letter-spacing: 0.3px;
        }
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }
            .print-container {
                width: 210mm;
                max-width: 210mm;
                padding: 10mm;
                margin: 0;
                background: none;
            }
            .qr-label {
                page-break-inside: avoid;
            }
            @page {
                size: A4;
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
        .info-text {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            🖨️ Print to Coupon Bond
        </button>
        <button class="btn-close" onclick="window.history.back()">
            ← Back
        </button>
    </div>

    <div class="info-text">
        Tables 1-20 - Coupon Bond Format (A4 / 210mm x 297mm)<br>
        4 columns × 5 rows = 20 labels per page
    </div>

    <div class="print-container">
        <div class="qr-grid">
            @foreach($tables as $tableNum)
            <div class="qr-label">
                <div class="table-number">TABLE {{ $tableNum }}</div>
                <div class="qr-code" id="qr-{{ $tableNum }}"></div>
                <div class="subtitle">EUT Snack House</div>
            </div>
            @endforeach
        </div>
    </div>

    <script>
        // Generate all QR codes - 40mm size (optimized for coupon bond)
        @foreach($tables as $tableNum)
        new QRCode(document.getElementById('qr-{{ $tableNum }}'), {
            text: '{{ url("/checkout") }}?table={{ $tableNum }}',
            width: 150,
            height: 150,
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
