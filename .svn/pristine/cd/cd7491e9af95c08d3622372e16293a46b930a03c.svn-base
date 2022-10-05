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

class RegistroEntrantes {

    private $uniqueId;
    private $agentNumber;
    private $date;
    private $state;
    private $txt;
    private $idPlatform;
    private $DNIS;
    private $ANI;
    private $errorMessage;
    private $lastInsertedIdRegistro;
    private $lastInsertedIdEntrantes;
    private $IP;
    private $USER;
    private $PASS;
    private $DBNAME;

    /**
     * Constructor
     * 
     * @param type $ip
     * @param type $dbname
     * @param type $user
     * @param type $pass
     * @throws \InvalidArgumentException
     */
    public function __construct($ip, $dbname, $user, $pass = "") {

        //Validations
        if (empty($ip)) {
            throw new \InvalidArgumentException("Missing parameter DB IP Address.");
        }
        
        if (empty($user)) {
            throw new \InvalidArgumentException("Missing parameter DB User.");
        }
        
        if (empty($dbname)) {
            throw new \InvalidArgumentException("Missing parameter DB Name.");
        }
        
        $this->IP = $ip;
        $this->USER = $user;
        $this->PASS = $pass;
        $this->DBNAME = $dbname;
        
        $this->uniqueId = "";
        $this->agentNumber = "";
        $this->date = "";
        $this->state = "";
        $this->txt = [];
        $this->idPlatform = "";
        $this->DNIS = "";
        $this->ANI = "";
        
        $this->errorMessage = "";
        $this->lastInsertedIdRegistro = "";
        $this->lastInsertedIdEntrantes = "";
        
    }

    /**
     * Sets unique id.
     * 
     * @param String $uniqueId
     */
    public function setUniqueId($uniqueId) {
        $this->uniqueId = $uniqueId;
    }

    /**
     * Sets date.
     * 
     * @param String $date
     */
    public function setDate($date) {
        $this->date = $date;
    }

    /**
     * Sets Tipificación
     * 
     * @param String $tipificacion
     */
    public function setState($tipificacion) {
        $this->state = $tipificacion;
    }

    /**
     * Sets Agent Number
     * 
     * @param String $agente
     */
    public function setAgentNumber($agente) {
        $this->agentNumber = $agente;
    }

    /**
     * Sets DNIS Number
     * 
     * @param String $DNIS
     */
    public function setDNIS($DNIS) {
        $this->DNIS = $DNIS;
    }

    /**
     * Sets ANI Number
     * 
     * @param String $ANI
     */
    public function setANI($ANI) {
        $this->ANI = $ANI;
    }

    /**
     * Sets platform id.
     * 
     * @param Numeric $platform
     */
    public function setPlatform($platform) {

        $this->idPlatform = $platform;
    }

    /**
     * It's under development
     */
    public function addStep() {
        
    }

    /**
     * 
     * @return String
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * 
     * @return Int
     */
    public function getLastInsertedIdRegistro() {
        return $this->lastInsertedIdRegistro;
    }

    /**
     * 
     * @return Int
     */
    public function getLastInsertedIdEntrantes() {
        return $this->lastInsertedIdEntrantes;
    }

