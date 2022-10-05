<?php

namespace skytel\asteriskPBX;

require_once dirname(__FILE__) . '/ECCP.class.php';
require_once dirname(__FILE__) . '/../../phpagi/phpagi.php';

/**
 * Description of ElastixService
 *
 * @author Juan vallejos <vallejosfj@gmail.com>
 * $Id: ElastixService.php
 */
class ElastixService {

    private $IP_ELASTIX = "";
    private $USER_ELASTIX = "";
    private $PASSW_ELASTIX = "";
    private $DBASE_NAME = "";

    /**
     * Constructor
     * 
     * @param String $ipElastix
     * @param String $userElastix
     * @param String $passElastix
     */
    public function __construct($ipElastix, $userElastix, $passElastix, $dbaseName) {

        if ($ipElastix === "") {
            throw new \InvalidArgumentException("Missing parameter: ipElastix");
        }

        if ($userElastix === "") {
            throw new \InvalidArgumentException("Missing parameter: userElastix");
        }

        if ($passElastix === "") {
            throw new \InvalidArgumentException("Missing parameter: passElastix");
        }
        if ($dbaseName === "") {
            throw new \InvalidArgumentException("Missing parameter: dbaseName");
        }

        $this->IP_ELASTIX = $ipElastix;
        $this->USER_ELASTIX = $userElastix;
        $this->PASSW_ELASTIX = $passElastix;
        $this->DBASE_NAME = $dbaseName;
    }

    /**
     * Trae el numero de la cola configurada en la PBX
     * @param type $int
     * @return type
     */
    public function getQueueNumber($int) {

        $link = $this->__connectionDB();
        $query = "SELECT q.queue from call_entry as c
        inner join queue_call_entry as q on c.id_queue_call_entry=q.id
        where c.id = $int";
        $result = $link->query($query) or die("Error: " . mysqli_error($link));
        $this->__closeConectionDB($link);
        return $result;
    }

    public function set_variableAsterisk($variable, $valor) {
        $agi = new \AGI();
        $result = $agi->set_variable($variable, $valor);
        return $result;
    }

    /**
     * Trae el nombre del agente que figura en la PBX
     * 
     * @param type $int
     * @return type
     */
    public function getAgentName($int) {

        $link = $this->__connectionDB();
        $query = "SELECT name from agent
        where number= $int";
        $result = $link->query($query) or die("Error: " . mysqli_error($link));
        $this->__closeConectionDB($link);
        return $result;
    }

    public function getBreak() {

        $link = $this->__connectionDB();
        $query = "SELECT b.id,b.name from break as b
        where b.tipo = 'B' and b.status='A'";
        $result = $link->query($query) or die("Error: " . mysqli_error($link));
        $this->__closeConectionDB($link);
        return $result;
    }

    /**
     * Logea al agente a la PBX
     * 
     * @param type $numeroAgente
     * @param type $extension
     * @param type $host
     * @param type $user
     * @param type $pass
     * @return boolean
     */
    public function loginAgent($numeroAgente, $extension, $host, $user, $pass) {
        $eccp = new \ECCP();

        $agent = "Agent/" . $numeroAgente;
        $passAgent = $numeroAgente;

        try {
            $eccp->connect($host, $user, $pass);
            $eccp->setAgentNumber($agent);
            $eccp->setAgentPass($passAgent);
            $login = $eccp->loginagent($extension);

            $eccp->disconnect();
        } catch (Exception $e) {
            
        }
        return $login;
    }

    /**
     * Deslogea al agente de la PBX
     * 
     * @param type $numeroAgente
     * 
     * @param type $host
     * @param type $user
     * @param type $pass
     */
    public function logoutAgent($numeroAgente, $host, $user, $pass) {
        $eccp = new \ECCP();

        $agent = "Agent/" . $numeroAgente;
        $passAgent = $numeroAgente;

        try {
            $eccp->connect($host, $user, $pass);
            $eccp->setAgentNumber($agent);
            $eccp->setAgentPass($passAgent);

            $logout = $eccp->logoutagent($agent);

            $eccp->disconnect();
        } catch (Exception $e) {
            
        }
        return $logout;
    }

    /**
     * Consulta status del agente
     * 
     * @param type $numeroAgente
     * 
     * @param type $host
     * @param type $user
     * @param type $pass
     * @return type
     */
    public function getAgentStatus($numeroAgente, $host, $user, $pass) {
        $eccp = new \ECCP();

        $agent = "Agent/" . $numeroAgente;
        $passAgent = $numeroAgente;

        try {
            $eccp->connect($host, $user, $pass);
            $eccp->setAgentNumber($agent);
            $eccp->setAgentPass($passAgent);
            $status = $eccp->getagentstatus();

            $eccp->disconnect();
        } catch (Exception $e) {
            
        }
        return $status;
    }

