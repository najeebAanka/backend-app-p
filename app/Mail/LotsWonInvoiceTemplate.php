<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;


class LotsWonInvoiceTemplate extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
        public $record;
    public function __construct($record)
    {
        //
          $this->record = $record;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
   
   $this->view('emails.lots-won')
          ->subject("test.com | Invoice ".$this->record->gen_id)
          ->with(['data'=> json_decode($this->record->contents)]);
 
    $this->withSymfonyMessage(function (Email $message) {
        $message->getHeaders()->addTextHeader(
            'MIME-Version', '1.0' ,
            'Organization', 'test.com' ,
            'X-Priority', '3' ,
            'X-Mailer', "PHP". phpversion()  ,
            'Content-type', 'text/html; charset=iso 8859-1' ,
            'From', env('MAIL_FROM_ADDRESS') ,
            'Reply-To', env('MAIL_FROM_ADDRESS') ,
        );
    });
 
    return $this;
  
    }
}
