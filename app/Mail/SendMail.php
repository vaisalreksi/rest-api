<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($req)
    {
        $this->data = $req;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        // $verification_code = str_random(30); //Generate verification code

        // return $this->from('admin@genesys.com')
        //         ->view('mail.mail', array('code' => $verification_code))
        //         ->with(array('data'=>$this->data));
    }
}
