<?php
namespace App\Jobs;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Dealer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeadNotificationMail;
use App\Mail\Adfmail;

class SendLeadNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $vehicle;
    protected $dealer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $user =NULL, $vehicle = null, $dealer )
    {
        $this->user = $user;
        $this->vehicle = $vehicle;
        $this->dealer = $dealer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->dealer['email'])->send(new LeadNotificationMail($this->user, $this->vehicle, $this->dealer));
        if(isset($this->dealer['adf_mail']) && !empty($this->dealer['adf_mail']))
            Mail::to($this->dealer['adf_mail'])->send(new Adfmail($this->user, $this->vehicle, $this->dealer));

            //Mail::to($this->dealer['email'])->send(new LeadNotificationMail($this->user, $this->vehicle, $this->dealer));
    }
}
