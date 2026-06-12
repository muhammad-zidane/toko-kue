<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentStatusLabelTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function payment_unpaid_no_proof_label_belum_bayar()
    {
        $payment = Payment::make(['status' => 'unpaid', 'proof_image' => null]);

        $this->assertSame('Belum Bayar', $payment->status_label);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function payment_unpaid_with_proof_label_menunggu_verifikasi()
    {
        $payment = Payment::make(['status' => 'unpaid', 'proof_image' => 'proofs/bukti.jpg']);

        $this->assertSame('Menunggu Verifikasi', $payment->status_label);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function payment_paid_label_lunas()
    {
        $payment = Payment::make(['status' => 'paid', 'proof_image' => null]);

        $this->assertSame('Lunas', $payment->status_label);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function payment_failed_label_pembayaran_ditolak()
    {
        $payment = Payment::make(['status' => 'failed', 'proof_image' => null]);

        $this->assertSame('Pembayaran Ditolak', $payment->status_label);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function belum_ada_payment_label_belum_bayar()
    {
        $order = new Order();

        $this->assertSame('Belum Bayar', $order->payment?->status_label ?? 'Belum Bayar');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function halaman_orders_index_tampilkan_dua_label_berbeda_sesuai_kondisi_bukti()
    {
        $user = User::factory()->create();

        // Order A: unpaid, proof_image null → harus tampil "Belum Bayar"
        $orderA = Order::factory()->for($user)->create();
        Payment::factory()->for($orderA)->create([
            'status'      => 'unpaid',
            'proof_image' => null,
        ]);

        // Order B: unpaid, proof_image terisi → harus tampil "Menunggu Verifikasi"
        $orderB = Order::factory()->for($user)->create();
        Payment::factory()->for($orderB)->create([
            'status'      => 'unpaid',
            'proof_image' => 'proofs/bukti.jpg',
        ]);

        $response = $this->actingAs($user)->get(route('orders.index'));

        $response->assertOk();
        $response->assertSee('Belum Bayar');
        $response->assertSee('Menunggu Verifikasi');
    }
}
