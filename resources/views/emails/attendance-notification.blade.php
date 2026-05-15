<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f5;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .title {
            color: #2563eb;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        .content {
            color: #374151;
            font-size: 16px;
            line-height: 1.6;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
            overflow: hidden;
        }

        .data-table th,
        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        .data-table th {
            color: #6b7280;
            width: 35%;
            font-weight: normal;
        }

        .data-table td {
            font-weight: bold;
            color: #111827;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: #9ca3af;
        }

        .status-badge {
            background-color: #d1fae5;
            color: #065f46;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 class="title">HadirAI SMK Jakarta 1</h1>
        </div>
        <div class="content">
            <p>Halo, Orang Tua / Wali Murid,</p>
            <p>Bersama email ini, kami memberitahukan bahwa ananda telah tiba di sekolah dan melakukan absensi otomatis
                menggunakan sistem deteksi wajah (*Face Recognition*).</p>

            <table class="data-table">
                <tr>
                    <th>Nama Siswa</th>
                    <td>{{ $attendance->student->user->name }}</td>
                </tr>
                <tr>
                    <th>NIS</th>
                    <td>{{ $attendance->student->nis }}</td>
                </tr>
                <tr>
                    <th>Kelas</th>
                    <td>{{ $attendance->student->class }}</td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->translatedFormat('l, d F Y') }}</td>
                </tr>
                <tr>
                    <th>Waktu Masuk</th>
                    <td>{{ $attendance->check_in }} WIB</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><span class="status-badge">✓ Hadir (Face Scan Valid)</span></td>
                </tr>
            </table>
        </div>
        <div class="footer">
            <p>Sistem Absensi Otomatis - SMK Jakarta 1</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas pesan ini.</p>
        </div>
    </div>
</body>

</html>
