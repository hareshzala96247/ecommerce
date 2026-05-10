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

class OrderConfirmedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private string $appName;
    private ?string $logoUrl;

    public function __construct(public Order $order)
    {
        $settings      = Setting::whereIn('key', ['site_name', 'logo'])->pluck('value', 'key');
        $this->appName = $settings->get('site_name') ?: config('app.name');
        $logoPath      = $settings->get('logo');
        $this->logoUrl = $logoPath ? asset('storage/' . $logoPath) : null;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmed – #' . str_pad($this->order->id, 5, '0', STR_PAD_LEFT),
        );
    }

    public function content(): Content
    {
        $order    = $this->order->loadMissing('items');
        $subtotal = $order->items->sum(fn ($i) => $i->price * $i->quantity);
        $shipping = round((float) $order->total - $subtotal, 2);

        return new Content(
            view: 'emails.order-confirmed',
            with: [
                'order'    => $order,
                'subtotal' => number_format($subtotal, 2),
                'shipping' => number_format($shipping, 2),
                'appName'  => $this->appName,
                'logoUrl'  => $this->logoUrl,
                'shopUrl'  => rtrim(config('app.url'), '/') . '/shop',
                'orderUrl' => rtrim(config('app.url'), '/') . '/orders/' . $order->id,
            ],
        );
    }
}
