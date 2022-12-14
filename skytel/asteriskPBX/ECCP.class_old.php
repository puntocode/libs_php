<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4:
  Codificación: UTF-8
  +----------------------------------------------------------------------+
  | Elastix version 0.5                                                  |f
  | http://www.elastix.org                                               |
  +----------------------------------------------------------------------+
  | Copyright (c) 2006 Palosanto Solutions S. A.                         |
  +----------------------------------------------------------------------+
  | Cdla. Nueva Kennedy Calle E 222 y 9na. Este                          |
  | Telfs. 2283-268, 2294-440, 2284-356                                  |
  | Guayaquil - Ecuador                                                  |
  | http://www.palosanto.com                                             |
  +----------------------------------------------------------------------+
  | The contents of this file are subject to the General Public License  |
  | (GPL) Version 2 (the "License"); you may not use this file except in |
  | compliance with the License. You may obtain a copy of the License at |
  | http://www.opensource.org/licenses/gpl-license.php                   |
  |                                                                      |
  | Software distributed under the License is distributed on an "AS IS"  |
  | basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See  |
  | the License for the specific language governing rights and           |
  | limitations under the License.                                       |
  +----------------------------------------------------------------------+
  | The Original Code is: Elastix Open Source.                           |
  | The Initial Developer of the Original Code is PaloSanto Solutions    |
  +----------------------------------------------------------------------+
  $Id: ECCP.class_old.php 19558 2016-06-02 15:13:36Z juan $ */

define('ECCP_PORT', 20005);

class ECCPConnFailedException extends Exception {}
class ECCPUnauthorizedException extends Exception {}
class ECCPIOException extends Exception {}
class ECCPMalformedXMLException extends Exception {}
class ECCPUnrecognizedPacketException extends Exception {}
class ECCPBadRequestException extends Exception {}

/**
 * Clase que contiene una implementación de un cliente del protocolo ECCP
 * (Elastix CallCenter Protocol) para uso de una consola de cliente web.
 */
class ECCP
{
    private $_listaEventos = array(); // Lista de eventos pendientes
    private $_parseError = NULL;
    private $_response = NULL;      // Respuesta recibida para un requerimiento
    private $_parser = NULL;        // Parser expat para separar los paquetes
    private $_iPosFinal = NULL;     // Posición de parser para el paquete parseado
    private $_sTipoDoc = NULL;      // Tipo de paquete. Sólo se acepta 'event' y 'response'
    private $_bufferXML = '';       // Datos pendientes que no forman un paquete completo
    private $_iNestLevel = 0;       // Al llegar a cero, se tiene fin de paquete

    private $_hConn = NULL;
    private $_iRequestID = 0;
    private $_sAppCookie;

    private $_agentNumber = '';
    private $_agentPass = '';

    /**
     * Procedimiento que inicia la conexión y el login al servidor ECCP. 
     *
     * @param   string  $server     Servidor al cual conectarse. Puede opcionalmente
     *                              indicar el puerto como localhost:20005
     * @param   string  $username   Nombre de usuario a usar para la conexión
     * @param   string  $secret     Contraseña a usar para login
     *
     * @return  void
     * @throws  ECCPConnFailedException, ECCPUnauthorizedException, ECCPIOException
     */
    public function connect($server, $username, $secret)
    {
    	// Determinar servidor y puerto a usar
        $iPuerto = ECCP_PORT;
        if(strpos($server, ':') !== false) {
            $c = explode(':', $server);
            $server = $c[0];
            $iPuerto = $c[1];
        }

        // Iniciar la conexión
        $errno = $errstr = NULL;
        $sUrlConexion = "tcp://$server:$iPuerto";
        $this->_hConn = @stream_socket_client($sUrlConexion, $errno, $errstr);
        if (!$this->_hConn) throw new ECCPConnFailedException("$sUrlConexion: ($errno) $errstr", $errno);
        
        $this->login($username, $secret);
    }

    public function setAgentNumber($sAgentNumber) { $this->_agentNumber = $sAgentNumber; }
    public function setAgentPass($sAgentPass) { $this->_agentPass = $sAgentPass; }

    public function disconnect()
    {
        $this->logout();
        if (!is_null($this->_parser)) {
            xml_parser_free($this->_parser);
            $this->_parser = NULL;
        }
        fclose($this->_hConn);
        $this->_hConn = NULL;
    }

    // Enviar una cadena entera de requerimiento al servidor ECCP
    private function send_request($xml_request)
    {
        $this->_iRequestID++;
        $xml_request->addAttribute('id', $this->_iRequestID);
        $s = $xml_request->asXML();
        while ($s != '') {
            $iEscrito = @fwrite($this->_hConn, $s);
            if ($iEscrito === FALSE) throw new ECCPIOException('output');
            $s = substr($s, $iEscrito);
        }
        $xml_response = $this->wait_response();
        if (isset($xml_response->failure))
            throw new ECCPBadRequestException((string)$xml_response->failure->message, (int)$xml_response->failure->code);
        return $xml_response;
    }
    
