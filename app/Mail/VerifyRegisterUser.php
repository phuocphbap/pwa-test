<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyRegisterUser extends Mailable
{
    use Queueable;
    use SerializesModels;
    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('【シェアシテ】メールアドレスを認証して利用を開始')
            ->view('templates.japan.verify_register_user')
            ->with($this->data);
    }
}
