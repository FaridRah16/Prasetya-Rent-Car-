<?php

/**
 * Konfigurasi pembayaran (Bank Transfer).
 *
 * Nilai dibaca dari .env agar mudah diubah tanpa menyentuh kode.
 * Variabel env:
 *   PAYMENT_BANK_NAME        Nama bank (mis. "Bank BCA")
 *   PAYMENT_ACCOUNT_NUMBER   Nomor rekening tujuan
 *   PAYMENT_ACCOUNT_NAME     Nama pemilik rekening
 */
return [

    'bank_name' => env('PAYMENT_BANK_NAME', 'Bank BCA'),

    'account_number' => env('PAYMENT_ACCOUNT_NUMBER', '1234567890'),

    'account_name' => env('PAYMENT_ACCOUNT_NAME', 'Prasetya Rent Car'),

];
