<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private string $appName;
    private ?string $logoUrl;

    private const STATUS_CONTENT = [
        'processing' => [
            'icon'    => '⚙️',
            'heading' => 'Order Being Processed',
            'message' => 'Great news! We\'ve received your payment and are now preparing your order with care.',
            'color'   => '#1d4ed8',
            'bg'      => 'rgba(13,110,253,0.08)',
        ],
        'shipped' => [
            'icon'    => '🚚',
            'heading' => 'Your Order Has Shipped!',
            'message' => 'Your order is on its way! It should arrive within the estimated delivery window.',
            'color'   => '#6b21a8',
            'bg'      => 'rgba(124,58,237,0.08)',
        ],
        'delivered' => [
            'icon'    => '✅',
            'heading' => 'Order Delivered!',
            'message' => 'Your order has been delivered. We hope you love your purchase! Feel free to leave a review.',
            'color'   => '#166534',
            'bg'      => 'rgba(34,197,94,0.08)',
        ],
        'cancelled' => [
            'icon'    => '❌',
            'heading' => 'Order Cancelled',
            'message' => 'Your order has been cancelled. If you did not request this or have questions, please contact us.',
            'color'   => '#991b1b',
            'bg'      => 'rgba(239,68,68,0.08)',
        ],
    ];

    public function __construct(public Order $order)
    {
        $settings      = Setting::whereIn('key', ['site_name', 'logo'])->pluck('value', 'key');
        $this->appName = $settings->get('site_name') ?: config('app.name');
        $logoPath      = $settings->get('logo');
        $this->logoUrl = $logoPath ? asset('storage/' . $logoPath) : null;
    }

    public function envelope(): Envelope
    {
        $label = ucfirst($this->order->status);

        return new Envelope(
            subject: 'Order ' . $label . ' – #' . str_pad($this->order->id, 5, '0', STR_PAD_LEFT),
        );
    }

    public function content(): Content
    {
        $status  = $this->order->status;
        $content = self::STATUS_CONTENT[$status] ?? [
            'icon'    => '📦',
            'heading' => 'Order Update',
            'message' => 'Your order status has been updated to ' . ucfirst($status) . '.',
            'color'   => '#374151',
            'bg'      => 'rgba(0,0,0,0.04)',
        ];

        return new Content(
            view: 'emails.order-status',
            with: [
                'order'    => $this->order,
                'content'  => $content,
                'appName'  => $this->appName,
                'logoUrl'  => $this->logoUrl,
                'shopUrl'  => rtrim(config('app.url'), '/') . '/shop',
                'orderUrl' => rtrim(config('app.url'), '/') . '/orders/' . $this->order->id,
            ],
        );
    }
}
