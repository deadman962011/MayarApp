<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustRestPassMail extends Mailable
{
    use Queueable, SerializesModels;

    public $RestToken;
    public $RestUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($RestToken,$RestUrl)
    {
        //
        $this->RestToken=$RestToken;
        $this->RestUrl=$RestUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('includes.RestPassMail')
        ->from('deadman1002014@gmail.com');
    }
}
