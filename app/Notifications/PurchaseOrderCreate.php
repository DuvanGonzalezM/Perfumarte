<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PurchaseOrderCreate extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Se ha creado un nuevo pedido de compra con el número: ' . $this->purchaseOrder->purchase_order_id,
            'url' => route('orders.detail', $this->purchaseOrder->purchase_order_id),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'newNotification' => DatabaseNotification::where('id', $this->id)->first()
        ]);
    }
}
