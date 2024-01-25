<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;


class WalletRechargeInvoiceTemplate extends Mailable
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
   
  $this->view('emails.recharge-success')
          ->subject("test Wallet recharge invoice ".$this->record->id)
          ->with(['data'=>$this->record]);
 
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