    /**
     * Lista las pausas configuradas en el elastix
     * 
     * @param type $numeroAgente
     * @param type $extension
     * @param type $host
     * @param type $user
     * @param type $pass
     * @return type
     */
    public function getPauses($numeroAgente, $host, $user, $pass) {
        $eccp = new \ECCP();

        $agent = "Agent/" . $numeroAgente;
        $passAgent = $numeroAgente;

        try {
            $eccp->connect($host, $user, $pass);
            $eccp->setAgentNumber($agent);
            $eccp->setAgentPass($passAgent);
            $status = $eccp->getpauses();

            $eccp->disconnect();
        } catch (Exception $e) {
            
        }
        return $status;
    }

    /**
     * Pone en pausa al agente
     * 
     * @param type $numeroAgente
     * 
     * @param type $host
     * @param type $user
     * @param type $pass
     * @param type $type
     * @return type
     */
    public function pauseAgent($numeroAgente, $host, $user, $pass, $idBreak) {
        $eccp = new \ECCP();

        $agent = "Agent/" . $numeroAgente;
        $passAgent = $numeroAgente;

        try {
            $eccp->connect($host, $user, $pass);
            $eccp->setAgentNumber($agent);
            $eccp->setAgentPass($passAgent);
            $status = $eccp->pauseagent($idBreak);


            $eccp->disconnect();
        } catch (Exception $e) {
            
        }
        return $status;
    }

    /**
     * Saca de la pausa al agente
     * 
     * @param type $numeroAgente
     * 
     * @param type $host
     * @param type $user
     * @param type $pass
     * @return type
     */
    public function unPauseAgent($numeroAgente, $host, $user, $pass) {
        $eccp = new \ECCP();

        $agent = "Agent/" . $numeroAgente;
        $passAgent = $numeroAgente;

        try {
            $eccp->connect($host, $user, $pass);
            $eccp->setAgentNumber($agent);
            $eccp->setAgentPass($passAgent);
            $status = $eccp->unpauseagent();
            $eccp->disconnect();
        } catch (Exception $e) {
            
        }
        return $status;
    }

    /**
     * Pone en hold la llamada
     * 
     * @param type $numeroAgente
     * 
     * @param type $host
     * @param type $user
     * @param type $pass
     * @return type
     */
    public function HoldCall($numeroAgente, $host, $user, $pass) {
        $eccp = new \ECCP();

        $agent = "Agent/" . $numeroAgente;
        $passAgent = $numeroAgente;

        try {
            $eccp->connect($host, $user, $pass);
            $eccp->setAgentNumber($agent);
            $eccp->setAgentPass($passAgent);
            $status = $eccp->hold();
            $eccp->disconnect();
        } catch (Exception $e) {
            
        }
        return $status;
    }

    /**
     * Saca del hold la llamada
     * 
     * @param type $numeroAgente
     * 
     * @param type $host
     * @param type $user
     * @param type $pass
     * @return type
     */
    public function unHoldCall($numeroAgente, $host, $user, $pass) {
        $eccp = new \ECCP();

        $agent = "Agent/" . $numeroAgente;
        $passAgent = $numeroAgente;

        try {
            $eccp->connect($host, $user, $pass);
            $eccp->setAgentNumber($agent);
            $eccp->setAgentPass($passAgent);
            print_r($eccp->getagentstatus());
            $status = $eccp->unhold();
            $eccp->disconnect();
        } catch (Exception $e) {
            
        }
        return $status;
    }

    function transferirLlamada($sTransferExt, $bAtxfer = FALSE) {
        try {
            $oECCP = new \ECCP();
            $respuesta = $bAtxfer ? $oECCP->atxfercall($sTransferExt) : $oECCP->transfercall($sTransferExt);
            if (isset($respuesta->failure)) {
                $this->errMsg = _tr('Unable to transfer call') . ' - ' . $this->_formatoErrorECCP($respuesta);
                return FALSE;
            }
            $oECCP->disconnect();
            return TRUE;
        } catch (Exception $e) {
            $this->errMsg = '(internal) transfercall: ' . $e->getMessage();
            return FALSE;
        }
    }

    /**
     * Link Database Elastix.
     * 
     * 
     * 
     * @return type
     * @throws \Exception
     */
    private function __connectionDB() {

        if (!($link = mysqli_connect($this->IP_ELASTIX, $this->USER_ELASTIX, $this->PASSW_ELASTIX))) {
            throw new \Exception("There was a problem establishing connection!");
        }
        if (!mysqli_select_db($link, $this->DBASE_NAME)) {
            throw new \Exception("There was a problem selecting database!");
        }
        mysqli_set_charset($link, 'utf8');
        return $link;
    }

