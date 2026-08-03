/**
 * wahb-printer.js
 * Silent print client for WebApp Hardware Bridge
 * Connects to ws://127.0.0.1:12212/printer
 */
(function (root, factory) {
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = factory();
    } else {
        root.WAHBPrinter = factory();
    }
})(typeof window !== 'undefined' ? window : this, function () {

    var DEFAULT_URL    = 'ws://127.0.0.1:12212/printer';
    var DEFAULT_KEY    = 'RECEIPT';
    var RECONNECT_BASE = 2000;
    var RECONNECT_MAX  = 30000;

    function WAHBPrinter(options) {
        var opts = Object.assign({
            url:           DEFAULT_URL,
            printerKey:    DEFAULT_KEY,
            autoReconnect: true,
        }, options || {});

        var _ws        = null;
        var _connected = false;
        var _retryDelay = RECONNECT_BASE;
        var _retryTimer = null;
        var _destroyed  = false;
        var _statusCb   = null;

        function _setStatus(s) {
            if (_statusCb) try { _statusCb(s); } catch(e) {}
        }

        function _connect() {
            if (_destroyed) return;
            if (_ws && (_ws.readyState === WebSocket.OPEN || _ws.readyState === WebSocket.CONNECTING)) return;
            _setStatus('connecting');
            try { _ws = new WebSocket(opts.url); } catch(e) { _scheduleReconnect(); return; }

            _ws.onopen = function() {
                _connected  = true;
                _retryDelay = RECONNECT_BASE;
                _setStatus('connected');
            };
            _ws.onclose = function() {
                _connected = false;
                _setStatus('disconnected');
                if (opts.autoReconnect && !_destroyed) _scheduleReconnect();
            };
            _ws.onerror = function() {};
            _ws.onmessage = function() {};
        }

        function _scheduleReconnect() {
            if (_retryTimer) clearTimeout(_retryTimer);
            _retryTimer = setTimeout(function() {
                _retryDelay = Math.min(_retryDelay * 2, RECONNECT_MAX);
                _connect();
            }, _retryDelay + Math.random() * 500);
        }

        function _send(payload) {
            if (!_connected || !_ws || _ws.readyState !== WebSocket.OPEN) return false;
            try { _ws.send(JSON.stringify(payload)); return true; } catch(e) { return false; }
        }

        this.printReceiptUrl = function(receiptPath, key) {
            var abs = (receiptPath && receiptPath.startsWith('http'))
                ? receiptPath
                : (window.location.origin + receiptPath);

            return _send({ type: key || opts.printerKey, url: abs });
        };

        this.isConnected  = function() { return _connected; };
        this.onStatusChange = function(fn) { _statusCb = fn; };
        this.setPrinterKey  = function(k) { opts.printerKey = k; };
        this.getPrinterKey  = function() { return opts.printerKey; };
        this.destroy = function() {
            _destroyed = true;
            if (_retryTimer) clearTimeout(_retryTimer);
            if (_ws) try { _ws.close(); } catch(e) {}
        };

        _connect();
    }

    return WAHBPrinter;
});
