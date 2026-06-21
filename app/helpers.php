<?php

/**
 * Format a phone number to Indonesian WhatsApp international format.
 * Converts leading '0' to '62' (Indonesia country code).
 *
 * Examples:
 *   08123456789  → 628123456789
 *   +628123456789 → 628123456789
 *   628123456789  → 628123456789
 *   8123456789    → 628123456789 (starts with 8, prepend 6)
 *
 * @param string|null $number
 * @return string
 */
function formatWhatsAppNumber(?string $number): string
{
    // Remove all non-digit characters
    $number = preg_replace('/[^0-9]/', '', $number ?? '');

    if (empty($number)) {
        return '';
    }

    // If starts with 0, replace with 62
    if (str_starts_with($number, '0')) {
        $number = '62' . substr($number, 1);
    }
    // If starts with 8 (missing country code & leading 0), prepend 62
    elseif (str_starts_with($number, '8')) {
        $number = '62' . $number;
    }
    // If already starts with 62, keep as is

    return $number;
}
