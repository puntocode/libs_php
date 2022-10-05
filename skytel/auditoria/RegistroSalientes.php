<?php

namespace skytel\auditoria;

/**
 * Description of RegistrosSalientes
 *
 * @author Daniel Cazal
 * 
 * $Id: GetCallDetail.php 12547 2015-02-19 13:05:07Z endrigo $
 */

/**
 * Description of RegistroSalientes
 * Required fields:
 * - DB IP address
 * - DB Name
 * - DB User
 * - uniqueId
 * - nroAgente
 * - date
 * - state
 * - idCliente
 * - idCampana
 * - idPlatform
 */
class RegistroSalientes {

    private $uniqueId;
    private $nroAgente;
    private $date;
    private $state;
    private $txt;
    private $idPlatform;
    private $idCliente;
    private $idCampana;
    private $errorMessage;
    private $lastInsertedIdRegistro;
    private $lastInsertedIdSalientes;
    private $IP;
    private $USER;
    private $PASS;
    private $DBNAME;
    private $tipoTipificacion;
    private $Cola;
    private $NroTelefono;
    private $IdGestion;

    public function __construct($ip, $dbname, $user, $pass = "") {
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
        $this->nroAgente = "";
        $this->date = "";
        $this->state = "";
        $this->txt = [];
        $this->idPlatform = "";
        $this->errorMessage = "";
        $this->lastInsertedIdRegistro = "";
        $this->lastInsertedIdSalientes = "";
        $this->idCliente = "";
        $this->idCampana = "";
        $this->tipoTipificacion = "0";

        $this->Cola = 0;
        $this->NroTelefono = "";
        $this->IdGestion = 0;
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
        $this->nroAgente = $agente;
    }

    /**
     * Sets IdCliente Number
     * 
     * @param String $idcliente
     */
    public function setIdCliente($idcliente) {
        $this->idCliente = $idcliente;
    }

    /**
     * Sets IdCampana Number
     * 
     * @param String $idcampana
     */
    public function setIdCampana($idcampana) {
        $this->idCampana = $idcampana;
    }

    public function setTipoTipificacion($tipoTipificacion) {
        $this->tipoTipificacion = $tipoTipificacion;
    }

    public function setCola($cola) {
        $this->Cola = $cola;
    }

    public function setNroTelefono($nroTelefono) {
        $this->NroTelefono = $nroTelefono;
    }

    public function setIdGestion($idGestion) {
        $this->IdGestion = $idGestion;
    }

