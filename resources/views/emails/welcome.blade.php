@extends('emails.layouts.base')

@section('title', 'Welcome to ' . $appName)

@section('preheader', 'Welcome, ' . $userName . '! Your account is ready — start shopping now.')

@section('content')

  {{-- Checkmark icon --}}
  <div style="text-align:center;margin-bottom:28px;">
    <div style="display:inline-block;width:68px;height:68px;line-height:68px;font-size:30px;
                background:linear-gradient(135deg,#0D6EFD,#7C3AED);border-radius:50%;
                color:#fff;text-align:center;
                box-shadow:0 12px 32px rgba(13,110,253,0.35);">
      &#10003;
    </div>
  </div>

  <h2 style="margin:0 0 10px;font-size:22px;font-weight:800;color:#0F0F1A;text-align:center;">
    Welcome, {{ $userName }}!
  </h2>
  <p style="margin:0 0 36px;font-size:15px;color:#6b7280;text-align:center;line-height:1.7;">
    Your account is ready. Start discovering amazing products and enjoy a seamless shopping experience.
  </p>

  {{-- Divider --}}
  <hr style="border:none;border-top:1px solid #F3F4F6;margin:0 0 32px;">

  {{-- What's next --}}
  <p style="margin:0 0 16px;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.1em;">
    What you can do
  </p>

  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:36px;">
    @foreach ($features as $f)
    <tr>
      <td style="padding:10px 0;border-bottom:1px solid #F9FAFB;vertical-align:middle;">
        <span style="font-size:18px;margin-right:14px;vertical-align:middle;">{{ $f['icon'] }}</span>
        <span style="font-size:14px;color:#374151;font-weight:500;vertical-align:middle;">{{ $f['text'] }}</span>
      </td>
    </tr>
    @endforeach
  </table>

  {{-- CTA --}}
  <div style="text-align:center;">
    <a href="{{ $shopUrl }}"
      style="display:inline-block;background:linear-gradient(135deg,#0D6EFD,#7C3AED);
             color:#ffffff;font-weight:700;font-size:15px;
             padding:14px 44px;border-radius:50px;text-decoration:none;
             box-shadow:0 8px 24px rgba(13,110,253,0.4);letter-spacing:0.01em;">
      Start Shopping &rarr;
    </a>
  </div>

@endsection

@section('footer_note')
  You received this email because you just created an account at
  <strong style="color:#6b7280;">{{ $appName }}</strong>.<br>
  If this wasn't you, you can safely ignore this email.
@endsection
