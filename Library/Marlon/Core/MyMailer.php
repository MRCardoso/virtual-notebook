<?php

namespace Library\Marlon\Core;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require ROOT.DS.'vendor'.DS.'autoload.php';

class MyMailer
{
    private $mailer = NULL;

    public function __construct()
    {
        $config = $GLOBALS['mailConfig'];
        
        $this->mailer = new PHPMailer(true);
        //Server settings
        $this->mailer->SMTPDebug = $config['debug'];
        $this->mailer->isSMTP();
        $this->mailer->SMTPAuth = true;
        $this->mailer->Host = $config['host'];
        $this->mailer->Username = $config['username'];
        $this->mailer->Password = $config['password'];
        $this->mailer->SMTPSecure = $config['encryption'];
        $this->mailer->Port = $config['port'];
    }

    public function from($fromEmail, $fromName)
    {
        $this->mailer->setFrom($fromEmail, $fromName);
        return $this;
    }

    public function to($toEmail, $toName)
    {
        $this->mailer->addAddress($toEmail, $toName);
        return $this;
    }

    public function subject($message)
    {
        $this->mailer->Subject = utf8_decode($message);
        return $this;
    }
    public function body($message,$title)
    {
        ob_start();
        require ROOT.DS.'Library'.DS.'Marlon'.DS.'html'.DS.'email'.DS.'body.html';
        $content = ob_get_clean();
        
        $this->mailer->Body = str_replace(['{content}','{title}'], [utf8_decode($message),utf8_decode($title)], $content);
        return $this;
    }

    public function altBody($message)
    {
        $this->mailer->AltBody = $message;
        return $this;
    }
    
    public function isHtml($isHtml = TRUE)
    {
        $this->mailer->isHTML($isHtml);
        return $this;
    }

    public function send()
    {
        try {
            $this->mailer->send();
            return TRUE;
        } catch (Exception $e) {
            return $this->mailer->ErrorInfo;
        }
    }
}