    private function __closeConectionDB($link) {
        mysqli_close($link);
    }

    function leerInfoLlamada($numeroAgente, $sCallType, $iCampaignId, $iCallId, $host, $user, $pass) {
        $agent = "Agent/" . $numeroAgente;
        $passAgent = $numeroAgente;
        try {
            $oECCP = new \ECCP();
            $oECCP->connect($host, $user, $pass);
            $oECCP->setAgentNumber($agent);
            $oECCP->setAgentPass($passAgent);

            $respuesta = $oECCP->getcallinfo($sCallType, $iCampaignId, $iCallId);
            if (isset($respuesta->failure)) {
                $this->errMsg = _tr('Unable to read call information') . ' - ' . $this->_formatoErrorECCP($respuesta);
                return NULL;
            }

            $reporte = array();
            foreach ($respuesta->children() as $xml_node) {
                switch ($xml_node->getName()) {
                    case 'call_attributes':
                        $reporte['call_attributes'] = $this->_traducirCallAttributes($xml_node);
                        break;
                    case 'matching_contacts':
                        $reporte['matching_contacts'] = $this->_traducirMatchingContacts($xml_node);
                        break;
                    case 'call_survey':
                        $reporte['call_survey'] = $this->_traducirCallSurvey($xml_node);
                        break;
                    default:
                        $reporte[$xml_node->getName()] = (string) $xml_node;
                        break;
                }
            }
            foreach (array('calltype', 'call_id', 'campaign_id', 'phone', 'status',
        'uniqueid', 'datetime_join', 'datetime_linkstart', 'trunk', 'queue',
        'agent_number', 'datetime_originate', 'datetime_originateresponse',
        'retries', 'call_attributes', 'matching_contacts', 'call_survey') as $k)
                if (!isset($reporte[$k]))
                    $reporte[$k] = NULL;
                $oECCP->disconnect();
            return $reporte;
        } catch (Exception $e) {
            $this->errMsg = '(internal) getcallinfo: ' . $e->getMessage();
            return NULL;
        }
    }

    private function _traducirCallAttributes($xml_node) {
        $reporte = array();
        foreach ($xml_node->attribute as $xml_attribute) {
            $reporte[(int) $xml_attribute->order] = array(
                'label' => (string) $xml_attribute->label,
                'value' => (string) $xml_attribute->value,
            );
        }
        ksort($reporte);
        return $reporte;
    }

    private function _traducirMatchingContacts($xml_node) {
        $reporte = array();
        foreach ($xml_node->contact as $xml_contact) {
            $atributos = array();
            foreach ($xml_contact->attribute as $xml_attribute) {
                $atributos[(int) $xml_attribute->order] = array(
                    'label' => (string) $xml_attribute->label,
                    'value' => (string) $xml_attribute->value,
                );
            }
            ksort($atributos);
            $reporte[(int) $xml_contact['id']] = $atributos;
        }
        return $reporte;
    }

    private function _traducirCallSurvey($xml_node) {
        $reporte = array();
        foreach ($xml_node->form as $xml_form) {
            $atributos = array();
            foreach ($xml_form->field as $xml_field) {
                $atributos[(int) $xml_field['id']] = array(
                    'label' => (string) $xml_field->label,
                    'value' => (string) $xml_field->value,
                );
            }
            ksort($atributos);
            $reporte[(int) $xml_form['id']] = $atributos;
        }
        return $reporte;
    }

    function estadoAgenteLogeado($sExtension, $numeroAgente, $host, $user, $pass) {
        $agent = "Agent/" . trim($numeroAgente);
        $passAgent = $numeroAgente;
        try {
            $oECCP = new \ECCP();
            $oECCP->connect($host, $user, $pass);
            $oECCP->setAgentNumber($agent);
            $oECCP->setAgentPass($passAgent);
            $connStatus = $oECCP->getagentstatus();
            if (isset($connStatus->failure)) {
                $this->errMsg = '(internal) getagentstatus: ' . $this->_formatoErrorECCP($connStatus);
                return array('estadofinal' => 'error');
            }

            $estado = $this->_traducirEstadoAgente($connStatus, $agent);
            $estado['estadofinal'] = 'logged-in';   // A modificar por condiciones

            if (!is_null($estado['pauseinfo']))
                foreach (array('pausestart') as $k) {
                    if (!is_null($estado['pauseinfo'][$k]) && preg_match('/^\d+:\d+:\d+$/', $estado['pauseinfo'][$k]))
                        $estado['pauseinfo'][$k] = date('Y-m-d ') . $estado['pauseinfo'][$k];
                }
            if (!is_null($estado['callinfo']))
                foreach (array('dialstart', 'dialend', 'queuestart', 'linkstart') as $k) {
                    if (!is_null($estado['callinfo'][$k]) && preg_match('/^\d+:\d+:\d+$/', $estado['callinfo'][$k]))
                        $estado['callinfo'][$k] = date('Y-m-d ') . $estado['callinfo'][$k];
                }

            if ($estado['status'] == 'offline') {
                $estado['estadofinal'] = is_null($estado['channel']) ? 'logged-out' : 'logging';
            } elseif ($estado['extension'] != $sExtension && preg_match('|^Agent/(\d+)$|', $agent)) {
                $estado['estadofinal'] = 'mismatch';
                $this->errMsg = 'Agente indicado, ya conectado a otra extensión.' .
                        ' ' . (string) $connStatus->extension . ' status ' . $estado['status'];
            }
            $oECCP->disconnect();
            return $estado;
        } catch (Exception $e) {
            $this->errMsg = '(internal) getagentstatus: ' . $e->getMessage();
            return array('estadofinal' => 'error');
        }
    }

