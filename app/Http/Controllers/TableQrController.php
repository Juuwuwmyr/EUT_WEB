<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TableQrController extends Controller
{
    // ── GET /admin/table-qrcodes — display QR codes for all tables ──
    public function index()
    {
        $tables = range(1, 30);
        return view('admin.table-qrcodes', compact('tables'));
    }

    // ── GET /admin/table-qrcodes/print — print QR codes for thermal printer ──
    public function print()
    {
        $tables = range(1, 30);
        return view('admin.table-qrcodes-print', compact('tables'));
    }

    // ── GET /admin/table-qrcodes/coupon — print QR codes for coupon bond ──
    public function coupon()
    {
        $tables = range(1, 20);
        return view('admin.table-qrcodes-coupon', compact('tables'));
    }
}
