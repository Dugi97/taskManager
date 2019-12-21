<?php


namespace App\Service;


use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class MailService
{
    public static function sendMail($from, $sendTo, $subject, $html, $password)
    {
        $email = (new Email())
            ->from($from)
            ->to($sendTo)
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->html($html)
            ->html($html);

        $transport = new GmailSmtpTransport($from, $password);
        $mailer = new Mailer($transport);
        $mailer->send($email);

        return true;
    }
}