    /**
     * Procedimiento para recibir eventos o respuestas del servidor ECCP. 
     * Este método leerá datos hasta que se haya visto alguna respuesta, o hasta
     * que el timeout opcional haya expirado.
     *
     * @param   int     $timeout    Intervalo en segundos a esperar por una
     *                              respuesta. Si se omite, se espera para siempre.
     *                              Si se especifica 0, se regresa de inmediato luego
     *                              de una sola verificación de datos.
     *
     * @return  mixed   Objeto SimpleXMLElement que representa los datos de la 
     *                  respuesta, o NULL si timeout.
     *
     * @throws  ECCPIOException
     */
    public function wait_response($timeout = NULL)
    {
        $iTimestampInicio = time();
        do {
            $listoLeer = array($this->_hConn);
            $listoEscribir = array();
            $listoErr = array();
            $iNumCambio = @stream_select($listoLeer, $listoEscribir, $listoErr, 0, 100000);
            if ($iNumCambio === FALSE) {
                throw new ECCPIOException('input');
            } elseif (count($listoErr) > 0) {
                throw new ECCPIOException('input');
            } elseif ($iNumCambio > 0 || count($listoLeer) > 0) {
                $s = fread($this->_hConn, 65536);
                $this->parsearPaquetesXML($s);
            }
        } while (is_null($this->_response) && (is_null($timeout) || time() - $iTimestampInicio < $timeout));

        // Devolver lo que haya de respuesta
        $r = $this->_response;
        $this->_response = NULL;
        return $r;
    }

    public function getParseError() { return $this->_parseError; }
    public function getEvent() { return array_shift($this->_listaEventos); }

    // Implementación de parser expat: inicio

    // Parsear y separar tantos paquetes XML como sean posibles
    private function parsearPaquetesXML($data)
    {
        if (is_null($this->_parser)) $this->_resetParser();

        $this->_bufferXML .= $data;
        $r = xml_parse($this->_parser, $data);
        while (!is_null($this->_iPosFinal)) {
            if ($this->_sTipoDoc == 'event') {
                $this->_listaEventos[] = simplexml_load_string(substr($this->_bufferXML, 0, $this->_iPosFinal));
            } elseif ($this->_sTipoDoc == 'response') {
                $this->_response = simplexml_load_string(substr($this->_bufferXML, 0, $this->_iPosFinal));
            } else {
                $this->_parseError = array(
                    'errorcode'     =>  -1,
                    'errorstring'   =>  "Unrecognized packet type: {$this->_sTipoDoc}",
                    'errorline'     =>  xml_get_current_line_number($this->_parser),
                    'errorpos'      =>  xml_get_current_column_number($this->_parser),
                );
                throw new ECCPUnrecognizedPacketException();
            }
            $this->_bufferXML = ltrim(substr($this->_bufferXML, $this->_iPosFinal));
            $this->_iPosFinal = NULL;
            $this->_resetParser();
            if ($this->_bufferXML != '')
                $r = xml_parse($this->_parser, $this->_bufferXML);
        }
        if (!$r) {
            $this->_parseError = array(
                'errorcode'     =>  xml_get_error_code($this->_parser),
                'errorstring'   =>  xml_error_string(xml_get_error_code($this->_parser)),
                'errorline'     =>  xml_get_current_line_number($this->_parser),
                'errorpos'      =>  xml_get_current_column_number($this->_parser),
            );
            throw new ECCPMalformedXMLException();
        }
        return $r;
    }
    
