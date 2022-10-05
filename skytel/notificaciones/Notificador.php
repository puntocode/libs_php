<?php
namespace skytel\notificaciones;

/**
 * [LIBS_PHP] , Skytel. All Rights Reserved. [2013].
 *
 * @author Endrigo Rivas <endrigo.rivas@gmail.com>
 * 
 * $Id: Notificador.php 19730 2016-06-20 18:57:03Z paulo.arza $
 */
?>
<?php

require_once dirname(__FILE__) . '/config/config.php';

/**
 * Unicamente notifica. No guarda en base de datos.
 */
class Notificador {

    private $errorMsg;

    /**
     * Esta clase es un Wrapper para los distintos tipos de notificación:
     * 
     * 1 - SMS
     * 2 - Beeper
     * 3 - Email
     */
    public function __construct() {

        if (!defined("libs_PARAGUAY"))
            throw new UnexpectedValueException("Undefined libs_PARAGUAY");
        if (!defined("libs_ARGENTINA"))
            throw new UnexpectedValueException("Undefined libs_ARGENTINA");
        if (!defined("libs_SMTP_HOST_SKYTEL"))
            throw new UnexpectedValueException("Undefined libs_SMTP_HOST_SKYTEL");
        if (!defined("libs_SMTP_FROM_SKYTEL"))
            throw new UnexpectedValueException("Undefined libs_SMTP_FROM_SKYTEL");
        if (!defined("libs_GATEWAY_BEEPER"))
            throw new UnexpectedValueException("Undefined libs_GATEWAY_BEEPER");

        date_default_timezone_set('America/Asuncion');
        $this->errorMsg = "";
    }

    /**
     * 
     * @param type $numero
     * @param type $mensaje
     * @param type $pais
     * @return type
     */
    public function SMS($numero, $mensaje, $pais) {

        $sms = new SMS($numero, $mensaje, $pais);
        $ok = $sms->enviar();
        if (!$ok) {
            $this->errorMsg = $sms->getErrorMsg();
            return false;
        } else {
            return true;
        }
    }

    /**
     * 
     * @param String $to
     * @param String $subject
     * @param String $message
     * @param String $fromName
     * @param String $contentType text/plain or text/html
     * @return boolean true/false
     */
    public function email($to, $subject, $message, $fromName, $contentType)
    {
                
        $usuariox = "no-reply@post-skytel.com";
        $passwordx = "95@clLVbLcATq";

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
//        $phpMailer->SMTPDebug = 2; // will echo errors and messages
        $phpMailer->isSMTP(); // telling the class to use SMTP
        $phpMailer->Host = libs_SMTP_HOST_SKYTEL; // SMTP server
        $phpMailer->ContentType = $contentType;

        $phpMailer->CharSet = 'UTF-8';//Codificacción utf-8
                
        $phpMailer->From = libs_SMTP_FROM_SKYTEL;
               
        $phpMailer->Username = $usuariox;        
        $phpMailer->Password = $passwordx;                
        
        $phpMailer->FromName = $fromName;
        $phpMailer->AddAddress($to);

        $phpMailer->Subject = $subject;
        $phpMailer->Body = $message;
        
        if (!$phpMailer->Send()) {
            $this->errorMsg = $phpMailer->ErrorInfo;
            return false;
        } else {
            return true;
        }
    }

    /**
     * 
     * @param type $pin
     * @param type $message
     * @return boolean
     */
    public function beeper($pin, $message) {

        if ($pin == "") {
            $this->errorMsg = "Empty PIN field";
            return false;
        }

        $beeper = new Beeper(libs_GATEWAY_BEEPER);
        $ok = $beeper->enviarMensaje($pin, $message);
        if (!$ok) {
            $this->errorMsg = $beeper->getErrorMsg();
            return false;
        } else {
            return true;
        }
    }

    /**
     * 
     * @return String Mensaje del error
     */
    public function getErrorMsg() {
        return $this->errorMsg;
    }

}

?>
