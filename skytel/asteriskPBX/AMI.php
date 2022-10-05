<?php

namespace skytel\asteriskPBX;

require_once dirname(__FILE__) . '/../../phpagi/phpagi-asmanager.php';

/**
 * Description of AMI
 *
 * @author Endrigo Rivas <endrigo.rivas@gmail.com>
 * 
 * @actualizado y ampliado: Juan Vallejos <vallejosfj@gmail.com>
 * $Id: AMI.php 22318 2017-01-12 12:19:00Z juan $
 */
class AMI {

    private $IP_ASTERISK = "";
    private $USER_ASTERISK = "";
    private $PASSW_ASTERISK = "";

    /**
     * Constructor
     * 
     * @param String $ipAsterisk
     * @param String $userAsterisk
     * @param String $passAsterisk
     */
    public function __construct($ipAsterisk, $userAsterisk, $passAsterisk) {

        if ($ipAsterisk === "") {
            throw new \InvalidArgumentException("Missing parameter: ipAsterisk");
        }

        if ($userAsterisk === "") {
            throw new \InvalidArgumentException("Missing parameter: userAsterisk");
        }

        if ($passAsterisk === "") {
            throw new \InvalidArgumentException("Missing parameter: passAsterisk");
        }

        $this->IP_ASTERISK = $ipAsterisk;
        $this->USER_ASTERISK = $userAsterisk;
        $this->PASSW_ASTERISK = $passAsterisk;
    }

    /**
     * Llama a la extensión del agente, y al atender genera la llamada
     * al numero de destino.
     * 
     * El numero de extension se consigue en base al numero de agente.
     * 
     * @param String $numeroAgente
     * @param String $destino
     * @param String $prefijoSalida
     * @param String $contexto
     * @return string
     */
    public function llamarSIP($numeroAgente, $destino, $prefijoSalida, $contexto = "from-internal") {

        if ($numeroAgente === "") {
            throw new \InvalidArgumentException("Missing parameter: numeroAgente");
        }

        if ($destino === "") {
            throw new \InvalidArgumentException("Missing parameter: destino");
        }

        if ($contexto === "") {
            throw new \InvalidArgumentException("Missing parameter: contexto");
        }

        $asm = $this->__getConnection();

        /**
         * Verify if there's a call in course.
         */
        $channelA = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelA !== "") {
            $params['response'] = "CALL_IN_COURSE";
            $params['message'] = "There is a call in course.";
            $asm->disconnect();
            return $params;
        }

        //Limpiar el numero de destino.
        $destino = $this->__cleanDestinity($destino);

        $params = [
            "exten" => $prefijoSalida . "" . $destino,
            "prioridad" => "1",
            "context" => $contexto,
            "sync" => FALSE,
            "timeout" => 60000, //miliseconds
        ];

        /**
         * Obtener la extensión en base al numero de agente.
         */
        $extension = $this->__getExtension($asm, $numeroAgente);

        if ($extension == "") {
            $params['response'] = "Error";
            $params['message'] = "No se encontro la extension";
            $asm->disconnect();
            return $params;
        }

        $params['channel'] = "SIP/$extension";

        /**
         * Ej
         * $asm->Originate("SIP/1613", "8985787401", $contexto, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'yes', 'id_nada');
         */
        $call = $asm->Originate(
                $params['channel'], $params['exten'], $contexto, $params['prioridad'], NULL, NULL, $params['timeout'], NULL, NULL, NULL, $params['sync'], NULL
        );

        //Valores de retorno
        $params['response'] = $call["Response"]; //Success, Error
        $params['message'] = $call["Message"];
        $asm->disconnect();
        return $params;
    }

    public function llamarSIPSaliente($destino, $extension, $prefijoSalida, $contexto = "from-internal") {

//        if ($numeroAgente === "") {
//            throw new \InvalidArgumentException("Missing parameter: numeroAgente");
//        }

        if ($destino === "") {
            throw new \InvalidArgumentException("Missing parameter: destino");
        }

        if ($contexto === "") {
            throw new \InvalidArgumentException("Missing parameter: contexto");
        }

        $asm = $this->__getConnection();

        /**
         * Verify if there's a call in course.
         */
//        $channelA = $this->__getChannelOfTransferA($asm, $numeroAgente);
//        if ($channelA !== "") {
//            $params['response'] = "CALL_IN_COURSE";
//            $params['message'] = "There is a call in course.";
//            return $params;
//        }
        //Limpiar el numero de destino.
        $destino = $this->__cleanDestinity($destino);

        $params = [
            "exten" => $prefijoSalida . "" . $destino,
            "prioridad" => "1",
            "context" => $contexto,
            "sync" => FALSE,
            "timeout" => 60000, //miliseconds
        ];

        /**
         * Obtener la extensión en base al numero de agente.
         */
//        $extension = $this->__getExtension($asm, $numeroAgente);

        if ($extension == "") {
            $params['response'] = "Error";
            $params['message'] = "No se encontro la extension";
            $asm->disconnect();
            return $params;
        }

        $params['channel'] = "SIP/$extension";

        /**
         * Ej
         * $asm->Originate("SIP/1613", "8985787401", $contexto, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'yes', 'id_nada');
         */
        $call = $asm->Originate(
                $params['channel'], $params['exten'], $contexto, $params['prioridad'], NULL, NULL, $params['timeout'], NULL, NULL, NULL, $params['sync'], NULL
        );

        //Valores de retorno
        $params['response'] = $call["Response"]; //Success, Error
        $params['message'] = $call["Message"];
        $asm->disconnect();
        return $params;
    }

    /**
     * 
     * @param type $numeroAgente
     * @param type $destino
     * @param type $extension
     * @param type $prefijoSalida
     * @param type $contexto
     * @return string
     * @throws \InvalidArgumentException
     */
    public function loginAgente($numeroAgente, $destino, $extension, $prefijoSalida, $contexto = "from-internal") {

        if ($numeroAgente === "") {
            throw new \InvalidArgumentException("Missing parameter: numeroAgente");
        }

        if ($destino === "") {
            throw new \InvalidArgumentException("Missing parameter: destino");
        }

        if ($contexto === "") {
            throw new \InvalidArgumentException("Missing parameter: contexto");
        }

        $asm = $this->__getConnection();

        /**
         * Verify if there's a call in course.
         */
        $channelA = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelA !== "") {
            $params['response'] = "CALL_IN_COURSE";
            $params['message'] = "There is a call in course.";
            $asm->disconnect();
            return $params;
        }

        //Limpiar el numero de destino.
        $destino = $this->__cleanDestinity($destino);

        $params = [
            "exten" => $prefijoSalida . "" . $destino,
            "prioridad" => "1",
            "context" => $contexto,
            "sync" => FALSE,
            "timeout" => 60000, //miliseconds
        ];

        /**
         * Obtener la extensión en base al numero de agente.
         */
