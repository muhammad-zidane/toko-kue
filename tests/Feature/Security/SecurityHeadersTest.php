<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function security_headers_present_and_hsts_absent()
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Empat header wajib ada
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');

        // HSTS sengaja TIDAK dipasang (aplikasi berjalan di http://localhost)
        $response->assertHeaderMissing('Strict-Transport-Security');
    }
}