    /**
     * Inserts data into Calidad's DB.
     * 
     * Required fields:
     * 
     *   uniqueId
     *   agentNumber
     *   DNIS
     *   idPlatform
     * 
     * Optional parameters
     *   date (default = NOW)
     *   state (default = N/A)
     *   ANI (default = N/A)
     *   TXT (default = N/A)
     * 
     * @throws InvalidArgumentException
     */
    public function saveDataEntrantes() {

        //Validations
        if (empty($this->uniqueId)) {
            throw new \InvalidArgumentException("Missing parameter Unique Id.");
        }

        if (empty($this->agentNumber)) {
            throw new \InvalidArgumentException("Missing parameter Agent Number.");
        }

        if (empty($this->DNIS)) {
            throw new \InvalidArgumentException("Missing parameter DNIS Number.");
        }

        if (empty($this->idPlatform)) {
            throw new \InvalidArgumentException("Missing parameter Platform Id.");
        }

        if (empty($date)) {
            $date = date("Y-m-d H:i:s");
        }

        if (empty($state)) {
            $state = "N/A";
        }

        if (empty($ANI)) {
            $ANI = "N/A";
        }

        if (empty($this->txt)) {
            $this->txt = 'N/A';
        }

        $flag = FALSE;
        $errorMessages = [];        
        
        $conn = $this->__getConnection();

        //Validation
        if ($conn === FALSE) {
            throw new \Exception($this->getErrorMessage());
        }

        $this->state = mysqli_real_escape_string($conn, $this->state);
        $this->txt = mysqli_real_escape_string($conn, $this->txt);

        $conn->autocommit(FALSE);

        $sql = "insert into registro (uniqueId,nroAgente,fecha,tipificacion,txt,idPlataforma) values ";
        $sql .= "('$this->uniqueId','$this->agentNumber','$this->date','$this->state','$this->txt','$this->idPlatform')";
        $rs = $conn->query($sql);

        if ($rs) {
            $this->lastInsertedIdRegistro = $conn->insert_id;
        } else {
            $errorMessages[] = "- " . $conn->error;
            $flag = TRUE;
        }

        $sql = "insert into entrantes (DNIS,ANI,idRegistro) values ";
        $sql .= "('$this->DNIS','$this->ANI',$this->lastInsertedIdRegistro)";
        $rs = $conn->query($sql);

        if ($rs) {
            $this->lastInsertedIdEntrantes = $conn->insert_id;
        } else {
            $errorMessages[] = "- " . $conn->error;
            $flag = TRUE;
        }

        if (!$flag) {
            $conn->commit();
            mysqli_close($conn);

            return TRUE;
        } else {
            $conn->rollback();
            $this->__errorReg($conn, $errorMessages);
            mysqli_close($conn);

            return FALSE;
        }
    }

    /**
     * Returns data from Calidad's DB with array format.
     * 
     * @param Numeric $agente
     * @param timestamp $fechaDesde i.e.: 'yyyy-mm-dd hh:mm:ss'
     * @param timestamp $fechaHasta i.e.: 'yyyy-mm-dd hh:mm:ss'
     * @return array
     */
    public function getDataEntrantes($agente, $fechaDesde, $fechaHasta, $DNIS) {

        $conn = $this->__getConnection();

        //Validation
        if ($conn === FALSE) {
            throw new \Exception($this->getErrorMessage());
        }

        $sql = "select r.id, r.uniqueId, r.fecha, r.tipificacion "
                . "from registro as r "
                . "join entrantes as e on r.id = e.idRegistro "
                . "where r.nroAgente = '$agente' "
                . "and e.DNIS in ($DNIS) "
                . "and r.fecha between '$fechaDesde' and '$fechaHasta'";

        $rs = $conn->query($sql);

        if (!$rs) {
            $this->errorMessage = "Error: " . $conn->error;
            return FALSE;
        }

        $arrayAux = [];
        while ($row = $rs->fetch_object()) {
            $arrayAux[] = $row;
        }

        mysqli_close($conn);

        return $arrayAux;
    }

    /**
     * 
     * @param connexion $conn
     * @param array $errorMessages
     * @return type
     */
    private function __errorReg($conn, $errorMessages) {

        $errores = "";

        $transData = [
            'uniqueId' => $this->uniqueId,
            'nroAgente' => $this->agentNumber,
            'fecha' => $this->date,
            'tipificacion' => $this->state,
            'txt' => $this->txt,
            'idPlataforma' => $this->idPlatform,
            'DNIS' => $this->DNIS,
            'ANI' => $this->ANI,
            'idRegistro' => $this->lastInsertedIdRegistro,
            'idEntrantes' => $this->lastInsertedIdEntrantes];
        $jtransData = json_encode($transData, JSON_UNESCAPED_UNICODE);

        $i = 0;

        foreach ($errorMessages as $errorMessage) {
            $i++;
            $errores .= "Error(" . $i . "): " . $errorMessage . "\n";
        }

        $sql = "insert into errores "
                . "(errorMessage,transaction) "
                . "values ("
                . "'" . mysqli_real_escape_string($conn, $errores) . "',"
                . "'" . mysqli_real_escape_string($conn, $jtransData) . "'"
                . ")";
        $result = $conn->query($sql);
        $conn->commit();

        $this->errorMessage = $errores;

        return;
    }

    /**
     * 
     * @return mySQLi connection 
     */
    private function __getConnection() {
        $conn = new \mysqli($this->IP, $this->USER, $this->PASS, $this->DBNAME);

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
