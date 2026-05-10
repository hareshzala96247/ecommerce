@extends('emails.layouts.base')

@section('title', 'Order Confirmed – #' . str_pad($order->id, 5, '0', STR_PAD_LEFT))

@section('preheader', 'Your order #' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . ' has been confirmed. Thank you, ' . $order->customer_name . '!')

@section('content')

  {{-- Icon --}}
  <div style="text-align:center;margin-bottom:24px;">
    <div style="display:inline-block;width:68px;height:68px;line-height:68px;font-size:30px;
                background:linear-gradient(135deg,#0D6EFD,#7C3AED);border-radius:50%;
                color:#fff;text-align:center;box-shadow:0 12px 32px rgba(13,110,253,0.35);">
      ✓
    </div>
  </div>

  <h2 style="margin:0 0 8px;font-size:22px;font-weight:800;color:#0F0F1A;text-align:center;">
    Order Confirmed!
  </h2>
  <p style="margin:0 0 32px;font-size:15px;color:#6b7280;text-align:center;line-height:1.7;">
    Thank you, <strong style="color:#0F0F1A;">{{ $order->customer_name }}</strong>!
    We've received your order and will start processing it shortly.
  </p>

  {{-- Order meta --}}
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
    style="background:#F9FAFB;border:1px solid #E5E7EB;border-radius:12px;margin-bottom:28px;">
    <tr>
      <td style="padding:16px 20px;border-right:1px solid #E5E7EB;width:50%;">
        <p style="margin:0 0 2px;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.08em;">Order Number</p>
        <p style="margin:0;font-size:16px;font-weight:800;color:#0F0F1A;">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
      </td>
      <td style="padding:16px 20px;width:50%;">
        <p style="margin:0 0 2px;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.08em;">Order Date</p>
        <p style="margin:0;font-size:14px;font-weight:600;color:#0F0F1A;">
          {{ $order->created_at->format('M j, Y') }}
        </p>
      </td>
    </tr>
  </table>

  {{-- Items --}}
  <p style="margin:0 0 12px;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.08em;">
    Items Ordered
  </p>
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:4px;">
    @foreach ($order->items as $item)
    <tr>
      <td style="padding:10px 0;border-bottom:1px solid #F3F4F6;vertical-align:top;">
        <p style="margin:0;font-size:14px;font-weight:600;color:#0F0F1A;">{{ $item->product_name }}</p>
        <p style="margin:2px 0 0;font-size:12px;color:#9ca3af;">
          ${{ number_format($item->price, 2) }} &times; {{ $item->quantity }}
        </p>
      </td>
      <td style="padding:10px 0;border-bottom:1px solid #F3F4F6;text-align:right;vertical-align:top;white-space:nowrap;">
        <p style="margin:0;font-size:14px;font-weight:700;color:#0F0F1A;">
          ${{ number_format($item->price * $item->quantity, 2) }}
        </p>
      </td>
    </tr>
    @endforeach
  </table>

  {{-- Totals --}}
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
    style="margin-bottom:28px;padding-top:4px;">
    <tr>
      <td style="padding:6px 0;font-size:13px;color:#6b7280;">Subtotal</td>
      <td style="padding:6px 0;font-size:13px;color:#0F0F1A;font-weight:500;text-align:right;">${{ $subtotal }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;font-size:13px;color:#6b7280;">Shipping</td>
      <td style="padding:6px 0;font-size:13px;text-align:right;font-weight:500;
                 color:{{ $shipping === '0.00' ? '#16a34a' : '#0F0F1A' }};">
        {{ $shipping === '0.00' ? 'Free' : '$' . $shipping }}
      </td>
    </tr>
    <tr style="border-top:2px solid #E5E7EB;">
      <td style="padding:10px 0 0;font-size:15px;font-weight:800;color:#0F0F1A;">Total</td>
      <td style="padding:10px 0 0;font-size:16px;font-weight:800;color:#0D6EFD;text-align:right;">
        ${{ number_format($order->total, 2) }}
      </td>
    </tr>
  </table>

  {{-- Shipping address --}}
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
    style="background:#F9FAFB;border:1px solid #E5E7EB;border-radius:12px;margin-bottom:32px;">
    <tr>
      <td style="padding:16px 20px;">
        <p style="margin:0 0 8px;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.08em;">
          Shipping To
        </p>
        <p style="margin:0;font-size:14px;color:#374151;line-height:1.7;">
          <strong style="color:#0F0F1A;">{{ $order->customer_name }}</strong><br>
          {{ $order->address }}<br>
          {{ $order->city }}@if($order->state), {{ $order->state }}@endif {{ $order->zip }}<br>
          {{ $order->country }}
          @if($order->phone)<br><span style="color:#9ca3af;">{{ $order->phone }}</span>@endif
        </p>
      </td>
    </tr>
  </table>

  {{-- CTA --}}
  <div style="text-align:center;">
    <a href="{{ $orderUrl }}"
      style="display:inline-block;background:linear-gradient(135deg,#0D6EFD,#7C3AED);
             color:#ffffff;font-weight:700;font-size:15px;
             padding:14px 44px;border-radius:50px;text-decoration:none;
             box-shadow:0 8px 24px rgba(13,110,253,0.4);">
      Track My Order &rarr;
    </a>
  </div>

@endsection

@section('footer_note')
  You received this because you placed an order at
  <strong style="color:#6b7280;">{{ $appName }}</strong>.<br>
  Questions? Reply to this email or contact our support team.
@endsection
