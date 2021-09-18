<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetAppUserPassword extends Mailable
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
        return $this->subject('【シェアシテ】パスワード再設定の申請')
            ->view('templates.japan.forgot_app_user_password')
            ->with($this->data);
    }
}
