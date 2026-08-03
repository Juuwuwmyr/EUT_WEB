require('dotenv').config();
const axios = require('axios');
const { ThermalPrinter, PrinterTypes, CharacterSet } = require('node-thermal-printer');

const APP_URL      = (process.env.APP_URL || 'http://localhost').replace(/\/$/, '');
const PRINT_TOKEN  = process.env.PRINT_TOKEN || '';
const POLL_MS      = parseInt(process.env.POLL_INTERVAL || '3000', 10);
const PRINTER_TYPE = (process.env.PRINTER_TYPE || 'usb').toLowerCase();
const PRINTER_IF   = process.env.PRINTER_INTERFACE || '\\\\.\COM3';

console.log('╔══════════════════════════════════════════╗');
console.log('║   EUT Kitchen Auto-Print Server v1.0     ║');
console.log('╚══════════════════════════════════════════╝');
console.log(`Server : ${APP_URL}`);
console.log(`Printer: ${PRINTER_IF} (${PRINTER_TYPE})`);
console.log(`Poll   : every ${POLL_MS / 1000}s`);
console.log('─────────────────────────────────────────');

// ── Init printer ─────────────────────────────────────────
function createPrinter() {
    return new ThermalPrinter({
        type:        PRINTER_TYPE === 'network' ? PrinterTypes.EPSON : PrinterTypes.EPSON,
        interface:   PRINTER_IF,
        characterSet: CharacterSet.PC852_LATIN2,
        removeSpecialCharacters: false,
        lineCharacter: '─',
        options: { timeout: 5000 },
    });
}

// ── Format currency ──────────────────────────────────────
function php(amount) {
    return 'P' + parseFloat(amount).toFixed(2);
}

// ── Print a kitchen ticket ───────────────────────────────
async function printTicket(job) {
    const printer = createPrinter();
    const isConnected = await printer.isPrinterConnected();
    if (!isConnected) {
        console.error(`[PRINT] ✗ Printer not connected — ${PRINTER_IF}`);
        return false;
    }

    const o = job;

    printer.alignCenter();
    printer.bold(true);
    printer.setTextSize(1, 1);
    printer.println('⬛ KITCHEN ⬛');
    printer.bold(false);
    printer.newLine();

    printer.setTextSize(2, 2);
    printer.bold(true);
    printer.println(o.order_number);
    printer.setTextSize(1, 1);
    printer.bold(false);

    printer.println(o.order_type_label.toUpperCase());
    printer.drawLine();

    printer.alignLeft();
    printer.println(`Customer: ${o.customer}`);
    printer.println(`Time    : ${o.placed_at}`);

    if (o.order_type === 'dine_in' && o.table_number) {
        printer.bold(true);
        printer.println(`TABLE   : ${o.table_number}`);
        printer.bold(false);
    }

    if (o.order_type === 'delivery' && o.delivery_address) {
        printer.println(`Address : ${o.delivery_address}`);
    }

    printer.drawLine();
    printer.setTextSize(1, 1);

    // Items
    for (const item of o.items) {
        printer.bold(true);
        printer.println(`${item.qty}x ${item.name}`);
        printer.bold(false);
        if (item.modifiers && item.modifiers.length) {
            for (const mod of item.modifiers) {
                const extra = mod.price ? ` ${mod.price}` : '';
                printer.println(`  - ${mod.name}${extra}`);
            }
        }
    }

    printer.drawLine();

    if (o.notes) {
        printer.bold(true);
        printer.println('!! SPECIAL INSTRUCTION !!');
        printer.bold(false);
        printer.println(o.notes);
        printer.drawLine();
    }

    printer.alignCenter();
    printer.println('*** KITCHEN COPY — NO PRICE ***');
    printer.newLine();
    printer.newLine();
    printer.cut();

    try {
        await printer.execute();
        console.log(`[PRINT] ✓ Printed kitchen ticket for ${o.order_number}`);
        return true;
    } catch (err) {
        console.error(`[PRINT] ✗ Print failed for ${o.order_number}:`, err.message);
        return false;
    }
}

// ── Poll and print loop ──────────────────────────────────
async function poll() {
    try {
        const res = await axios.get(`${APP_URL}/api/print-server/pending-prints`, {
            headers: { 'X-Print-Token': PRINT_TOKEN },
            timeout: 5000,
        });

        const jobs = res.data.jobs || [];

        if (jobs.length > 0) {
            console.log(`[POLL] ${jobs.length} job(s) pending`);
        }

        for (const job of jobs) {
            const ok = await printTicket(job);
            if (ok) {
                // Mark as printed so it doesn't re-print
                await axios.post(`${APP_URL}/api/print-server/mark-printed/${job.job_id}`, {}, {
                    headers: { 'X-Print-Token': PRINT_TOKEN },
                    timeout: 5000,
                }).catch(e => console.error('[MARK] Failed to mark printed:', e.message));
            }
        }
    } catch (err) {
        if (err.code === 'ECONNREFUSED') {
            console.error(`[POLL] ✗ Cannot reach server — ${APP_URL}`);
        } else if (err.response?.status === 401) {
            console.error('[POLL] ✗ Invalid PRINT_TOKEN — check your .env');
        } else {
            console.error('[POLL] Error:', err.message);
        }
    }
}

// ── Start ────────────────────────────────────────────────
console.log('Starting...\n');
poll(); // run immediately on start
setInterval(poll, POLL_MS);
