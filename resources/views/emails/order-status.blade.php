@extends('emails.layouts.base')

@section('title', 'Order ' . ucfirst($order->status) . ' – #' . str_pad($order->id, 5, '0', STR_PAD_LEFT))

@section('preheader', $content['heading'] . ' – Order #' . str_pad($order->id, 5, '0', STR_PAD_LEFT))

@section('content')

  {{-- Status icon --}}
  <div style="text-align:center;margin-bottom:24px;">
    <div style="display:inline-block;width:72px;height:72px;line-height:72px;font-size:34px;
                border-radius:50%;text-align:center;
                background:{{ $content['bg'] }};border:2px solid {{ $content['color'] }}20;">
      {{ $content['icon'] }}
    </div>
  </div>

  <h2 style="margin:0 0 10px;font-size:22px;font-weight:800;color:#0F0F1A;text-align:center;">
    {{ $content['heading'] }}
  </h2>
  <p style="margin:0 0 32px;font-size:15px;color:#6b7280;text-align:center;line-height:1.7;">
    {{ $content['message'] }}
  </p>

  {{-- Order summary card --}}
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
    style="background:#F9FAFB;border:1px solid #E5E7EB;border-radius:12px;margin-bottom:32px;">
    <tr>
      <td style="padding:20px 24px;">

        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
          <tr>
            <td style="width:50%;vertical-align:top;padding-bottom:12px;">
              <p style="margin:0 0 2px;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.08em;">Order Number</p>
              <p style="margin:0;font-size:16px;font-weight:800;color:#0F0F1A;">
                #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
              </p>
            </td>
            <td style="width:50%;vertical-align:top;padding-bottom:12px;text-align:right;">
              <p style="margin:0 0 4px;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.08em;">Status</p>
              <span style="display:inline-block;padding:4px 12px;border-radius:50px;font-size:12px;font-weight:700;
                           background:{{ $content['bg'] }};color:{{ $content['color'] }};">
                {{ ucfirst($order->status) }}
              </span>
            </td>
          </tr>
          <tr>
            <td colspan="2" style="border-top:1px solid #E5E7EB;padding-top:12px;">
              <p style="margin:0 0 2px;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.08em;">Customer</p>
              <p style="margin:0;font-size:14px;font-weight:600;color:#0F0F1A;">{{ $order->customer_name }}</p>
              <p style="margin:2px 0 0;font-size:13px;color:#6b7280;">{{ $order->customer_email }}</p>
            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>

  {{-- CTA (not shown for cancelled) --}}
  @if ($order->status !== 'cancelled')
  <div style="text-align:center;">
    <a href="{{ $orderUrl }}"
      style="display:inline-block;background:linear-gradient(135deg,#0D6EFD,#7C3AED);
             color:#ffffff;font-weight:700;font-size:15px;
             padding:14px 44px;border-radius:50px;text-decoration:none;
             box-shadow:0 8px 24px rgba(13,110,253,0.4);">
      View My Order &rarr;
    </a>
  </div>
  @else
  <div style="text-align:center;">
    <a href="{{ $shopUrl }}"
      style="display:inline-block;border:2px solid #E5E7EB;
             color:#374151;font-weight:700;font-size:15px;
             padding:13px 44px;border-radius:50px;text-decoration:none;">
      Continue Shopping
    </a>
  </div>
  @endif

@endsection

@section('footer_note')
  You received this update for order
  <strong style="color:#6b7280;">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</strong>
  placed at <strong style="color:#6b7280;">{{ $appName }}</strong>.<br>
  Questions? Reply to this email or contact our support team.
@endsection
