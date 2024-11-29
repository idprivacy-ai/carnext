<?php
namespace App\Mail;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Dealer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeadNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $vehicle;
    public $dealer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $user=null, $vehicle = null, $dealer = null)
    {
        $this->user = $user;
        $this->vehicle = $vehicle;
        $this->dealer = $dealer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.leadalert')
                    ->with([
                        'user' => $this->user,
                        'vehicle' => $this->vehicle,
                        'dealer' => $this->dealer
                    ]);
    }
}

