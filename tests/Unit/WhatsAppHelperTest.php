<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class WhatsAppHelperTest extends TestCase
{
    public function test_converts_leading_zero_to_62(): void
    {
        $this->assertSame('628123456789', formatWhatsAppNumber('08123456789'));
    }

    public function test_strips_plus_and_separators(): void
    {
        $this->assertSame('628123456789', formatWhatsAppNumber('+62 812-3456-789'));
    }

    public function test_prepends_62_when_starts_with_8(): void
    {
        $this->assertSame('628123456789', formatWhatsAppNumber('8123456789'));
    }

    public function test_keeps_existing_62_prefix(): void
    {
        $this->assertSame('628123456789', formatWhatsAppNumber('628123456789'));
    }

    public function test_empty_null_or_non_numeric_returns_empty_string(): void
    {
        $this->assertSame('', formatWhatsAppNumber(null));
        $this->assertSame('', formatWhatsAppNumber(''));
        $this->assertSame('', formatWhatsAppNumber('abc'));
    }
}
