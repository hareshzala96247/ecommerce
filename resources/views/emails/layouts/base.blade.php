<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', $appName ?? config('app.name'))</title>
</head>
<body style="margin:0;padding:0;background:#F3F4F6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">

  {{-- Hidden preheader text shown as email preview in inbox --}}
  @hasSection('preheader')
  <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;color:#F3F4F6;">
    @yield('preheader')
    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
  </div>
  @endif

  <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
    style="background:#F3F4F6;padding:48px 16px;">
    <tr>
      <td align="center">
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
          style="max-width:560px;">

          {{-- ── Header ── --}}
          <tr>
            <td style="background:linear-gradient(135deg,#0D6EFD 0%,#7C3AED 100%);
                       border-radius:20px 20px 0 0;padding:36px 48px;text-align:center;">
              @if (!empty($logoUrl))
                <a href="{{ $shopUrl ?? '#' }}" style="display:inline-block;">
                  <img src="{{ $logoUrl }}"
                    alt="{{ $appName ?? config('app.name') }}"
                    style="max-height:56px;max-width:200px;width:auto;height:auto;display:block;margin:0 auto;">
                </a>
              @else
                <a href="{{ $shopUrl ?? '#' }}" style="text-decoration:none;">
                  <p style="margin:0 0 4px;font-size:34px;line-height:1;">🛍️</p>
                  <span style="color:#ffffff;font-size:22px;font-weight:800;letter-spacing:-0.3px;">
                    {{ $appName ?? config('app.name') }}
                  </span>
                </a>
              @endif
            </td>
          </tr>

          {{-- ── Body / Content ── --}}
          <tr>
            <td style="background:#ffffff;padding:48px;
                       border-left:1px solid #E5E7EB;border-right:1px solid #E5E7EB;">
              @yield('content')
            </td>
          </tr>

          {{-- ── Footer ── --}}
          <tr>
            <td style="background:#F9FAFB;border:1px solid #E5E7EB;border-top:none;
                       border-radius:0 0 20px 20px;padding:24px 48px;text-align:center;">
              <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;line-height:1.8;">
                @yield('footer_note',
                  'You received this email from <strong style="color:#6b7280;">' .
                  e($appName ?? config('app.name')) .
                  '</strong>.')
              </p>
              <p style="margin:0;font-size:11px;color:#d1d5db;">
                &copy; {{ date('Y') }} {{ $appName ?? config('app.name') }}. All rights reserved.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
