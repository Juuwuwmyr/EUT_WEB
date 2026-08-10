<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table QR Codes (1-20) - A4 Coupon Sheet</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 20px;
        }
        .no-print {
            text-align: center;
            padding: 16px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .no-print button {
            padding: 10px 22px;
            margin: 0 6px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            transition: transform 0.1s;
        }
        .btn-print {
            background: #10b981;
            color: #fff;
        }
        .btn-print:hover {
            background: #059669;
        }
        .btn-close {
            background: #e2e8f0;
            color: #1e293b;
        }
        .btn-close:hover {
            background: #cbd5e1;
        }
        .info-text {
            text-align: center;
            font-size: 13px;
            color: #475569;
            margin-bottom: 16px;
            font-weight: 600;
        }
        .print-container {
            /* Exact A4 Sheet: 210mm x 297mm */
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 6mm;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        .qr-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(5, 1fr);
            gap: 4mm;
            height: 283mm; /* Perfectly fills A4 297mm page minus 2x6mm padding */
        }
        .qr-label {
            text-align: center;
            border: 1px dashed #94a3b8;
            border-radius: 6px;
            padding: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            page-break-inside: avoid;
            background: #fff;
        }
        .table-number {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }
        .qr-code {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            flex-grow: 1;
            margin: 2px 0;
        }
        .qr-code canvas,
        .qr-code svg {
            max-width: 34mm !important;
            max-height: 34mm !important;
            width: 34mm !important;
            height: 34mm !important;
        }
        .url-text {
            font-size: 7px;
            color: #64748b;
            font-family: monospace;
            word-break: break-all;
            line-height: 1.1;
        }
        .subtitle {
            font-size: 7.5px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 2px;
            letter-spacing: 0.3px;
        }
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }
            .no-print, .info-text {
                display: none !important;
            }
            .print-container {
                width: 210mm;
                max-width: 210mm;
                height: 297mm;
                padding: 6mm;
                margin: 0;
                background: none;
                box-shadow: none;
            }
            .qr-grid {
                gap: 3mm;
                height: 285mm;
            }
            .qr-label {
                border: 1px dashed #64748b;
                page-break-inside: avoid;
            }
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            🖨️ Print A4 Coupon Sheet (20-in-1)
        </button>
        <button class="btn-close" onclick="window.history.back()">
            ← Back
        </button>
    </div>

    <div class="info-text">
        Tables 1 to 20 — A4 Coupon Sheet (4 columns × 5 rows = 20 tables on 1 A4 page)<br>
        Target URL: <code>https://eut-delivery.duckdns.org/shop?table=N</code>
    </div>

    <div class="print-container">
        <div class="qr-grid">
            @foreach($tables as $tableNum)
            <div class="qr-label">
                <div class="table-number">TABLE {{ $tableNum }}</div>
                <div class="qr-code" id="qr-{{ $tableNum }}"></div>
                <div class="url-text">eut-delivery.duckdns.org/shop?table={{ $tableNum }}</div>
                <div class="subtitle">E.U.T Snack House</div>
            </div>
            @endforeach
        </div>
    </div>

    <script>
        // Generate all QR codes for tables 1-20 pointing to https://eut-delivery.duckdns.org/shop?table=N
        @foreach($tables as $tableNum)
        new QRCode(document.getElementById('qr-{{ $tableNum }}'), {
            text: 'https://eut-delivery.duckdns.org/shop?table={{ $tableNum }}',
            width: 140,
            height: 140,
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
