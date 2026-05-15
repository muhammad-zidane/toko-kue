<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id'   => $this->order->id,
            'order_code' => $this->order->order_code,
            'message'    => 'Pesanan baru masuk: #' . $this->order->order_code,
            'total'      => $this->order->total_price,
            'url'        => route('admin.orders.show', $this->order),
        ];
    }
}
