<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EmailService
{
    public static function sendMail($toEmail, $subject, $view, $data = [])
    {
        Mail::send($view, $data, function ($mail) use ($toEmail, $subject) {
            $mail->to($toEmail)
                ->subject($subject)
                ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }
}
