<?php

namespace skytel\dreamPBX;

require_once dirname(__FILE__) . '/../utils/socket/Socket.php';
require_once dirname(__FILE__) . '/../utils/json/JsonUtils.php';

/**
 * Description of PBXInfo
 *
 * @author Endrigo Rivas <endrigo.rivas@gmail.com>
 * 
 * $Id: GetCallDetail.php 12547 2015-02-19 13:05:07Z endrigo $
 */

/*
 // EJ de uso:
 //Obtiene todos los datos de la llamada
 //
if ($uniqueId !== "" && $uniqueId !== NULL) {
    sleep(2); //El retardo es necesario para que funcione el API de la DREAMPBX
    $callObj = new \skytel\dreamPBX\GetCallDetail($uniqueId, SOCKET_IP_PBX, SOCKET_PORT_PBX, GET_CALL_AUTH_PBX);
    $callerid = $callObj->getANI();
}
*/
class GetCallDetail {

    private $uniqueID;
    private $ip;
    private $port;
    private $timeoutConnect;
    private $timeoutWrite;
    private $timeoutRead;
    //Datos de la llamada
    private $jsonSended;
    private $jsonResult;
    private $DNIS;
    private $destino;
    private $hora;
    private $fecha;
    private $tipo;
    private $agente;
    private $evento;
    private $rutaGrabacion;
    private $campana;
    private $origen; //ANI
    private $success;

    /**
     * 
     * @param type $uniqueId
     * @param type $IP
     * @param type $port
     * @param type $timeoutConnect  [Default 10 sec]
     * @param type $timeoutWrite [Default 10 sec]
     * @param type $timeoutRead [Default 10 sec]
     */
    function __construct($uniqueId, $IP, $port) {

        if (empty($uniqueId)) {
            throw new InvalidArgumentException("Falta parametro uniqueId");
        }
        if (empty($IP)) {
            throw new InvalidArgumentException("Falta parametro IP");
        }
        if (empty($port)) {
            throw new InvalidArgumentException("Falta parametro PORT");
        }

        $this->uniqueID = $uniqueId;
        $this->ip = $IP;
        $this->port = $port;
        $this->timeoutConnect = 10; //seconds
        $this->timeoutWrite = 10; //seconds
        $this->timeoutRead = 10; //seconds
        $this->success = "";

        $this->jsonSended = json_encode(
                array(
                    "EVENT" => "GET_CALL_DETAIL",
                    "UNIQUEID" => "$uniqueId"
                )
        );

        //El API de DreamPBX necesita éste caracter al final de string.
        $this->jsonSended .= "\n";

        $socket = new \skytel\utils\socket\Socket($IP, $port);

        $this->setJsonResult($socket->socketTransmission($this->jsonSended));

        //Limpia el caracter de fin de linea.
        $buscar = array(chr(13) . chr(10), "\r\n", "\n", "\r", "\000");
        $reemplazar = array("", "", "", "", "");
        $this->setJsonResult(str_ireplace($buscar, $reemplazar, $this->getJsonResult()));

        $this->__populate();
    }

    public function getJsonResult() {
        return $this->jsonResult;
    }

    public function getDNIS() {
        return $this->DNIS;
    }

    public function getDestino() {
        return $this->destino;
    }

    public function getHora() {
        return $this->hora;
    }

    public function getUniqueID() {
        return $this->uniqueID;
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function getAgente() {
        return $this->agente;
    }

    public function getEvento() {
        return $this->evento;
    }

    public function getRutaGrabacion() {
        return $this->rutaGrabacion;
    }

    public function getCampana() {
        return $this->campana;
    }

    public function getANI() {
        return $this->origen;
    }

    /**
     * Check if the last operation was successful
     * 
     * @return Boolean
     */
    public function getSuccess() {
        return $this->success;
    }

    private function setJsonResult($jsonResult) {
        $this->jsonResult = $jsonResult;
    }

    private function setDNIS($DNIS) {
        $this->DNIS = $DNIS;
    }

    private function setDestino($destino) {
        $this->destino = $destino;
    }

    private function setHora($hora) {
        $this->hora = $hora;
    }

//    private function setUniqueID($uniqueID) {
//        $this->uniqueID = $uniqueID;
//    }

    private function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    private function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    private function setAgente($agente) {
        $this->agente = $agente;
    }

    private function setEvento($evento) {
        $this->evento = $evento;
    }

    private function setRutaGrabacion($rutaGrabacion) {
        $this->rutaGrabacion = $rutaGrabacion;
    }

    private function setCampana($campana) {
        $this->campana = $campana;
    }

    private function setANI($origen) {
        $this->origen = $origen;
    }

    private function setSuccess($success) {
        $this->success = $success;
    }

    /**
     * 
     * @throws \InvalidArgumentException
     */
    private function __populate() {

        $jsonObj = json_decode($this->getJsonResult());
        if ($jsonObj === NULL) {
            throw new \InvalidArgumentException("Error: " . \skytel\utils\json\JsonUtils::getJSONErrorMsg());
        }

        //Save values
        $this->setDNIS(isset($jsonObj->dnis) ? $jsonObj->dnis : "");
        $this->setDestino(isset($jsonObj->destino) ? $jsonObj->destino : "");
        $this->setHora(isset($jsonObj->hora) ? $jsonObj->hora : "");
        $this->setFecha(isset($jsonObj->fecha) ? $jsonObj->fecha : "");
        $this->setTipo(isset($jsonObj->tipo) ? $jsonObj->tipo : "");
        $this->setAgente(isset($jsonObj->agente) ? $jsonObj->agente : "");
        $this->setEvento(isset($jsonObj->EVENT) ? $jsonObj->EVENT : "");
        $this->setRutaGrabacion(isset($jsonObj->ruta_grabacion) ? $jsonObj->ruta_grabacion : "");
        $this->setCampana(isset($jsonObj->campana) ? $jsonObj->campana : "");
        $this->setANI(isset($jsonObj->origen) ? $jsonObj->origen : "");

        $this->setSuccess(true);
        if (isset($jsonObj->UNIQUEID) && $jsonObj->UNIQUEID === "NOFOUND") {
            $this->setSuccess(false);
        }
    }

}