//        $extension = $this->__getExtension($asm, $numeroAgente);

        if ($extension == "") {
            $params['response'] = "Error";
            $params['message'] = "No se encontro la extension";
            $asm->disconnect();
            return $params;
        }

        $params['channel'] = "SIP/$extension";

        /**
         *  ami->Originate(
          $sExtension,        // channel
          NULL, NULL, NULL,   // extension, context, priority
          'AgentLogin',       // application
          $agentFields['number'],        // data
          NULL,
          $sAgente.' Login', // CallerID
          NULL, NULL,
          TRUE,               // async
          'ECCP:1.0:'.posix_getpid().':AgentLogin:'.$sAgente     // action-id
          );
         * Ej
         * $asm->Originate("SIP/1613", "8985787401", $contexto, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'yes', 'id_nada');
         */
        $call = $asm->Originate(
                $params['channel'], NULL, NULL, NULL, 'AgentLogin', $numeroAgente, $params['timeout'], $numeroAgente . 'Login', NULL, NULL, $params['sync'], 'Agente logeado desde plataforma::AgentLogin:' . $sAgente
        );

        //Valores de retorno
        $params['response'] = $call["Response"]; //Success, Error
        $params['message'] = $call["Message"];
        $asm->disconnect();
        return $params;
    }

    /**
     * This function call to destinity and link with the agent. 
     * Meantime the agent listen to the ring tone.
     * 
     * The agent must be logged using both the softphone or IP thelephone.
     * 
     * @param String $numeroAgente
     * @param String $destino Ej: 985787401, 
     * @param String $prefijoSalida Ej. 8 llamadas a PY, 75411 llamadas a AR.
     * @param String $contexto
     * @return string
     */
    public function llamarAgente($numeroAgente, $destino, $prefijoSalida, $contexto = "from-internal") {

        if ($numeroAgente === "") {
            throw new \InvalidArgumentException("Missing parameter: numeroAgente");
        }

        if ($destino === "") {
            throw new \InvalidArgumentException("Missing parameter: destino");
        }

        if ($contexto === "") {
            throw new \InvalidArgumentException("Missing parameter: contexto");
        }

        $asm = $this->__getConnection();

        /**
         * Verify if there're a call in course.
         */
        $channelA = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelA !== "") {
            $params['response'] = "CALL_IN_COURSE";
            $params['message'] = "There is a call in course.";
            $asm->disconnect();
            return $params;
        }

        $channelAgent = "";

        //Limpiar el numero de destino.
        $destino = $this->__cleanDestinity($destino);

        $params = [
            "channel" => "agent/$numeroAgente",
            "exten" => $prefijoSalida . "" . $destino,
            "prioridad" => "1",
            "context" => $contexto,
            "sync" => FALSE,
            "timeout" => 60000, //miliseconds
        ];

        /**
         * Ej
         * $asm->Originate("agent/1613", "8985787401", $contexto, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'yes', 'id_nada');
         */
        $call = $asm->Originate(
                $params['channel'], $params['exten'], $contexto, $params['prioridad'], NULL, NULL, $params['timeout'], NULL, NULL, NULL, $params['sync'], NULL
        );

        //This function blocks execution
        $status = $this->__getStatusOfCall($asm, $numeroAgente, $channelAgent);

        if ($status === "UNANSWERED") {
            $params['response'] = $status; //UNANSWERED
            $params['message'] = "Unsuccessfull transference. Unanswered call";
        }

        if ($status === "ANSWERED") {
            $params['response'] = $status; //ANSWERED
            $params['message'] = "Successfull transference. Answered call";
        }
        $asm->disconnect();
        return $params;
    }

    public function llamarAgenteSaliente($numeroAgente, $destino, $prefijoSalida, $contexto = "from-internal") {

        if ($numeroAgente === "") {
            throw new \InvalidArgumentException("Missing parameter: numeroAgente");
        }

        if ($destino === "") {
            throw new \InvalidArgumentException("Missing parameter: destino");
        }

        if ($contexto === "") {
            throw new \InvalidArgumentException("Missing parameter: contexto");
        }

        $asm = $this->__getConnection();

        /**
         * Verify if there're a call in course.
         */
        $channelA = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelA !== "") {
            $params['response'] = "CALL_IN_COURSE";
            $params['message'] = "There is a call in course.";
            $asm->disconnect();
            return $params;
        }

        $channelAgent = "";

        //Limpiar el numero de destino.
        $destino = $this->__cleanDestinity($destino);

        $params = [
            "channel" => "agent/$numeroAgente",
            "exten" => $prefijoSalida . "" . $destino,
            "prioridad" => "1",
            "context" => $contexto,
            "sync" => FALSE,
            "timeout" => 60000, //miliseconds
        ];

        /**
         * Ej
         * $asm->Originate("agent/1613", "8985787401", $contexto, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'yes', 'id_nada');
         */
        $call = $asm->Originate(
                $params['channel'], $params['exten'], $contexto, $params['prioridad'], NULL, NULL, $params['timeout'], NULL, NULL, NULL, $params['sync'], NULL
        );

        //This function blocks execution
        $status = $this->__getStatusOfCall($asm, $numeroAgente, $channelAgent);

        if ($status === "UNANSWERED") {
            $params['response'] = $status; //UNANSWERED
            $params['uniqueid'] = FALSE;
//            $params['uniqueid'] = $this->getUniqueid($numeroAgente);
            $params['message'] = "Unsuccessfull transference. Unanswered call";
        }

        if ($status === "ANSWERED") {
            $params['response'] = $status; //ANSWERED
            $params['uniqueid'] = $this->getUniqueid($numeroAgente);
            $params['message'] = "Successfull transference. Answered call";
        }

        return $params;
    }

    public function llamarAgenteCola($numeroAgente, $destino, $cola, $prefijoSalida, $contexto = "from-internal") {

        if ($numeroAgente === "") {
            throw new \InvalidArgumentException("Missing parameter: numeroAgente");
        }

        if ($destino === "") {
            throw new \InvalidArgumentException("Missing parameter: destino");
        }

        if ($contexto === "") {
            throw new \InvalidArgumentException("Missing parameter: contexto");
        }

        $asm = $this->__getConnection();

        /**
         * Verify if there're a call in course.
         */
        $channelA = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelA !== "") {
            $params['response'] = "CALL_IN_COURSE";
            $params['message'] = "There is a call in course.";
            $asm->disconnect();
            return $params;
        }

        $channelAgent = "";

        //Limpiar el numero de destino.
        $destino = $this->__cleanDestinity($destino);

        $params = [
            "channel" => "SIP/Troncal-billing/9313673013595" . $destino,
            //"channel" => "agent/$numeroAgente",
            //"exten" => $prefijoSalida . "" . $destino,
            "prioridad" => "1",
            "context" => $contexto,
            "sync" => FALSE,
            "timeout" => 60000, //miliseconds
        ];

        /**
         * Ej
         * $asm->Originate("agent/1613", "8985787401", $contexto, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'yes', 'id_nada');
         */
        $call = $asm->Originate(
                $params['channel'], NULL, $contexto, $params['prioridad'], 'Queue', $cola, $params['timeout'], NULL, NULL, NULL, $params['sync'], NULL
        );
