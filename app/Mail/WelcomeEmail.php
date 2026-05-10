<?php

namespace App\Mail;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    private string $appName;
    private ?string $logoUrl;

    public function __construct(public User $user)
    {
        $settings      = Setting::whereIn('key', ['site_name', 'logo'])->pluck('value', 'key');
        $this->appName = $settings->get('site_name') ?: config('app.name');
        $logoPath      = $settings->get('logo');
        $this->logoUrl = $logoPath ? asset('storage/' . $logoPath) : null;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . $this->appName . '!',
        );
    }

    public function content(): Content
    {
        $appName = $this->appName;
        $logoUrl = $this->logoUrl;

        return new Content(
            view: 'emails.welcome',
            with: [
                'userName' => $this->user->name,
                'appName'  => $appName,
                'logoUrl'  => $logoUrl,
                'shopUrl'  => rtrim(config('app.url'), '/') . '/shop',
                'features' => [
                    ['icon' => '🛍️', 'text' => 'Browse our full product catalog'],
                    ['icon' => '❤️',  'text' => 'Save favourites to your wishlist'],
                    ['icon' => '📦', 'text' => 'Track your orders in real time'],
                    ['icon' => '🔒', 'text' => 'Shop securely with encrypted checkout'],
                ],
            ],
        );
    }
}