    public function addStep() {
        
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

    /**
     * Inserts data into Calidad's DB.
     * 
     * @throws InvalidArgumentException
     */
    public function saveDataSalientes() {
        $flag = FALSE;
        $errorMessages = [];
        if (empty($this->uniqueId)) {
            throw new \InvalidArgumentException("Missing parameter Unique Id.");
        }
        if (empty($this->nroAgente)) {
            throw new \InvalidArgumentException("Missing parameter Agent Number.");
        }
        if (empty($this->date)) {
            throw new \InvalidArgumentException("Missing parameter Date.");
        }
        if (empty($this->state)) {
            throw new \InvalidArgumentException("Missing parameter Call State.");
        }
        if (empty($this->idPlatform)) {
            throw new \InvalidArgumentException("Missing parameter Platform Name.");
        }
        if (empty($this->idCliente)) {
            throw new \InvalidArgumentException("Missing parameter Id Cliente.");
        }
        if (empty($this->idCampana)) {
            throw new \InvalidArgumentException("Missing parameter Id Campaña.");
        }
        if ($this->tipoTipificacion === "") {
            throw new \InvalidArgumentException("Missing parameter Tipo de Tipificacion. -> " . $this->tipoTipificacion);
        }

        if ($this->Cola === 0) {
            throw new \InvalidArgumentException("Missing parameter Cola. -> " . $this->Cola);
        }
        if ($this->NroTelefono === "") {
            throw new \InvalidArgumentException("Missing parameter Número de Telefono. -> " . $this->NroTelefono);
        }
        if ($this->IdGestion === 0) {
            throw new \InvalidArgumentException("Missing parameter ID Gestión. -> " . $this->IdGestion);
        }

        if (empty($this->txt)) {
            $this->txt = '';
        }

        $conn = $this->__getConnection();

        //Validation
        if (!$conn) {
            throw new \Exception($this->getErrorMessage());
        }

        $this->state = mysqli_real_escape_string($conn, $this->state);
        $this->txt = mysqli_real_escape_string($conn, $this->txt);

        $conn->autocommit(FALSE);

        $sql = "insert into registro (uniqueId,nroAgente,fecha,tipificacion,txt,idPlataforma,tipoTipificacion,idGestion) values ";
        $sql .= "('$this->uniqueId','$this->nroAgente','$this->date','$this->state','$this->txt','$this->idPlatform','$this->tipoTipificacion','$this->IdGestion')";
        $rs = $conn->query($sql);

        if ($rs) {
            $this->lastInsertedIdRegistro = $conn->insert_id;
        } else {
            $errorMessages[] = $conn->error;
            $flag = TRUE;
        }

        $sql = "insert into salientes (idCliente,idCampana,idRegistro,nroTelefono,colaCampana) values ";
        $sql .= "('$this->idCliente','$this->idCampana','$this->lastInsertedIdRegistro','$this->NroTelefono','$this->Cola')";
        $rs = $conn->query($sql);

        if ($rs) {
            $this->lastInsertedIdSalientes = $conn->insert_id;
        } else {
            $errorMessages[] = $conn->error;
            $flag = TRUE;
        }

        if (!$flag) {
            $conn->commit();
            return TRUE;
        } else {
            $conn->rollback();
            $this->__errorReg($conn, $errorMessages);
            return FALSE;
        }
        
         mysqli_close($conn);
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
     * Returns data from Calidad's DB with array format.
     * 
     * @param Numeric $agente
     * @param timestamp $fechaDesde i.e.: 'yyyy-mm-dd hh:mm:ss'
     * @param timestamp $fechaHasta i.e.: 'yyyy-mm-dd hh:mm:ss'
     * @return array
     */
    public function getDataSalientes($agente, $fechaDesde, $fechaHasta, $idCliente, $idCampana) {
        $conn = $this->__getConnection();

        //Validation
        if ($conn === FALSE) {
            throw new \Exception($this->getErrorMessage());
        }

        $sql = "select r.id, uniqueId, fecha, tipificacion, r.idGestion "
                . "from registro as r "
                . "join salientes as s on r.id = s.idRegistro "
                . "where nroAgente = '$agente' "
                . "and s.idCliente = '$idCliente' "
                . "and s.idCampana = '$idCampana'  "
                . "and fecha between '$fechaDesde' and '$fechaHasta' ";

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
        //$conn->close;
        return $arrayAux;
    }

    /**
     * Returns data from Calidad's DB with array format.
     * 
     * @param Numeric $agente
     * @param timestamp $fechaDesde i.e.: 'yyyy-mm-dd hh:mm:ss'
     * @param timestamp $fechaHasta i.e.: 'yyyy-mm-dd hh:mm:ss'
     * @return array
     */
    public function getDataVentasRealesSalientes($fechaDesde, $fechaHasta, $idCliente, $idCampana) {
        $conn = $this->__getConnection();

        //Validation
        if ($conn === FALSE) {
            throw new \Exception($this->getErrorMessage());
        }
        $sql = "select r.id, r.uniqueId, r.idGestion, r.fecha, r.tipificacion, s.nroTelefono, s.colaCampana "
                . "from registro as r "
                . "join salientes as s on r.id = s.idRegistro "
                . "where s.idCliente = '$idCliente' "
                . "and s.idCampana = '$idCampana'  "
                . "and r.tipoTipificacion = 1 "
                . "and r.fecha between '$fechaDesde' and '$fechaHasta' ";

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
        //$conn->close;
        return $arrayAux;
    }

    public function getErrorMessage() {
        return $this->errorMessage;
    }

    public function getLastInsertedIdRegistro() {
        return $this->lastInsertedIdRegistro;
    }

    public function getLastInsertedIdSalientes() {
        return $this->lastInsertedIdSalientes;
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
            'nroAgente' => $this->nroAgente,
            'fecha' => $this->date,
            'tipificacion' => $this->state,
            'txt' => $this->txt,
            'idPlataforma' => $this->idPlatform,
            'idCliente' => $this->idCliente,
            'idCampana' => $this->idCampana,
            'idRegistro' => $this->lastInsertedIdRegistro,
            'idSalientes' => $this->lastInsertedIdSalientes];
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

}
