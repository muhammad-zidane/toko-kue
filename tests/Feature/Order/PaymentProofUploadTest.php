<?php

namespace Tests\Feature\Order;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\RequiresPhpExtension('gd')]
class PaymentProofUploadTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function owner_upload_valid_jpg_sets_pending_unpaid()
    {
        Storage::fake('public');

        $owner = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'status' => 'unpaid',
            'proof_image' => null,
        ]);

        $file = UploadedFile::fake()->image('bukti.jpg', 640, 480);

        $response = $this->actingAs($owner)->post(route('orders.uploadProof', $order), [
            'proof_image' => $file,
        ]);

        // Assert redirect to success page
        $response->assertRedirect(route('orders.success', $order));
        $response->assertSessionHas('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');

        // Assert file stored
        Storage::disk('public')->assertExists($payment->refresh()->proof_image);

        // Assert payment status stays 'unpaid'
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'unpaid',
            'proof_image' => $payment->fresh()->proof_image,
        ]);

        // Assert order status stays 'pending' (NOT processing)
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function upload_php_file_rejected()
    {
        Storage::fake('public');

        $owner = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
        ]);
        Payment::factory()->create([
            'order_id' => $order->id,
            'status' => 'unpaid',
            'proof_image' => null,
        ]);

        // Attempt upload with invalid file (fake text file)
        $file = UploadedFile::fake()->create('payload.php', 100, 'text/plain');

        $response = $this->actingAs($owner)->post(route('orders.uploadProof', $order), [
            'proof_image' => $file,
        ]);

        // Assert validation error
        $response->assertSessionHasErrors('proof_image');

        // Assert file was NOT stored
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'proof_image' => null,
        ]);

        // Assert order unchanged
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function non_owner_gets_403()
    {
        Storage::fake('public');

        $owner = User::factory()->create(['role' => 'customer']);
        $other_user = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create(['user_id' => $owner->id]);
        Payment::factory()->create([
            'order_id' => $order->id,
            'status' => 'unpaid',
            'proof_image' => null,
        ]);

        $file = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->actingAs($other_user)->post(route('orders.uploadProof', $order), [
            'proof_image' => $file,
        ]);

        // Assert 403 Forbidden
        $this->assertEquals(403, $response->status());

        // Assert no file stored, no changes
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'proof_image' => null,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_confirm_after_upload_sets_paid_processing()
    {
        Storage::fake('public');

        $owner = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'status' => 'unpaid',
        ]);

        // Step 1: Owner upload
        $file = UploadedFile::fake()->image('bukti.jpg');
        $this->actingAs($owner)->post(route('orders.uploadProof', $order), [
            'proof_image' => $file,
        ]);

        // Assert state after upload: unpaid/pending
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'unpaid',
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);

        // Step 2: Admin confirm payment
        $this->actingAs($admin)
            ->post(route('admin.orders.confirmPayment', $order));

        // Assert payment now 'paid'
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);

        // Assert order now 'processing'
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function pending_verification_query_detects_order()
    {
        Storage::fake('public');

        $owner = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
        ]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'status' => 'unpaid',
            'proof_image' => null,
        ]);

        // Upload proof
        $file = UploadedFile::fake()->image('bukti.jpg');
        $this->actingAs($owner)->post(route('orders.uploadProof', $order), [
            'proof_image' => $file,
        ]);

        // Query: find orders waiting for verification
        // (proof_image IS NOT NULL AND payment.status == 'unpaid')
        $pendingVerification = Order::whereHas('payment', function ($q) {
            $q->whereNotNull('proof_image')
              ->where('status', 'unpaid');
        })->get();

        // Assert this order is detected
        $this->assertContains($order->id, $pendingVerification->pluck('id'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function paid_payment_blocks_reupload()
    {
        Storage::fake('public');

        $owner = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Create order with ALREADY PAID payment
        $order = Order::factory()->create([
            'user_id' => $owner->id,
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'status' => 'paid',
            'paid_at' => now(),
            'proof_image' => 'old_proof.jpg',
        ]);

        // Owner tries to upload again
        $file = UploadedFile::fake()->image('bukti2.jpg');

        $response = $this->actingAs($owner)->post(route('orders.uploadProof', $order), [
            'proof_image' => $file,
        ]);

        // Assert redirect back with error
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Pembayaran untuk pesanan ini sudah dikonfirmasi.');

        // Assert payment.status STAYED 'paid' (NOT downgraded to unpaid)
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
            'proof_image' => 'old_proof.jpg', // Unchanged
        ]);

        // Assert order.status unchanged
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }
}
