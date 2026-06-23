
<?php

/**
 * Konfigurasi Midtrans Payment Gateway.
 *
 * Nilai dibaca dari .env agar mudah diubah tanpa menyentuh kode.
 * Untuk sandbox (development): MIDTRANS_IS_PRODUCTION=false
 *
 * Variabel env:
 *   MIDTRANS_IS_PRODUCTION     false untuk sandbox, true untuk production
 *   MIDTRANS_SERVER_KEY        Server Key dari dashboard Midtrans
 *   MIDTRANS_CLIENT_KEY        Client Key dari dashboard Midtrans
 *   MIDTRANS_MERCHANT_ID       Merchant ID dari dashboard Midtrans
 *   MIDTRANS_SNAP_URL          (opsional) override Snap base URL
 *   MIDTRANS_IRIS_URL          (opsional) override IRIS base URL
 *   MIDTRANS_COREAPI_URL       (opsional) override Core API base URL
 */
return [

    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),

    /**
     * Midtrans 3DS (Veritrans) — biarkan true untuk keamanan ekstra.
     */
    'enable_3ds' => true,

    /**
     * Snapshot URL (normalnya tidak perlu diganti).
     */
    'snap_url' => env('MIDTRANS_SNAP_URL', ''),

    /**
     * IRIS URL (normalnya tidak perlu diganti).
     */
    'iris_url' => env('MIDTRANS_IRIS_URL', ''),

    /**
     * Core API URL (normalnya tidak perlu diganti).
     */
    'core_api_url' => env('MIDTRANS_COREAPI_URL', ''),

];
