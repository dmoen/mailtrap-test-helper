<?php

use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    const HOST = "smtp.mailtrap.io";

    const PORT = 25;

    const USERNAME = "3cd907b9fcf846";

    const PASSWORD = "98fd1c3715512d";
    
    private $mailer;

    public function __construct($html = true)
    {
        $this->mailer = new PHPMailer(true);

        //Server settings
        $this->mailer->SMTPDebug = 2;
        $this->mailer->isSMTP();
        $this->mailer->Host = self::HOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = self::USERNAME;
        $this->mailer->Password = self::PASSWORD;
        $this->mailer->Port = self::PORT;
        $this->mailer->isHTML($html);
    }

    public function from($address, $name)
    {
        $this->mailer->setFrom($address, $name);

        return $this;
    }

    public function to($address)
    {
        $this->mailer->addAddress($address);

        return $this;
    }

    public function subject($subject)
    {
        $this->mailer->Subject = $subject;

        return $this;
    }

    public function body($body)
    {
        $this->mailer->Body = $body;

        return $this;
    }

    public function send()
    {
        $this->mailer->send();
    }
}