<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointment Confirmed</title>
</head>
<body style="margin:0;padding:0;background:#f4f6fa;font-family:'Segoe UI',Helvetica,Arial,sans-serif;color:#2c2c2c;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fa;padding:32px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 4px 16px rgba(20,30,60,0.06);">

          {{-- Header --}}
          <tr>
            <td style="background:#1a2e4a;padding:28px 32px;text-align:left;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="font-size:20px;font-weight:700;color:#ffffff;letter-spacing:.3px;">
                    Medicare Hospital
                  </td>
                  <td align="right" style="font-size:11px;color:#b2e0f5;text-transform:uppercase;letter-spacing:1px;">
                    Appointment Confirmed
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Hero --}}
          <tr>
            <td style="padding:36px 32px 8px 32px;text-align:center;">
              <div style="display:inline-block;width:64px;height:64px;border-radius:50%;background:#e6f7f4;line-height:64px;font-size:30px;color:#0a8c6a;font-weight:700;">&#10003;</div>
              <h1 style="margin:18px 0 6px 0;font-size:22px;color:#1a2e4a;font-weight:700;">
                Your appointment is confirmed
              </h1>
              <p style="margin:0;font-size:14px;line-height:1.6;color:#5b6478;">
                Hello <strong>{{ $patient->name ?? 'Patient' }}</strong>, your appointment with
                <strong>Dr. {{ $doctor->name ?? 'our specialist' }}</strong> has been approved.
              </p>
            </td>
          </tr>

          {{-- Details card --}}
          <tr>
            <td style="padding:24px 32px 8px 32px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f7f9fc;border:1px solid #e7eaf1;border-radius:10px;">
                <tr>
                  <td style="padding:18px 22px;font-size:13px;color:#6b7385;text-transform:uppercase;letter-spacing:1px;border-bottom:1px solid #eaedf3;font-weight:600;">
                    Appointment Details
                  </td>
                </tr>
                <tr>
                  <td style="padding:14px 22px;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;color:#2c2c2c;">
                      <tr>
                        <td style="padding:6px 0;color:#6b7385;width:40%;">Patient</td>
                        <td style="padding:6px 0;font-weight:600;">{{ $patient->name ?? '—' }}</td>
                      </tr>
                      <tr>
                        <td style="padding:6px 0;color:#6b7385;">Doctor</td>
                        <td style="padding:6px 0;font-weight:600;">Dr. {{ $doctor->name ?? '—' }}</td>
                      </tr>
                      @if(!empty($doctor?->specialty))
                      <tr>
                        <td style="padding:6px 0;color:#6b7385;">Specialty</td>
                        <td style="padding:6px 0;">{{ $doctor->specialty }}</td>
                      </tr>
                      @endif
                      <tr>
                        <td style="padding:6px 0;color:#6b7385;">Date</td>
                        <td style="padding:6px 0;font-weight:600;">
                          {{ optional($appointment->appointment_date)->format('l, F j, Y') ?? '—' }}
                        </td>
                      </tr>
                      <tr>
                        <td style="padding:6px 0;color:#6b7385;">Time</td>
                        <td style="padding:6px 0;font-weight:600;">
                          {{ \Illuminate\Support\Carbon::parse($appointment->start_time)->format('g:i A') }}
                          &mdash;
                          {{ \Illuminate\Support\Carbon::parse($appointment->end_time)->format('g:i A') }}
                        </td>
                      </tr>
                      @if(!empty($appointment->treatment))
                      <tr>
                        <td style="padding:6px 0;color:#6b7385;">Treatment</td>
                        <td style="padding:6px 0;">{{ $appointment->treatment }}</td>
                      </tr>
                      @endif
                      <tr>
                        <td style="padding:6px 0;color:#6b7385;">Reference</td>
                        <td style="padding:6px 0;font-family:Consolas,monospace;font-size:13px;color:#1a2e4a;">
                          #{{ str_pad((string) $appointment->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Reminders --}}
          <tr>
            <td style="padding:18px 32px 4px 32px;">
              <p style="margin:0 0 8px 0;font-size:13px;font-weight:600;color:#1a2e4a;">Before your visit</p>
              <ul style="margin:0;padding-left:18px;font-size:13px;line-height:1.7;color:#5b6478;">
                <li>Please arrive 15 minutes before your scheduled time.</li>
                <li>Bring a valid ID and any previous medical records.</li>
                <li>If you need to reschedule, contact the hospital at least 24 hours in advance.</li>
              </ul>
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td style="padding:24px 32px 28px 32px;text-align:center;border-top:1px solid #eaedf3;margin-top:18px;">
              <p style="margin:0 0 4px 0;font-size:12px;color:#8b93a7;">
                Need help? Reply to this email or call us anytime.
              </p>
              <p style="margin:0;font-size:11px;color:#aab1c2;">
                &copy; {{ date('Y') }} Medicare Hospital. This email was sent because your appointment was confirmed.
              </p>
            </td>
          </tr>
        </table>

        <p style="margin:14px 0 0 0;font-size:11px;color:#aab1c2;">This is an automated message — please do not reply directly.</p>
      </td>
    </tr>
  </table>
</body>
</html>