//        sleep(2);
//        $channelCaller = $this->__getChannelOutbound($numeroAgente);
//        $prefijoTransfer = "";
//        $callTrans = $asm->Redirect(
//                $channelCaller, $prefijoTransfer, '7000', $params['context'], $params['prioridad']
//        );
//        $this->cortarAgente($numeroAgente);
//        //$res = $this->__transferirAcola($numeroAgente, $channelCaller, $cola, $prefijoTransfer, $contexto);
//        $params['transfer'] = $callTrans;
        //This function blocks execution
        $status = $this->__getStatusOfCall($asm, $numeroAgente, $channelAgent);

        if ($status === "UNANSWERED") {
            $params['response'] = $status; //UNANSWERED
            $params['message'] = "Unsuccessfull transference. Unanswered call";
        }

        if ($status === "ANSWERED") {
            $params['response'] = $status; //ANSWERED
            $params['message'] = "Successfull transference. Answered call";
        }
        $asm->disconnect();
        return $params;
    }

    /**
     * Scenario:
     * A phones to operator. So you've 2 channels: channel Operator, and channel with A.
     * Operator phones to B. So you've 3 channels: channel Operator, channel with A, channel with B.
     * If B accepts the call, you link channel A to channel B.
     * If B reject the call, or it's hangup, you link channel A with operator.
     * 
     * Previously you begin to transfer, you need a call in course. (Between A point and OPERATOR)
     * 
     * @param type $destino
     * @throws \InvalidArgumentException
     */
    public function transferir($numeroAgente, $destino, $prefijoSalida, $contexto = "from-internal") {

        if ($numeroAgente === "") {
            throw new \InvalidArgumentException("Missing parameter: numeroAgente");
        }

        if ($destino === "") {
            throw new \InvalidArgumentException("Missing parameter: destino");
        }

        if ($contexto === "") {
            throw new \InvalidArgumentException("Missing parameter: contexto");
        }

        //Clean destinity number
        $destino = $this->__cleanDestinity($destino);

        $asm = $this->__getConnection();

        $params = array(
            'channel' => "agent/$numeroAgente", //Agent channel
            'context' => $contexto,
            'exten' => $prefijoSalida . "" . $destino);

        $params['channelB'] = "";

        /**
         * Verify if there's a call in course.
         */
        $channelA = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelA === "") {
            $params['response'] = "REQUIRED_CALL"; //Success, Error
            $params['message'] = "There isn't a call in course.";
            $asm->disconnect();
            return $params;
        }

        $params['channelA'] = $channelA;

        /**
         * This generate a call between Agent and B. 
         * (Links channel agent and channel B).
         */
        $call = $asm->send_request('Atxfer', $params);

        //Valores de retorno
        $params['response'] = $call["Response"]; //Success, Error
        $params['message'] = $call["Message"];

        if ($params['response'] === "Error") {
            $asm->disconnect();
            return $params;
        }

        //This function blocks execution
        $status = $this->__getStatusOfTransfer($asm, $numeroAgente, $channelA);

        /**
         * Check if point A hangups the connection after transference began.
         */
        if ($this->__getChannelOfTransferA($asm, $numeroAgente) === "") {
            $params['response'] = "POINT_A_FINISHED";
            $params['message'] = "Origin point finished connection. Ended call.";
            $asm->disconnect();
            return $params;
        }

        if ($status === "UNANSWERED") {
            $params['response'] = $status; //UNANSWERED
            $params['message'] = "Unsuccessfull transference. Unanswered call";
        }

        if ($status === "ANSWERED") {
            $params['response'] = $status; //ANSWERED
            $params['message'] = "Successfull transference. Answered call";

            $channelB = $this->__getChannelOfTransferA($asm, $numeroAgente);
            $params['channelB'] = $channelB;
        }
        $asm->disconnect();
        return $params;
    }

    /**
     * Scenario:
     * A phones to operator. So you've 2 channels: channel Operator, and channel with A.
     * Operator phones to B. So you've 3 channels: channel Operator, channel with A, channel with B.
     * In this moment, the PBX hangups call with A, and link channel A with channel B
     * 
     * Previously you begin to transfer, you need a call in course.
     * 
     * @param type $destino
     * @throws \InvalidArgumentException
     */
    public function transferirDirecto($numeroAgente, $destino, $prefijoSalida, $contexto = "from-internal") {

        if ($numeroAgente === "") {
            throw new \InvalidArgumentException("Missing parameter: numeroAgente");
        }

        if ($destino === "") {
            throw new \InvalidArgumentException("Missing parameter: destino");
        }

        if ($contexto === "") {
            throw new \InvalidArgumentException("Missing parameter: contexto");
        }

        //Clean destinity number
        $destino = $this->__cleanDestinity($destino);

        $asm = $this->__getConnection();

        $params = array(
            'channel' => "agent/$numeroAgente", //Agent channel
            'context' => $contexto,
            'exten' => $prefijoSalida . "" . $destino);

        /**
         * Verify if there're a call in course.
         */
        $channelA = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelA === "") {
            $params['response'] = "REQUIRED_CALL"; //Success, Error
            $params['message'] = "There isn't a call in course.";
            $asm->disconnect();
            return $params;
        }

        $params['channelA'] = $channelA;

        $extrachannel = "";
        $priority = 1;

        /**
         * This generate a call between Agent and B. 
         * (Links channel A and channel B).
         */
        $call = $asm->Redirect(
                $params['channelA'], $extrachannel, $params['exten'], $params['context'], $priority
        );

        //Returned values
        $params['response'] = $call["Response"]; //Success, Error
        $params['message'] = $call["Message"];

        if ($params['response'] === "Error") {
            $asm->disconnect();
            return $params;
        }
        $asm->disconnect();
        return $params;
    }

    /**
     * Hang up agent call. Expects a call between A point and AGENT.
     * 
     * 
     * @param type $numeroAgente
     * @return type
     * @throws \InvalidArgumentException
     */
    public function cortarAgente($numeroAgente) {

        if ($numeroAgente === "") {
            throw new \InvalidArgumentException("Missing parameter: numeroAgente");
        }

        $asm = $this->__getConnection();

        $params['agent'] = $numeroAgente;

        /**
         * Verify if there're a call in course.
         */
        $channelA = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelA === "") {
            $params['response'] = "REQUIRED_CALL"; //Success, Error
            $params['message'] = "There isn't a call in course.";
            $asm->disconnect();
            return $params;
        }

        $call = $asm->Hangup("agent/$numeroAgente");

        //Valores de retorno
        $params['response'] = $call["Response"]; //Success, Error
        $params['message'] = $call["Message"];
        $asm->disconnect();
        return $params;
    }

    /**
     * 
     * @param type $numeroAgente
     * @param type $channelCurrent
     * @param type $channelB
     * @return string
     * @throws \InvalidArgumentException
     */
    public function aceptarTransferencia($numeroAgente, $channelA, $channelB) {

        //Validations
        if ($numeroAgente === "") {
            throw new \InvalidArgumentException("Missing parameter: numeroAgente");
        }

        if ($channelA === "") {
            throw new \InvalidArgumentException("Missing parameter: channelA");
        }

        if ($channelB === "") {
            throw new \InvalidArgumentException("Missing parameter: channelB");
        }

        $asm = $this->__getConnection();

        $params['agent'] = $numeroAgente;
        $params['channelA'] = $channelA;
        $params['channelB'] = $channelB;

        /**
         * Detectar que se cortó la conexión con el punto B
         * Si es así, en éste punto el operador tiene el canal secundario igual a canalA
         */
        $channelCurrent = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelCurrent === $channelA) {

            $params['response'] = "POINT_B_FINISHED";
            $params['message'] = "Final call point is closed! (Between operator and B)";
            $asm->disconnect();
            return $params;
        }

        $call = $asm->Hangup("agent/$numeroAgente");

        /**
         * Detectar que se conectaron correctamente el punto A con el punto B
         */
        $linkedAB = $this->__areLinkedAB($channelA, $channelB);
        if ($linkedAB === TRUE) {

            $params['response'] = $call["Response"]; //Success, Error
            $params['message'] = "Linked calls!:" . $call["Message"];
            $asm->disconnect();
            return $params;
        }

        /**
         * Detectar que se cortó la conexion con el punto A
         * Si es así, en este punto el operador tiene el canal secundario vacio.
         */
        $channelCurrent = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelCurrent === "") {

            $params['response'] = "POINT_A_FINISHED";
            $params['message'] = "Initial call point is closed! (Beetween A and operator)";
            $asm->disconnect();
            return $params;
        }

        //Nunca debería llegar aqui.
        throw new \InvalidArgumentException("Invalid condition reached!");
    }

    /**
     * A llama a Operador.
     * Operador contacta con B.
     * B, reclina la llamada.
     * 
     * @param type $numeroAgente
     * @return type
     * @throws \InvalidArgumentException
     */
    public function rechazarTransferencia($numeroAgente, $channelA, $channelB) {

        //Validations
        if ($numeroAgente === "") {
            throw new \InvalidArgumentException("Missing parameter: numeroAgente");
        }

        if ($channelA === "") {
            throw new \InvalidArgumentException("Missing parameter: channelA");
        }

        if ($channelB === "") {
            throw new \InvalidArgumentException("Missing parameter: channelB");
        }

        $asm = $this->__getConnection();

        $params['agent'] = $numeroAgente;
        $params['channelA'] = $channelA;
        $params['channelB'] = $channelB;

        /**
         * Detectar que se cortó la conexión con el punto B
         * Si es así, en éste punto el operador tiene el canal secundario igual a canalA
         */
        $channelCurrent = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelCurrent === $channelA) {

            $params['response'] = "POINT_B_FINISHED";
            $params['message'] = "Final call point is closed! (Between operator and B)";
            $asm->disconnect();
            return $params;
        }

        $channelBAux = $this->__getChannelOfTransferB($asm, $numeroAgente);
        $params['channelBAux'] = $channelBAux;

        $call = $asm->Hangup($channelBAux);

        sleep(2);

        /**
         * Detectar que se cortó la conexion con el punto A
         * Si es así, en este punto el operador esta libre tiene el canal secundario vacio.
         */
        $channelCurrent = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelCurrent === "") {

            $params['response'] = "POINT_A_FINISHED";
            $params['message'] = "Initial call point is closed! (Beetween A and operator)";
            $asm->disconnect();
            return $params;
        }

        //Valores de retorno
        $params['response'] = $call["Response"]; //Success, Error
        $params['message'] = $call["Message"];
        $asm->disconnect();
        return $params;
    }

    /**
     * 
     * Verifica si existe una llamada en curso entre el agente y el punto A.
     * 
     * @param type $numeroAgente
     * @return string
     */
    public function verificaLlamada($numeroAgente) {

        $asm = $this->__getConnection();

        /**
         * Verify if there're a call in course.
         */
        $channelA = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelA === "") {
            $params['response'] = "NO_CALL_IN_COURSE";
            $params['message'] = "There isn't a call in course.";
            $asm->disconnect();
            return $params;
        }

        $params['response'] = "CALL_IN_COURSE";
        $params['message'] = "There is a call in course.";
        $asm->disconnect();
        return $params;
    }

    /**
     * Retorna el 
     * 
     * @param \skytel\asteriskPBX\type $asm
     * @param type $numeroAgente
     * @return type
     */
    public function agenteEstado($numeroAgente) {

        $status = "UNREGISTERED";

        $asm = $this->__getConnection();

        $resp = $asm->Command("agent show online");

        foreach (explode("\n", $resp['data']) as $line) {

            $numeroAgenteAux = $this->__getToken($line, 0);

            if ($numeroAgente == $numeroAgenteAux) {
                $status = "REGISTERED";
                break;
            }
        }

        $params['response'] = $status;
        $params['message'] = "";
        $asm->disconnect();
        return $params;
    }

    /**
     * 
     * @return string
     */
    public function listaAgentesActivos() {

        $arrayAux = [];

        $asm = $this->__getConnection();
        $resp = $asm->Command("agent show online");

        foreach (explode("\n", $resp['data']) as $line) {

            if ((stristr($line, '(') === FALSE) && (stristr($line, ')') === FALSE)) {
                continue;
            }

            $arrayAgent = [];

            $agentNumber = $this->__getToken($line, 0);
            $agentName = $this->__getNameToken($line);


            $arrayAgent['agentNumber'] = $agentNumber;
            $arrayAgent['agentName'] = $agentName;

            $arrayAux[] = $arrayAgent;
        }

        //Sort smallest to biggest
        usort($arrayAux, function ($a, $b) {

            if ($a["agentNumber"] == $b["agentNumber"]) {
                return 0;
            }

            return ($a["agentNumber"] > $b["agentNumber"]) ? 1 : -1;
        });

        $params['response'] = $arrayAux;
        $params['message'] = "";
        $asm->disconnect();
        return $params;
    }

    /**
     * Trae el DNIS desde el evento de la llamada
     * juan.vallejos - 2016 <vallejosfj@gmail.com>
     * @param type $acd
     * @return type
     */
    public function getDNISPbx($acd) {

        $agente = "Agent/" . $acd;
        $asm = $this->__getConnection();
        $resp = $asm->Command("core show channel $agente");

        $resp2 = explode("Caller ID:", $resp['data']);

        $resp3 = explode("\n", $resp2[1]);

        $dnis = trim($resp3[0]);
        $asm->disconnect();
        return $dnis;
    }

    public function cortarSaliente($acd) {

        $agente = "Agent/" . $acd;
        $asm = $this->__getConnection();
        $resp = $asm->Command("core show channel $agente");

        $resp2 = explode("level 1: channel=", $resp['data']);
        if (count($resp2) == 1) {
            $canal = "";
        } else {
            $resp3 = explode("\n", $resp2[1]);

            $canal = trim($resp3[0]);
        }

        if ($canal != "") {
            $respuesta = $asm->Hangup($canal);
            $asm->disconnect();
        } else {
            $respuesta = FALSE;
        }
        return $respuesta;
    }

    public function cortarSipSaliente($éxtension) {

        $sip = "sip/" . $éxtension;
        $asm = $this->__getConnection();
        $resp = $asm->Command("core show channels concise");
        $r = $resp['data'];

        $resp2 = in_array("sip/1003", $resp['data']);
        foreach ($resp2 as $key => $value) {
            $d = $key;
            $c = $value;
        }
        if (count($resp2) == 1) {
            $canal = "";
        } else {
            $resp3 = explode("\n", $resp2[1]);

            $canal = trim($resp3[0]);
        }


        $respuesta = $asm->Hangup($canal);
        $asm->disconnect();
        return $respuesta;
    }

    private function __getChannelOutbound($acd) {

        $agente = "Agent/" . $acd;
        $asm = $this->__getConnection();
        $resp = $asm->Command("core show channel $agente");

        $resp2 = explode("level 1: dstchannel=", $resp['data']);

        $resp3 = explode("\n", $resp2[1]);

        $channel = trim($resp3[0]);
        $asm->disconnect();
        return $channel;
    }

    public function getUniqueid($acd) {

        $agente = "Agent/" . $acd;
        $asm = $this->__getConnection();
        $resp = $asm->Command("core show channel $agente");
        $respuesta = explode("Privilege:", $resp['data']);
        $r = strpos($respuesta[1], "is not a known channel");
        if ($r == "21") {
            $uniqueid = FALSE;
        } else {
            $resp2 = explode("UniqueID:", $resp['data']);

            $resp3 = explode("\n", $resp2[1]);

            $uniqueid = trim($resp3[0]);
        }
        $asm->disconnect();
        return $uniqueid;
    }

    private function __transferirAcola($numeroAgente, $channelA, $destino, $prefijoSalida, $contexto = "from-internal") {

//        if ($numeroAgente === "") {
//            throw new \InvalidArgumentException("Missing parameter: numeroAgente");
//        }

        if ($destino === "") {
            throw new \InvalidArgumentException("Missing parameter: destino");
        }

        if ($contexto === "") {
            throw new \InvalidArgumentException("Missing parameter: contexto");
        }

        //Clean destinity number
        $destino = $this->__cleanDestinity($destino);

        $asm = $this->__getConnection();

        $params = array(
            'channel' => "Agent/" . $numeroAgente, //Agent channel
            'context' => $contexto,
            'exten' => $prefijoSalida . "" . $destino);

        /**
         * Verify if there're a call in course.
         */
        $channelB = $this->__getChannelOfTransferB($asm, $numeroAgente);
        //$channelA = $this->__getChannelOfTransferA($asm, $numeroAgente);
        if ($channelB === "") {
            $params['response'] = "REQUIRED_CALL"; //Success, Error
            $params['message'] = "There isn't a call in course.";
            $asm->disconnect();
            return $params;
        }

        $params['channelA'] = $channelA;

        $extrachannel = "";
        $priority = 1;

        /**
         * This generate a call between Agent and B. 
         * (Links channel A and channel B).
         */
        $call = $asm->Redirect(
                $params['channel'], $extrachannel, $params['exten'], $params['context'], $priority
        );

        //Returned values
        $params['response'] = $call["Response"]; //Success, Error
        $params['message'] = $call["Message"];

        if ($params['response'] === "Error") {
            $asm->disconnect();
            return $params;
        }
        $asm->disconnect();
        return $params;
    }

    /**
     * 
     * @param type $ip_asterisk
     * @param type $user_asterisk
     * @param type $pass_asterisk
     * @return \AGI_AsteriskManager
     */
    private function __getConnection() {

        $asm = new \AGI_AsteriskManager();

        $conn = $asm->connect($this->IP_ASTERISK, $this->USER_ASTERISK, $this->PASSW_ASTERISK);

        //Connection: Error
        if ($conn !== TRUE) {
            throw new \Exception("There was a problem establishing connection!");
        }

        return $asm;
    }

    /**
     * 
     * @param \skytel\asteriskPBX\type $asm
     * @param type $numeroAgente
     * @return type
     */
    private function __getExtension($asm, $numeroAgente) {

        $extension = "";

        $resp = $asm->Command("agent show online");

        foreach (explode("\n", $resp['data']) as $line) {

            $numeroAgenteAux = $this->__getToken($line, 0);
            $extensionAux = $this->__getToken($line, 4);
            $extensionAux = $this->__getToken($line, 0, "-");

            if ($numeroAgente == "" || $numeroAgenteAux == "") {
                continue;
            }

            if ($numeroAgente == $numeroAgenteAux) {
                $extension = $extensionAux;
                break;
            }
        }

        return $extension;
    }

    /**
     * 
     * @param \skytel\asteriskPBX\type $asm
     * @param type $numeroAgente
     * @return If there isn't a call in course we return empty channel. 
     *       Otherwise, we return name of channel.
     */
    private function __getChannelOfTransferA($asm, $numeroAgente) {

        $channel = "";
        $status = "";

        $resp = $asm->Command("agent show online");

        /**
         * Expected:
         * 1009 (Prueba Desarrollo) logged in on SIP/1613-000006e3 talking to SIP/GWPY-00000815 (musiconhold is 'silencioooo')
         */
        foreach (explode("\n", $resp['data']) as $line) {

            $numeroAgenteAux = $this->__getToken($line, 0);
            $status = $this->__getToken($line, 5);
            $channelAux = $this->__getToken($line, 7);

//            var_dump($numeroAgenteAux);
//            var_dump($status);
//            var_dump($channelAux);

            if ($numeroAgente == "" || $numeroAgenteAux == "") {
                continue;
            }

            if ($numeroAgente == $numeroAgenteAux) {
                $channel = $channelAux;
                break;
            }
        }

//        var_dump($status);
//        var_dump($channel);
        /**
         * If there isn't a call in course we return empty channel. 
         * Otherwise, we return name of channel.
         */
        if ($status !== "talking") {
            $channel = "";
        }

        return $channel;
    }

    /**
     * 
     * @param type $asm
     * @param type $numeroAgente
     * @return type
     */
    private function __getChannelOfTransferB($asm, $numeroAgente) {

        $channel = "";

        $resp = $asm->Command("agent show online");

        foreach (explode("\n", $resp['data']) as $line) {

            $numeroAgenteAux = $this->__getToken($line, 0);
            $channelAux = $this->__getToken($line, 7);

            if ($numeroAgente == "" || $numeroAgenteAux == "") {
                continue;
            }

            if ($numeroAgente == $numeroAgenteAux) {
                $channel = $channelAux;
                break;
            }
        }

        return $channel;
    }

    /**
     * 
     * @param type $destino
     * @return type
     */
    private function __cleanDestinity($destino) {

        $telefono = "";

        //Eliminar cualquier caracter, espacio, no numérico
        $telefono = preg_replace("/[^0-9]/", "", $destino);

        return $telefono;
    }

    /**
     * 
     * @param type $numeroAgente
     * @param type $channelA
     */
    private function __getStatusOfTransfer($asm, $numeroAgente, $channelA) {

        //Tiempo para que comienze la conexion con el punto B.
        sleep(5); //seconds

        /*
          //Mientras B NO atiende la llamada, o se corta la conexion
          1009 (Prueba Desarrollo) logged in on SIP/1613-000006e3 is idle (musiconhold is 'silencioooo')

          //Llamada recibida de A. (Talking y el canal A)
          1009 (Prueba Desarrollo) logged in on SIP/1613-000006e3 talking to SIP/GWPY-000007f6 (musiconhold is 'silencioooo')

          //Llamada atendida por B (Talking y el canal B)
          1009 (Prueba Desarrollo) logged in on SIP/1613-000006e3 talking to Local/8985787401@from-internal-00000026;1 (musiconhold is 'silencioooo')
         */
        $status = "UNANSWERED";
        for ($loops = 0; $loops < 20; $loops++) {

            $resp = $asm->Command("agent show online");
//        var_dump($loops);

            foreach (explode("\n", $resp['data']) as $line) {

                $numeroAgenteAux = $this->__getToken($line, 0);
                $status = $this->__getToken($line, 5);

                //Ignore lines that don't start with number of agent.
                if ($numeroAgente == "" || $numeroAgenteAux == "") {
                    continue;
                }

                if ($numeroAgente === $numeroAgenteAux && $status === "talking") {
                    $channelAux = $this->__getToken($line, 7);

                    //return the call to A. 
                    if ($channelA == $channelAux) {
                        $status = "UNANSWERED";
                    } else {
                        $status = "ANSWERED";
                    }

                    return $status;
                }
            }

            sleep(1); //seconds
        }

        return $status;
    }

    /**
     * This function extract element of array, and avoid warning.
     * 
     * The first token is in position 0.
     * 
     * So, delete the name of agent before get the token. That was necesary because
     * the name of agent has variable lenght. May be 2, 3, o 4 tokens or more.
     * 
     * I.E
     * 
     * 1009         (Prueba Desarrollo xx) logged in on SIP/1613-00000fb9 is idle (musiconhold is 'silencioooo')
     * 
     * result
     * 
     * '1009 logged in on SIP/1613-00000fb9 is idle (musiconhold is 'silencioooo')'
     * 
     * @param type $txt
     * @param type $position
     * @param type $separator
     * @return type
     */
    private function __getToken($txt, $position, $separator = " ") {

        $txtAux = $this->__deleteAllBetween('(', ')', $txt);

        //Convert multiple spaces in single space. 
        $txtAux = trim(preg_replace('/\s+/', " ", $txtAux));

        $aux = explode($separator, $txtAux);
        return ((isset($aux[$position])) ? $aux[$position] : "");
    }

    /**
     * 
     * @param type $string
     * @return string
     */
    private function __getNameToken($string) {

        $BEGIN_CHARACTER = "(";
        $END_CHARACTER = ")";

        $beginningPos = strpos($string, $BEGIN_CHARACTER);
        $endPos = strpos($string, $END_CHARACTER);

        if ($beginningPos === false || $endPos === false) {
            return "";
        }

        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($END_CHARACTER)) - $beginningPos);

        //Output Format 
        $textToDelete = str_replace($BEGIN_CHARACTER, '', $textToDelete);
        $textToDelete = str_replace($END_CHARACTER, '', $textToDelete);

        $textToDelete = ucwords(strtolower($textToDelete));

        return $textToDelete;
    }

    /**
     * http://stackoverflow.com/questions/13031250/php-function-to-delete-all-between-certain-characters-in-string
     * 
     * @param type $beginning
     * @param type $end
     * @param type $string
     * @return type
     */
    private function __deleteAllBetween($beginning, $end, $string) {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);

        if ($beginningPos === false || $endPos === false) {
            return $string;
        }

        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

        return str_replace($textToDelete, '', $string);
    }

    /**
     * 
     * @param type $channelA
     * @param type $channelB
     * @return string
     */
    private function __areLinkedAB($channelA, $channelB) {

        //Tiempo para que enlace los canales.
        sleep(2);

        $asm = $this->__getConnection();

        $resp = $asm->Command("core show channels concise");

        /**
         * Channel!Context!Extension!Prio!State!Application!Data!CallerID!Duration!Accountcode!PeerAccount!BridgedTo
         * 
         * Agente conectado Expected:
         * SIP/1613-0000001b!from-internal!*88881009!3!Up!AgentLogin!1009!1613!Austral!!3!7999!(None)!1442584812.2065
         * 
         * Agente recibe una llamada:
         * SIP/1613-0000001b!from-internal!*88881009!3!Up!AgentLogin!1009!1613!Austral!!3!8164!IAX2/Trunk_Gateway-3393!1442584812.2065
         * Agent/1009!from-internal!4198227!1!Up!AppQueue!(Outgoing Line)!4198227!!!3!4!IAX2/Trunk_Gateway-3393!1442592972.4176
         * 
         * Agente habla con B
         * SIP/1613-0000001b!from-internal!*88881009!3!Up!AgentLogin!1009!1613!Austral!!3!8246!Local/8985787401@from-internal-00000155;1!1442584812.2065
         * Agent/1009!from-internal!4198227!1!Up!AppQueue!(Outgoing Line)!4198227!!!3!86!Local/8985787401@from-internal-00000155;1!1442592972.4176
         * 
         * Agente acepta la transferencia:
         * SIP/1613-0000001b!from-internal!*88881009!3!Up!AgentLogin!1009!1613!Austral!!3!8341!(None)!1442584812.2065
         * 
         * Verifico por Channel A o Channel B
         * Channel A IAX2/Trunk_Gateway-3393
         * Channel B Local/8985787401@from-internal-00000155;1
         * 
         * //asterisk -rx "core show channels concise" | grep IAX2/Trunk_Gateway-3393
         * IAX2/Trunk_Gateway-3393!ext-queues!4198227!10!Up!Transferred Call!Local/8985787401@from-internal-00000155;1!3110!!!3!352!Local/8985787401@from-internal-00000155;1!1442593149.4201
         * Local/8985787401@from-internal-00000155;1!from-internal!8985787401!1!Up!Transferred Call!IAX2/Trunk_Gateway-3393!!!!3!282!IAX2/Trunk_Gateway-3393!1442593042.4188
         * 
         * //asterisk -rx "core show channels concise" | grep "Local/8985787401@from-internal-00000155;1"
         * IAX2/Trunk_Gateway-3393!ext-queues!4198227!10!Up!Transferred Call!Local/8985787401@from-internal-00000155;1!3110!!!3!393!Local/8985787401@from-internal-00000155;1!1442593149.4201
         * Local/8985787401@from-internal-00000155;1!from-internal!8985787401!1!Up!Transferred Call!IAX2/Trunk_Gateway-3393!!!!3!323!IAX2/Trunk_Gateway-3393!1442593042.4188
         *
         */
        foreach (explode("\n", $resp['data']) as $line) {

            /**
             * Get values
             */
            $lineArray = explode("!", $line);
            $channelAAux = isset($lineArray[0]) ? $lineArray[0] : "";
            $channelAStatus = isset($lineArray[5]) ? $lineArray[5] : "";

            //Reviso contra el canal A
            if ($channelAAux === $channelA && $channelAStatus === "Transferred Call") {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * 
     * @param type $numeroAgente
     * @param type $channelAgent
     */
    private function __getStatusOfCall($asm, $numeroAgente) {

        //Tiempo para que comienze la conexion con el punto B.
        sleep(5); //seconds

        /*
          //Mientras B NO atiende la llamada, o se corta la conexion
          1009 (Prueba Desarrollo) logged in on SIP/1613-000006e3 is idle (musiconhold is 'silencioooo')

          //Llamada generada por agente hacia A. (Talking y el canal A)
          1009 (Prueba Desarrollo) logged in on SIP/1613-000006e3 talking to SIP/GWPY-000007f6 (musiconhold is 'silencioooo')
         */
        for ($loops = 0; $loops < 20; $loops++) {

            $resp = $asm->Command("agent show online");

            foreach (explode("\n", $resp['data']) as $line) {

                $numeroAgenteAux = $this->__getToken($line, 0);
                $status = $this->__getToken($line, 5);

                //Ignore lines that don't start with number of agent.
                if ($numeroAgente == "" || $numeroAgenteAux == "") {
                    continue;
                }

                if ($numeroAgente === $numeroAgenteAux && $status === "talking") {
                    return "ANSWERED";
                }
            }

            sleep(1); //seconds
        }

        return "UNANSWERED";
    }

}

?>