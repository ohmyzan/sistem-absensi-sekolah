<?php

namespace App\Mail;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttendanceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $attendance;

    // Menerima data absensi saat class ini dipanggil
    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pemberitahuan Kehadiran Siswa - ' . $this->attendance->attendance_date,
        );
    }

    public function content(): Content
    {
        return new Content(
            // Kita akan membuat file view ini di langkah berikutnya
            view: 'emails.attendance-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
