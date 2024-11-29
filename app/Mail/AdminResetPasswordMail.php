<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;


    public $staff;
    public $newPassword;

    public function __construct($staff, $newPassword)
    {
        $this->staff = $staff;
        $this->newPassword = $newPassword;
    }

    public function build()
    {
        return $this->view('emails.staff_reset_password')
            ->with([
                'name' => $this->staff->first_name,
                'email' => $this->staff->email,
                'password' => $this->newPassword,
            ]);
    }
}
