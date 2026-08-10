@extends('admin.layout')
@section('title', 'Table QR Codes')

@push('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
    .qr-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        padding: 20px;
    }
    .qr-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .qr-code-container {
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }
    .qr-code-container canvas,
    .qr-code-container svg {
        max-width: 100% !important;
        max-height: 100% !important;
    }
    .table-label {
        font-size: 18px;
        font-weight: 700;
        color: #111;
        margin: 12px 0 8px;
    }
    .table-subtitle {
        font-size: 12px;
        color: #666;
        margin-bottom: 12px;
    }
    .btn-print-all {
        position: fixed;
        bottom: 24px;
        right: 24px;
        padding: 14px 28px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        z-index: 100;
    }
    .btn-print-all:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.5);
    }
</style>
@endpush

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">
    <div style="padding: 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
        <div>
            <h1 style="font-size: 28px; font-weight: 700; margin: 0 0 4px; color: #111;">Table QR Codes</h1>
            <p style="color: #666; font-size: 14px; margin: 0;">Generate and print QR codes for dine-in table numbers (1-20) targeting <code>https://eut-delivery.duckdns.org/shop?table=N</code></p>
        </div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('admin.table-qrcodes.coupon') }}" style="display:inline-flex;align-items:center;gap:6px;padding:12px 20px;background:linear-gradient(135deg, #10b981, #059669);color:#fff;border-radius:8px;font-weight:700;font-size:14px;text-decoration:none;box-shadow:0 4px 12px rgba(16,185,129,0.3);">
                📄 Print A4 Coupon Sheet (1-20)
            </a>
            <a href="{{ route('admin.table-qrcodes.print') }}" style="display:inline-flex;align-items:center;gap:6px;padding:12px 20px;background:linear-gradient(135deg, #2563eb, #1d4ed8);color:#fff;border-radius:8px;font-weight:700;font-size:14px;text-decoration:none;box-shadow:0 4px 12px rgba(37,99,235,0.3);">
                🖨️ Print POS Rolls (1-30)
            </a>
        </div>
    </div>

    <div class="qr-grid" id="qrGrid">
        @foreach($tables as $tableNum)
        <div class="qr-card" id="qr-card-{{ $tableNum }}">
            <div class="table-label">Table {{ $tableNum }}</div>
            <div class="table-subtitle">Dine-in QR Code</div>
            <div class="qr-code-container" id="qr-{{ $tableNum }}"></div>
            <div style="font-size:10px;color:#888;margin-bottom:8px;word-break:break-all;">https://eut-delivery.duckdns.org/shop?table={{ $tableNum }}</div>
            <button onclick="downloadQR({{ $tableNum }})" style="width: 100%; padding: 8px 12px; border: 1px solid #e5e7eb; background: #fff; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; color: #333; transition: all 0.2s;">
                ↓ Download PNG
            </button>
        </div>
        @endforeach
    </div>
</div>

<script>
// Generate all QR codes
@foreach($tables as $tableNum)
new QRCode(document.getElementById('qr-{{ $tableNum }}'), {
    text: 'https://eut-delivery.duckdns.org/shop?table={{ $tableNum }}',
    width: 200,
    height: 200,
    colorDark: '#000000',
    colorLight: '#ffffff',
    correctLevel: QRCode.CorrectLevel.H
});
@endforeach

function downloadQR(tableNum) {
    const canvas = document.querySelector(`#qr-${tableNum} canvas`);
    if (!canvas) {
        alert('QR code not ready yet');
        return;
    }
    const link = document.createElement('a');
    link.href = canvas.toDataURL('image/png');
    link.download = `table-${tableNum}-qr.png`;
    link.click();
}
</script>
@endsection
