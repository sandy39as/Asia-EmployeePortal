<?php

namespace App\Mail;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KabagLeaveRequestSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public LeaveRequest $leaveRequest,
        public Employee $employee,
        public User $kabag
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject:
                'Pengajuan Baru Menunggu Persetujuan - '
                . ($this->employee->nama ?? 'Karyawan')
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.kabag-leave-request-submitted'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
