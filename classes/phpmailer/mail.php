<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/Exception.php';
require __DIR__.'/PHPMailer.php';
require __DIR__.'/SMTP.php';

class Mail extends PHPMailer
{
    // Set default variables for all new objects
    public $From     = 'email@email.pl';
    public $FromName = SITETITLE;
    public $Host = 'smtp.mailtrap.io';
    public $Mailer = 'smtp';
    public $SMTPAuth = true;
    public $Username = '30b788edfcbc3f';
    public $Password = 'b34435a6b4e3fc';
    //public $SMTPSecure = 'tls';
    public $WordWrap = 75;

    public function subject($subject)
    {
        $this->Subject = $subject;
    }

    public function body($body)
    {
        $this->Body = $body;
    }

    public function send()
    {
        $this->AltBody = strip_tags(stripslashes($this->Body))."\n\n";
        $this->AltBody = str_replace("&nbsp;", "\n\n", $this->AltBody);
        return parent::send();
    }
}
