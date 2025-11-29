<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Informasi Kehadiran Siswa - SIMKESA</title>
  <style>
    /* Fallback styles for clients that support <style>; keep critical styles inline below */
    @media (prefers-color-scheme: dark) {
      .card { background-color:#1f2937 !important; }
      .text-muted { color:#c7c7c7 !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f5f7fb;padding:24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px rgba(16,24,40,0.08);" class="card">
          <tr>
            <td style="background:#2a6ad1;color:#ffffff;padding:20px 24px;">
              <h1 style="margin:0;font-size:20px;line-height:28px;">SIMKESA • Informasi Kehadiran</h1>
              <div style="margin-top:4px;font-size:12px;opacity:.9;">Notifikasi otomatis dari sistem</div>
            </td>
          </tr>
          <tr>
            <td style="padding:24px;">
              <p style="margin:0 0 12px 0;font-size:14px;line-height:22px;">Yth. Orang Tua/Wali dari <strong>{{ $student->full_name }}</strong>,</p>
              <p style="margin:0 0 20px 0;font-size:14px;line-height:22px;">Berikut adalah informasi kehadiran siswa pada hari ini:</p>

              <!-- Info Card -->
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px solid #eef2f7;border-radius:10px;overflow:hidden;">
                <tr>
                  <td style="padding:16px 20px;background:#f9fbff;border-bottom:1px solid #eef2f7;">
                    <strong style="font-size:14px;">Detail Kehadiran</strong>
                  </td>
                </tr>
                <tr>
                  <td style="padding:16px 20px;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                      <tr>
                        <td width="140" style="padding:6px 0;color:#64748b;font-size:13px;">Tanggal</td>
                        <td style="padding:6px 0;font-size:13px;"><strong>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') }}</strong></td>
                      </tr>
                      <tr>
                        <td width="140" style="padding:6px 0;color:#64748b;font-size:13px;">Kelas</td>
                        <td style="padding:6px 0;font-size:13px;"><strong>{{ $batch->year }} - {{ $class->name }}</strong></td>
                      </tr>
                      <tr>
                        <td width="140" style="padding:6px 0;color:#64748b;font-size:13px;">Status</td>
                        <td style="padding:6px 0;font-size:13px;">
                          <span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#eef2ff;color:#1d4ed8;font-weight:600;font-size:12px;letter-spacing:.2px;">
                            @if (strtoupper($status) === 'H')
                                Hadir
                            @elseif (strtoupper($status) === 'I')
                                Izin
                            @elseif (strtoupper($status) === 'A')
                                Alpha
                            @elseif (strtoupper($status) === 'S')
                                Sakit
                            @endif
                          </span>
                        </td>
                      </tr>
                      @if($remark)
                      <tr>
                        <td width="140" style="padding:6px 0;color:#64748b;font-size:13px;">Keterangan</td>
                        <td style="padding:6px 0;font-size:13px;">{{ $remark }}</td>
                      </tr>
                      @endif
                    </table>
                  </td>
                </tr>
              </table>

              <!-- Callout -->
              <div style="margin-top:18px;padding:12px 14px;border:1px dashed #d6e0f5;border-radius:8px;background:#fbfdff;color:#334155;font-size:12px;line-height:18px;">
                Mohon tidak membalas email ini. Untuk pertanyaan lebih lanjut, silakan hubungi wali kelas atau pihak sekolah.
              </div>

              <!-- CTA (optional) -->
              <div style="text-align:center;margin-top:22px;">
                <a href="{{ url('/') }}" style="display:inline-block;background:#2a6ad1;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:8px;font-size:13px;font-weight:600;">Buka SIMKESA</a>
              </div>

              <p style="margin:22px 0 0 0;color:#6b7280;font-size:12px;line-height:18px;">Email ini dikirim secara otomatis oleh sistem SIMKESA.</p>
            </td>
          </tr>
          <tr>
            <td style="padding:14px 24px;background:#f8fafc;color:#94a3b8;font-size:11px;text-align:center;">
              © {{ date('Y') }} SIMKESA. Semua hak dilindungi.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
