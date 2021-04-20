<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustActivateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $ActivationUrl;



    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code,$ActivationUrl)
    {
        //
        $this->code=$code;
        $this->ActivationUrl=$ActivationUrl;
       
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {


               
        return $this->view('includes.ActivationMail')
        ->from('deadman1002014@gmail.com');
        
    }
}