    private function _traducirEstadoAgente($connStatus, $acd) {
        $estado = array(
            'status' => (string) $connStatus->status,
            'channel' => isset($connStatus->channel) ? (string) $connStatus->channel : NULL,
            'extension' => isset($connStatus->extension) ? (string) $connStatus->extension : NULL,
            'onhold' => isset($connStatus->onhold) ? ($connStatus->onhold == 1) : FALSE,
            'callchannel' => isset($connStatus->callchannel) ? (string) $connStatus->callchannel : NULL, // <-- duplicado en remote_channel
            'pauseinfo' => isset($connStatus->pauseinfo) ? array(
                'pauseid' => (int) $connStatus->pauseinfo->pauseid,
                'pausename' => (string) $connStatus->pauseinfo->pausename,
                'pausestart' => (string) $connStatus->pauseinfo->pausestart,
                    ) : NULL,
            'callinfo' => isset($connStatus->callinfo) ? array_merge(
                            $this->_traducirEstadoLlamada($connStatus->callinfo), array(
                        'agent_number' => $acd,
                        'remote_channel' => isset($connStatus->remote_channel) ? (string) $connStatus->remote_channel : NULL,
                            )
                    ) : NULL,
            'waitedcallinfo' => isset($connStatus->waitedcallinfo) ? array(
                'calltype' => (string) $connStatus->waitedcallinfo->calltype,
                'campaign_id' => (int) $connStatus->waitedcallinfo->campaign_id,
                'callid' => (int) $connStatus->waitedcallinfo->callid,
                'status' => (string) $connStatus->waitedcallinfo->status,
                    ) : NULL,
        );
        if (isset($connStatus->agentchannel))
            $estado['agentchannel'] = (string) $connStatus->agentchannel;
        if (is_null($estado['pauseinfo']) && isset($connStatus->pauseid)) {
            $estado['pauseinfo'] = array(
                'pauseid' => (int) $connStatus->pauseid,
                'pausename' => (string) $connStatus->pausename,
                'pausestart' => (string) $connStatus->pausestart,
            );
        }
        if (is_null($estado['callinfo']) && isset($connStatus->callchannel)) {
            $estado['callinfo'] = array_merge($this->_traducirEstadoLlamada($connStatus), array(
                'agent_number' => $this->_agent,
                'remote_channel' => $connStatus->callchannel,
            ));
        }
        return $estado;
    }

    private function _traducirEstadoLlamada($xml_callinfo) {
        return array(
            'callstatus' => (string) $xml_callinfo->callstatus,
            'calltype' => (string) $xml_callinfo->calltype,
            'campaign_id' => isset($xml_callinfo->campaign_id) ? (int) $xml_callinfo->campaign_id : NULL,
            'callid' => (int) $xml_callinfo->callid,
            'callnumber' => (string) $xml_callinfo->callnumber,
            'queuenumber' => isset($xml_callinfo->queuenumber) ? (string) $xml_callinfo->queuenumber : NULL,
            'dialstart' => isset($xml_callinfo->dialstart) ? (string) $xml_callinfo->dialstart : NULL,
            'dialend' => isset($xml_callinfo->dialend) ? (string) $xml_callinfo->dialend : NULL,
            'queuestart' => isset($xml_callinfo->queuestart) ? (string) $xml_callinfo->queuestart : NULL,
            'linkstart' => isset($xml_callinfo->linkstart) ? (string) $xml_callinfo->linkstart : NULL,
        );
    }

    private function _formatoErrorECCP($x) {
        if (isset($x->failure)) {
            return (int) $x->failure->code . ' - ' . (string) $x->failure->message;
        } else {
            return '';
        }
    }

}