    // Resetear el parseador, para iniciarlo, o luego de parsear un paquete
    private function _resetParser()
    {
        if (!is_null($this->_parser)) xml_parser_free($this->_parser);
        $this->_parser = xml_parser_create('UTF-8');
        xml_set_element_handler ($this->_parser,
            array($this, 'xmlStartHandler'),
            array($this, 'xmlEndHandler'));
        xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, 0);
    }

    function xmlStartHandler($parser, $name, $attribs)
    {
        $this->_iNestLevel++;
    }

    function xmlEndHandler($parser, $name)
    {
        $this->_iNestLevel--;
        if ($this->_iNestLevel == 0) {
            $this->_iPosFinal = xml_get_current_byte_index($parser);
            $this->_sTipoDoc = $name;
        }
    }

    // Implementación de parser expat: final

    private function agentHash($agent_number, $agent_pass)
    {
        return md5($this->_sAppCookie.$agent_number.$agent_pass);	
    }

    // Requerimientos conocidos del protocolo ECCP
    
    public function login($username, $password)
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('login');
        $xml_cmdRequest->addChild('username', $username);
        $xml_cmdRequest->addChild('password', md5($password));
        $xml_response = $this->send_request($xml_request);
        if (isset($xml_response->login_response->app_cookie))
            $this->_sAppCookie = $xml_response->login_response->app_cookie; 
        return $xml_response->login_response;
    }
    
    public function logout()
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_logoutRequest = $xml_request->addChild('logout');
        $xml_response = $this->send_request($xml_request);
        return TRUE;
    }
    
    public function loginagent($extension, $password = NULL)
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('loginagent');
        $xml_cmdRequest->addChild('agent_number', $this->_agentNumber);
        $xml_cmdRequest->addChild('agent_hash', $this->agentHash($this->_agentNumber, $this->_agentPass));
        $xml_cmdRequest->addChild('extension', $extension);
        if (!is_null($password))
            $xml_cmdRequest->addChild('password', $password);
        $xml_response = $this->send_request($xml_request);
        return $xml_response->loginagent_response;
    }

    public function logoutagent()
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('logoutagent');
        $xml_cmdRequest->addChild('agent_number', $this->_agentNumber);
        $xml_cmdRequest->addChild('agent_hash', $this->agentHash($this->_agentNumber, $this->_agentPass));
        $xml_response = $this->send_request($xml_request);
        return $xml_response->logoutagent_response;
    }

    public function getagentstatus()
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('getagentstatus');
        $xml_cmdRequest->addChild('agent_number', $this->_agentNumber);
        $xml_response = $this->send_request($xml_request);
        return $xml_response->getagentstatus_response;
    }

    public function getcampaigninfo($campaign_type, $campaign_id)
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('getcampaigninfo');
        $xml_cmdRequest->addChild('campaign_type', $campaign_type);
        $xml_cmdRequest->addChild('campaign_id', $campaign_id);
        $xml_response = $this->send_request($xml_request);
        return $xml_response->getcampaigninfo_response;
    }

    public function getcallinfo($campaign_type, $campaign_id, $call_id)
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('getcallinfo');
        $xml_cmdRequest->addChild('campaign_type', $campaign_type);
        $xml_cmdRequest->addChild('campaign_id', $campaign_id);
        $xml_cmdRequest->addChild('call_id', $call_id);
        $xml_response = $this->send_request($xml_request);
        return $xml_response->getcallinfo_response;
    }
    
    public function setcontact($call_id, $contact_id)
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('setcontact');
        $xml_cmdRequest->addChild('agent_number', $this->_agentNumber);
        $xml_cmdRequest->addChild('agent_hash', $this->agentHash($this->_agentNumber, $this->_agentPass));
        $xml_cmdRequest->addChild('call_id', $call_id);
        $xml_cmdRequest->addChild('contact_id', $contact_id);
        $xml_response = $this->send_request($xml_request);
        return $xml_response->getcallinfo_response;
    }
    
    public function saveformdata($campaign_type, $call_id, $formdata)
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('saveformdata');
        $xml_cmdRequest->addChild('agent_number', $this->_agentNumber);
        $xml_cmdRequest->addChild('agent_hash', $this->agentHash($this->_agentNumber, $this->_agentPass));
        $xml_cmdRequest->addChild('call_id', $call_id);

        $xml_forms = $xml_cmdRequest->addChild('forms');
        foreach ($formdata as $idForm => $fields) {
            $xml_form = $xml_forms->addChild('form');
            $xml_form->addAttribute('id', $idForm);
            foreach ($fields as $idField => $sFieldValue) {
                $xml_field = $xml_form->addChild('field', $sFieldValue);
                $xml_field->addAttribute('id', $idField);
            }
        }

        $xml_response = $this->send_request($xml_request);
        return $xml_response->saveformdata_response;
    }
    
    public function getpauses()
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('getpauses');
        $xml_response = $this->send_request($xml_request);
        return $xml_response->getpauses_response;
    }
    
    public function pauseagent($pause_type)
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('pauseagent');
        $xml_cmdRequest->addChild('agent_number', $this->_agentNumber);
        $xml_cmdRequest->addChild('agent_hash', $this->agentHash($this->_agentNumber, $this->_agentPass));
        $xml_cmdRequest->addChild('pause_type', $pause_type);
        $xml_response = $this->send_request($xml_request);
        return $xml_response->pauseagent_response;
    }

    public function unpauseagent()
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('unpauseagent');
        $xml_cmdRequest->addChild('agent_number', $this->_agentNumber);
        $xml_cmdRequest->addChild('agent_hash', $this->agentHash($this->_agentNumber, $this->_agentPass));
        $xml_response = $this->send_request($xml_request);
        return $xml_response->unpauseagent_response;
    }

    public function hangup()
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('hangup');
        $xml_cmdRequest->addChild('agent_number', $this->_agentNumber);
        $xml_cmdRequest->addChild('agent_hash', $this->agentHash($this->_agentNumber, $this->_agentPass));
        $xml_response = $this->send_request($xml_request);
        return $xml_response->hangup_response;
    }

    public function hold()
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('hold');
        $xml_cmdRequest->addChild('agent_number', $this->_agentNumber);
        $xml_cmdRequest->addChild('agent_hash', $this->agentHash($this->_agentNumber, $this->_agentPass));
        $xml_response = $this->send_request($xml_request);
        return $xml_response->hold_response;
    }
    
    public function unhold()
    {
        $xml_request = new SimpleXMLElement("<request />");
        $xml_cmdRequest = $xml_request->addChild('unhold');
        $xml_cmdRequest->addChild('agent_number', $this->_agentNumber);
        $xml_cmdRequest->addChild('agent_hash', $this->agentHash($this->_agentNumber, $this->_agentPass));
        $xml_response = $this->send_request($xml_request);
        return $xml_response->unhold_response;
    }
     
}
?>
