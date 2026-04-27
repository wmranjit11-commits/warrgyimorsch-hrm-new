<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalarySlipMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $employee, $total, $payrolls;

    public function __construct($employee, $total, $payrolls)
    {
        $this->employee = $employee;
        $this->total = $total;
        $this->payrolls = $payrolls;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Salary Slip Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'payroll.simple_mail',
            with: [
                'employee' => $this->employee,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdf = \PDF::loadView('payroll.bulk_payslip_pdf', [
            'payrolls' => $this->payrolls
        ]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'salary_slip.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
