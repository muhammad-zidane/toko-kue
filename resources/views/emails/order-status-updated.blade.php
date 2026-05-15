@php
$statusLabels = [
    'pending'    => 'Menunggu Konfirmasi',
    'processing' => 'Sedang Diproses',
    'shipped'    => 'Sedang Dikirim',
    'completed'  => 'Selesai',
    'cancelled'  => 'Dibatalkan',
];
$label = $statusLabels[$order->status] ?? ucfirst($order->status);
@endphp
<x-mail::message>
# Update Status Pesanan #{{ $order->order_code }}

Halo **{{ $order->user->name }}**,

Status pesananmu telah diperbarui menjadi: **{{ $label }}**.

| Info | Detail |
|------|--------|
| Nomor Pesanan | {{ $order->order_code }} |
| Status Terbaru | {{ $label }} |
| Tanggal Kirim | {{ $order->delivery_date?->format('d M Y') ?? '-' }} |
| Total | Rp {{ number_format($order->total_price, 0, ',', '.') }} |

<x-mail::button :url="route('orders.show', $order)">
Lihat Detail Pesanan
</x-mail::button>

Terima kasih sudah mempercayai **Jagoan Kue**! 🍰

Salam manis,<br>
**Tim Jagoan Kue**
</x-mail::message>
