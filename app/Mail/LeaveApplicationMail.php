<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;
    public $employee;

    public function __construct($leave, $employee)
    {
        $this->leave = $leave;
        $this->employee = $employee;
    }

    public function build()
    {
        return $this->subject('New Leave Application')
                    ->view('leave.leave_mail');
    }
}