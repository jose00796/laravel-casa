<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ValidateUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $subject = 'Validar Usuario';
    public $user_name;
    public $api_token;
    
    public function __construct($user_name, $api_token)
    {
        $this->user_name = $user_name;
        $this->api_token = $api_token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ValidateUser')->with('user_name', $this->user_name, 'api_token', $this->api_token);
    }
}
