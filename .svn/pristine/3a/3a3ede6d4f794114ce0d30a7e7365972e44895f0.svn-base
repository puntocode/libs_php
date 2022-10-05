<?php

/**
 * [MULTIPRODUCTOS] , Skytel. All Rights Reserved. [2015].
 *
 * @author Daniel Cazal
 * 
 * $Id: router.php 13138 2015-03-30 16:32:33Z endrigo $
 */
?>
<?php

namespace skytel\auditoria;

require_once dirname(__FILE__) . '/config/config.php';
require_once 'skytel/auditoria/RegistroEntrantes.php';

class RegistroEntrantesWrapper {

    /**
     * If uniqueId is empty, it returns inmediatelly.
     * 
     * 
     * @param type $uniqueId [obligatory]
     * @param type $agentNumber [obligatory]
     * @param type $DNIS [obligatory]
     * @param type $idPlatform [obligatory]
     * @param type $date
     * @param type $state
     * @param type $ANI
     * @return Array Array with 2 keys 'response' and 'success'. Success may be
     *               TRUE, FALSE, NONE.  NONE means that Uniqueid is missing.
     */
    public function guardar($uniqueId, $agentNumber, $DNIS, $idPlatform, $date = "NOW", $state = "N/A", $ANI = "N/A") {

        //Datos para prueba
/*
        $uniqueId = 123;
        $agentNumber = 1009;
//        $DNIS = 666556;
        $DNIS = "";
        $idPlatform = "2";
        $date = "";
        $state = "";
        $ANI = "";
*/        
        //Start time
        $fechaRecibido = date("Y-m-d H:i:s");

        $qualityPlatform = new \skytel\auditoria\RegistroEntrantes(IP_DB_AUDITORIA, NAME_DB_AUDITORIA, USER_DB_AUDITORIA, PASS_DB_AUDITORIA);
        $qualityPlatform->setUniqueId($uniqueId);
        $qualityPlatform->setAgentNumber($agentNumber);
        $qualityPlatform->setDNIS($DNIS);
        $qualityPlatform->setPlatform($idPlatform);
        $qualityPlatform->setDate($date);
        $qualityPlatform->setState($state);
        $qualityPlatform->setANI($ANI);

        $resultado = $this->__saveData($uniqueId, $qualityPlatform);

        //Finish time
        $fechaResultado = date("Y-m-d H:i:s");

        $recibidos = [
            "uniqueId" => $uniqueId,
            "agentNumber" => $agentNumber,
            "DNIS" => $DNIS,
            "ANI" => $ANI,
            "idPlatform" => $idPlatform,
            "date" => $date,
            "state" => $state
        ];

        $this->__saveHistorial($recibidos, $fechaRecibido, $resultado, $fechaResultado);
        
        return $resultado;
    }

    /**
     * 
     * @param type $uniqueId
     * @param type $qualityPlatform
     * @return type
     */
    private function __saveData($uniqueId, $qualityPlatform) {

        //Validation
        if (empty($uniqueId)) {
            return [
                'response' => "Operacion cancelada porque no se recibio uniqueId!",
                'success' => "NONE"
            ];
        }

        try {

            $response = [
                'response' => "Transacción exitosa!",
                'success' => NULL
            ];

            $result = $qualityPlatform->saveDataEntrantes();
            if ($result === FALSE) {
                $response['response'] = $qualityPlatform->getErrorMessage();
            }

            $response['success'] = $result;

            return $response;
        } catch (\Exception $ex) {

            return [
                'response' => $ex->getMessage(),
                'success' => FALSE
            ];
        }
    }

    /**
     * 
     * @param type $recibidos
     * @param type $fechaRecibido
     * @param type $resultado
     * @param type $fechaResultado
     */
    private function __saveHistorial($recibidos, $fechaRecibido, $resultado, $fechaResultado) {

        
        $recibidosAux = json_encode($recibidos, JSON_UNESCAPED_UNICODE);
        $resultadoAux = json_encode($resultado, JSON_UNESCAPED_UNICODE);
        
        $conn = $this->__getConnection();

        $sql = "insert into datos_procesados "
                . "(datos_recibidos, fecha_recibido, datos_resultado, fecha_resultado) "
                . "values ("
                . "'$recibidosAux',"
                . "'$fechaRecibido',"
                . "'$resultadoAux',"
                . "'$fechaResultado'"
                . ")";

        $result = $conn->query($sql);

        mysqli_close($conn);
    }

    /**
     * 
     * @return mySQLi connection 
     */
    private function __getConnection() {

        $conn = new \mysqli(IP_DB_AUDITORIA, USER_DB_AUDITORIA, PASS_DB_AUDITORIA, NAME_DB_AUDITORIA);

        if (!$conn) {
            $this->errorMessage = "Error: " . $conn->error;
            return FALSE;
        }

        if (!mysqli_set_charset($conn, "UTF8")) {
            $this->errorMessage = "Error: " . $conn->error;
            return FALSE;
        }

        return $conn;
    }

}
