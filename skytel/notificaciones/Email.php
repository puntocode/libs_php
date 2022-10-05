<?php
namespace skytel\notificaciones;

/**
 * [LIBS_PHP] , Skytel. All Rights Reserved. [2013].
 *
 * @author Endrigo Rivas <endrigo.rivas@gmail.com>
 * 
 * $Id: Email.php 19730 2016-06-20 18:57:03Z paulo.arza $
 */

require_once dirname(__FILE__) . '/../../phpmailer/class.phpmailer.php';

/**
 * Description of Email
 *
 * @author Endrigo
 */
class Email {

    private $errorMsg;
    private $ip;
    private $fromSignature;
    private $user;
    private $passw;

    /**
     * Constructor
     * 
     * @param string $ip ip SMTP server
     * @param string $user
     * @param string $passw
     */
    public function __construct($ip, $fromSignature = "", $user = "", $passw = "") {
        $this->ip = $ip;
        $this->fromSignature = $fromSignature;
        $this->user = $user;
        $this->passw = $passw;
    }

    /**
     * Envía mensaje a 1 destinatario de email
     * 
     * @param type $to
     * @param type $subject
     * @param type $message
     * @param type $fromName
     * @param type $contentType "text/plain" or "text/html"
     * @return boolean true/false
     */
    public function send($to, $subject, $message, $fromName = "", $contentType = "text/html") {
        
        $usuariox = "no-reply@post-skytel.com";
        $passwordx = "95@clLVbLcATq";

        $this->errorMsg = "";

        if ($to == "") {
            $this->errorMsg = "Empty TO field";
            return false;
        }

        if ($message == "") {
            $this->errorMsg = "Empty MESSAGE field";
            return false;
        }

        $to = trim($to);
        $message = trim($message);
        $fromName = trim($fromName);

        $phpMailer = new \PHPMailer();
        $phpMailer->IsSMTP(); // telling the class to use SMTP
        $phpMailer->Host = $this->ip; // SMTP server
        $phpMailer->ContentType = $contentType;
        $phpMailer->From = $this->fromSignature;
        
        $phpMailer->Username = $usuariox;        
        $phpMailer->Password = $passwordx;
        
        $phpMailer->FromName = $fromName;
        $phpMailer->AddAddress($to);

        $phpMailer->Subject = $subject;
        $phpMailer->Body = $message;

        $phpMailer->CharSet = 'UTF-8'; //Codificacción utf-8

        if (!$phpMailer->Send()) {
            $this->errorMsg = $phpMailer->ErrorInfo;
            return false;
        } else {
            return true;
        }
    }

    /**
     * 
     * @return type
     */
    public function getErrorMsg() {
        return $this->errorMsg;
    }

}
