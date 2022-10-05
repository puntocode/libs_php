<?php

namespace skytel\notificaciones;

/**
 * [LIBS_PHP] , Skytel. All Rights Reserved. [2015].
 *
 * @author Endrigo Rivas <endrigo.rivas@gmail.com>
 * 
 * $Id: Notificador.php 8332 2014-06-30 21:18:35Z endrigo $
 */
?>
<?php

require_once dirname(__FILE__) . '/config/config.php';
if(!class_exists('nusoap_base')) {require_once dirname(__FILE__) . '/../../nusoap/nusoap.php';}
if(!class_exists('nusoap_wsdlcache')) {require_once dirname(__FILE__) . '/../../nusoap/class.wsdlcache.php';}
//require_once dirname(__FILE__) . '/../../nusoap/nusoap.php';
//require_once dirname(__FILE__) . '/../../nusoap/class.wsdlcache.php';

class NotificadorWS {

    private $errorMsg;
    private $timeZone; //Default 'America/Asuncion'.
    private $pathCache; //Is different in Windows and in Linux.

    /**
     * Constructor
     * 
     * @throws UnexpectedValueException
     */

    public function __construct() {

        if (!defined("libs_URL_WS"))
            throw new UnexpectedValueException("Undefined libs_URL_WS");

        $this->errorMsg = "";

        $this->timeZone = 'America/Asuncion';
        date_default_timezone_set($this->timeZone);

        $this->pathCache = "/tmp";
        $PATH_WINDOWS = "C:\\temp";
        
        /**
         * Is running on Windows
         */
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            
            if (!file_exists($PATH_WINDOWS)) {
                mkdir($PATH_WINDOWS);
            }

            $this->pathCache = $PATH_WINDOWS;
        }
    }

    /**
     * 
     * @param type $timeZone
     */
    public function setTimezone($timeZone) {

        //Validation
        if (!in_array($timeZone, DateTimeZone::listIdentifiers())) {
            throw new UnexpectedValueException("Invalid timeZone '$timeZone'");
        }

        $this->timeZone = $timeZone;
        date_default_timezone_set($this->timeZone);
    }

    /**
     * 
     * @param Int $cliente
     * @param String $destinatarios JSON. SMS, EMAIL, Beeper
     * @param String $asunto
     * @param String $mensaje
     * @param Int $medio
     * @param String $contentType text/plain - text/html. Default is text/plain.
     * @param String $fromName
     * @param String $camposExtras JSON
     */
    public function sendNotifications($cliente, $destinatarios, $asunto, $mensaje, $medio, $contentType = "text/plain", $fromName = "Skytel", $camposExtras = "") {

        //Datos constantes
        $METHOD_NAME = 'sendNotifications';

        $countDestinataries = count($this->__delimitarDestinatarios($destinatarios));

        //Validación de destinatarios
        $destinatariosAux = $this->__delimitarDestinatarios($destinatarios);

        $mensajeAux = str_replace("\r\n", '', $mensaje);

        $params = [
            'mapMessage' => json_encode($mensajeAux), //Build JSON format
            'mapMessageExtras' => json_encode($camposExtras), //BuiltExtra JSON format
            'destinatarios' => json_encode($destinatariosAux), //Destinos JSON format
            'cliente' => $cliente, //Cliente
            'medio' => $medio, //Medio Integer
            'asunto' => $asunto, //Asunto
            'fromName' => $fromName, //FromName
            'contentType' => $contentType //ContentType
        ];

        /**
         * Formato de retorno
         */
        $return = [
            "msg" => "",
            "error" => FALSE
        ];

        /**
         * Cache
         */
        $soapurl = libs_URL_WS . "?wsdl";

        $cache = new \nusoap_wsdlcache($this->pathCache, 12000);

        $wsdl = $cache->get($soapurl);
        if (is_null($wsdl)) {
            $wsdl = new \wsdl($soapurl);
            $cache->put($wsdl);
        }

        /**
         * Client
         */
        $nsObj = new \nusoap_client($wsdl, true);

        // 1. Llamar a la funcion getRespuesta del servidor
        $result = $nsObj->call(
                $METHOD_NAME, // Funcion a llamar
                $params, // Parametros pasados a la funcion
                "uri:" . libs_URL_WS, // namespace
                "uri:" . libs_URL_WS . "/$METHOD_NAME" // SOAPAction
        );

        //Verificacion que los parametros estan ok,
        if ($nsObj->fault) {
            return [
                "msg" => $result,
                "error" => TRUE
            ];
        }

        //Conexion sin problemas
        $errorWs = $nsObj->getError();
        if ($errorWs) {
            return [
                "msg" => $nsObj->getError(),
                "error" => TRUE
            ];
        }

        //Parse del retorno del webservice
        $jsonObj = json_decode($result, TRUE);
        if ($jsonObj === NULL || $jsonObj["error"] === TRUE) {
            return [
                "msg" => $jsonObj["msg"],
                "error" => TRUE
            ];
        }

        //WARNING        
        if ($countDestinataries == 0) {
            return [
                "msg" => "No se recibieron destinatarios",
                "error" => "WARNING"
            ];
        }

        return $return;
    }

    /**
     * 
     * @param type $cliente
     * @param String $destinatarios //Unicamente emails.
     * @param type $asunto
     * @param type $mensaje
     * @param type $templateName
     * @param type $medio
     * @param type $fromName
     * @param type $camposExtras
     * 
     * @return Mixed. TRUE (Boolean, Error encontrado), FALSE (Boolean, Sin errores), WARNING (String, Enviado con alguna advertencia)
     */
    public function sendEmailsWithTemplate($cliente, $destinatarios, $asunto, $mensaje, $templateName, $medio, $fromName = "Skytel", $camposExtras = "") {

        //Datos constantes
        $METHOD_NAME = 'sendEmailsWithTemplate';

        $countDestinataries = count($this->__delimitarDestinatarios($destinatarios));

        //Validación de destinatarios
        $destinatariosValidados = [];
        $destinatariosInvalidos = [];
        foreach ($this->__delimitarDestinatarios($destinatarios) as $email) {

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $destinatariosInvalidos[] = $email;
                continue;
            }

            $destinatariosValidados[] = $email;
        }

        $mensajeAux = str_replace("\r\n", '', $mensaje);

        $params = [
            'mapMessage' => json_encode($mensajeAux), //Build JSON format
            'mapMessageExtras' => json_encode($camposExtras), //BuiltExtra JSON format
            'destinatarios' => json_encode($destinatariosValidados), //Destinos JSON format
            'cliente' => $cliente, //Cliente
            'medio' => $medio, //Medio Integer
            'template' => $templateName, //Nombre del template
            'asunto' => $asunto, //Asunto
            'fromName' => $fromName //FromName
        ];

        /**
         * Formato de retorno
         */
        $return = [
            "msg" => "",
            "error" => FALSE
        ];

        $soapurl = libs_URL_WS . "?wsdl";
        $cache = new \nusoap_wsdlcache($this->pathCache, 12000);

        /**
         * Cache
         */
        $wsdl = $cache->get($soapurl);
        if (is_null($wsdl)) {
            $wsdl = new \wsdl($soapurl);
            $cache->put($wsdl);
        }

        /**
         * Cliente
         */
        $nsObj = new \nusoap_client($wsdl, true);

        // 1. Llamar a la funcion getRespuesta del servidor
        $result = $nsObj->call(
                $METHOD_NAME, // Funcion a llamar
                $params, // Parametros pasados a la funcion
                "uri:" . libs_URL_WS, // namespace
                "uri:" . libs_URL_WS . "/$METHOD_NAME" // SOAPAction
        );

        //Verificacion que los parametros estan ok,
        if ($nsObj->fault) {
            return [
                "msg" => $result,
                "error" => TRUE
            ];
        }

        //Verificacion de la conexion
        $error = $nsObj->getError();
        if ($error) {
            return [
                "msg" => $nsObj->getError(),
                "error" => TRUE
            ];
        }

        //Parse del retorno del webservice
        $jsonObj = json_decode($result, TRUE);
        if ($jsonObj === NULL || $jsonObj["error"] === TRUE) {
            return [
                "msg" => $jsonObj["msg"],
                "error" => TRUE
            ];
        }

        //WARNING
        if (count($destinatariosInvalidos) > 0) {
            return [
                "msg" => "Destinatarios invalidos: " . implode(",", $destinatariosInvalidos),
                "error" => "WARNING"
            ];
        }

        //WARNING        
        if ($countDestinataries == 0) {
            return [
                "msg" => "No se recibieron destinatarios",
                "error" => "WARNING"
            ];
        }

        return $return;
    }

    /**
     * Devuelve la lista de mensajes en formato array
     * 
     * @param String $str
     * @return Array
     */
    function __delimitarDestinatarios($str) {

        $str = str_replace(";", ",", $str);
//    $lg->debug("Paso 1: " . $str);
        $str = str_replace(":", ",", $str);
//    $lg->debug("Paso 2: " . $str);
        $str = str_replace(" ", ",", $str);
//    $lg->debug("Paso 3: " . $str);
        $str = str_replace("|", ",", $str);
//    $lg->debug("Paso 4: " . $str);
        $str = str_replace("\r\n", "", $str);
//    $lg->debug("Paso 5: " . $str);
        $str = str_replace("\r", "", $str);
//    $lg->debug("Paso 6: " . $str);
        $str = str_replace("\n", "", $str);
//    $lg->debug("Paso 7: " . $str);

        $arrayAux = explode(",", $str);
        return array_filter($arrayAux); //remueve espacios vacios
    }

}
