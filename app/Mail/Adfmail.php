<?php 
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Adfmail extends Mailable
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
        return $this->markdown('emails.adfmail')
                    ->with([
                        'user' => $this->user,
                        'vehicle' => $this->vehicle,
                        'dealer' => $this->dealer
                    ]);
    }
}