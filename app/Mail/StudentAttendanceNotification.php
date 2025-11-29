<?php

namespace App\Mail;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentAttendanceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Attendance $attendance;

    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance->loadMissing('student.schoolClass.batch');
    }

    public function build()
    {
        $student   = $this->attendance->student;
        $class     = $student->schoolClass;
        $batch     = $class->batch;
        $status    = strtoupper($this->attendance->status);
        $remark    = $this->attendance->remark;

        return $this->subject('Informasi Kehadiran Siswa - SIMKESA')
            ->view('emails.attendance_notification', [
                'attendance' => $this->attendance,
                'student'    => $student,
                'class'      => $class,
                'batch'      => $batch,
                'status'     => $status,
                'remark'     => $remark,
            ]);
    }
}
