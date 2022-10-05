<?php

/**
 * [PANTALLAS_STAR] , Skytel. All Rights Reserved. [2013].
 *
 * @author Endrigo Rivas <endrigo.rivas@gmail.com>
 * 
 * $Id: TipificarLlamada.php 13698 2015-05-21 20:00:45Z endrigo $
 */
?>
<?php

namespace skytel\auditoria;

//Requires
require_once dirname(__FILE__) . '/config/config.php';

class TipificarLlamada {

    private $errorMessage;

    /**
     * 
     * @param String $uniqueId
     * @param int $idTipoTipif //Archivo de configuracion, Ej: TICKETS_QUEJAS_GENERALES 
     * @param int $idRelacional //El id de la insercion/queja/consulta.
     * @param int $id_dnis // DNIS
     * @param String $callerId
     * @param String $numeroAgente
     * @param String $nombreAgente
     * @throws \Exception
     */
    public function open($uniqueId, $id_dnis, $callerId = "-1", $numeroAgente = "-1", $nombreAgente = "N/A", $id_usuario_sso = NULL)
    {
       // var_dump($uniqueId);
      //  DIE('EN open');
        $conn = $this->__getConnection();
     
        if ($conn === FALSE) {
            throw new \Exception("Error al conectar con la base de datos.");
        }
        /**
         * Datos predefinidos
         */
        $fecha = date("Y-m-d H:i:s");
      
        $sql = "INSERT INTO tipificacion_llamadas ( "
                . " uniqueid,"
                . " fecha,"
                . " numero_agente,"
                . " nombre_agente,"
                . " callerid,"
                . " id_dnis,"                
                . " msg_tipificacion,"
                . " id_relacional,"
                . " extra_close,"
                . " id_usuario_sso"
                . ") "
                . "VALUES ( "
                . " '$uniqueId',"
                . " '$fecha',"
                . " '$numeroAgente',"
                . " '$nombreAgente',"
                . " '$callerId',"
                . " $id_dnis,"
                . " NULL,"
                . " NULL,"
                . "0";
        
                if(empty($id_usuario_sso))
                {
                    
                    $sql = $sql . ", NULL";
                    
                }
                else
                {
                    
                    $sql =  $sql . ", $id_usuario_sso";
                    
                }    
                                
                $sql =  $sql . ")";

        //var_dump($sql);

        $rs = mysqli_query($conn, $sql) or die("Error: " . mysqli_error($conn));
        if ($rs === FALSE) {
            throw new \Exception("Error al insertar la tipificacion de la llamada.");
        }

        $lastID = mysqli_insert_id($conn);

        mysqli_close($conn);
        
        return $lastID;
    }

    /**
     * 
     * @param type $uniqueId
     * @param type $txtTipificacion
     * @throws InvalidArgumentException
     */
    public function uniqueIDCheck($uniqueId, $id_dnis)
    {
        $pasaValidacion = true;
        
        if (empty($uniqueId))
        {
            throw new \InvalidArgumentException("Empty uniqueid!");
        }

        if (empty($id_dnis))
        {
            throw new \InvalidArgumentException("Empty dnis!");
        }
        

        $conn = $this->__getConnection();

        //Validation
        if (!$conn)
        {
            throw new \Exception("Error: " . mysqli_error($conn));
        }
        
        $sql = "SELECT COUNT(*) AS cantidad_registros FROM tipificacion_llamadas WHERE (uniqueid = '$uniqueId') AND (id_dnis = $id_dnis)";
        
        $rs = mysqli_query($conn, $sql) or die("Error: " . mysqli_error($conn));
        
        if ($rs === FALSE)
        {
            mysqli_close($conn);
            throw new \Exception("Error al ejectutar la consulta.");
        }
        
        $aux_respuesta = $rs->fetch_object()->cantidad_registros;
        
        if($aux_respuesta > 0)
        {
            
            $pasaValidacion = false;
            
        }
        
        mysqli_close($conn);

        return $pasaValidacion;
        
    }
    
    public function close($uniqueId, $msg_tipificacion, $idRelacional, $id_dnis, $callerId = "-1", $numeroAgente = "-1", $nombreAgente = "N/A", $id_usuario_sso = NULL)
    {
        
        $fecha = date("Y-m-d H:i:s");
        
        //echo ("<br/>uniqueId = $uniqueId, msg_tipificacion = $msg_tipificacion, idRelacional = $idRelacional, id_dnis = $id_dnis, callerId = $callerId, numeroAgente = $numeroAgente, nombreAgente = $nombreAgente");
        //die("---");
     
        if (empty($uniqueId))
        {
            throw new \InvalidArgumentException("Empty uniqueid!");
        }
        
        if (empty($id_dnis))
        {
            throw new \InvalidArgumentException("Empty $id_dnis!");
        }
        
        if (empty($msg_tipificacion))
        {
            throw new \InvalidArgumentException("Empty msg_tipificacion!");
        }

        if (empty($idRelacional))
        {
            throw new \InvalidArgumentException("Empty idRelacional!");
        }
        

        $conn = $this->__getConnection();

        //Validation
        if (!$conn)
        {
            throw new \Exception("Error: " . mysqli_error($conn));
        }
        
        $sql_control_1 = "SELECT COUNT(*) AS cantidad_registros FROM tipificacion_llamadas WHERE (uniqueid = '$uniqueId') AND (id_dnis = $id_dnis) AND (msg_tipificacion IS NULL) AND (id_relacional IS NULL)";
        
        $rs_control_1 = mysqli_query($conn, $sql_control_1) or die("Error: " . mysqli_error($conn));
        
        if ($rs_control_1 === FALSE)
        {
            mysqli_close($conn);
            throw new \Exception("Error al ejectutar la consulta : '$sql_control_1'. ");
        }
        
        $aux_respuesta_1 = $rs_control_1->fetch_object()->cantidad_registros;
                
        if($aux_respuesta_1 == 1)
        {
            
            $sql_actualizacion = "UPDATE tipificacion_llamadas 
                                SET msg_tipificacion = '$msg_tipificacion',
                                id_relacional = $idRelacional";
            
            if(!empty($id_usuario_sso))
            {
                
                $sql_actualizacion = $sql_actualizacion . ", id_usuario_sso = $id_usuario_sso";
                
            }
            
            $sql_actualizacion = $sql_actualizacion . " WHERE (uniqueid = '$uniqueId') AND (id_dnis = $id_dnis) AND (msg_tipificacion IS NULL) AND (id_relacional IS NULL)";
            
            $rs_actualizacion = mysqli_query($conn, $sql_actualizacion) or die("Error: " . mysqli_error($conn));

            if ($rs_actualizacion === FALSE)
            {
                mysqli_close($conn);
                
                throw new \Exception("Error al insertar la tipificacion de la llamada con UniqueID = '$uniqueId' .-");
                
            }
            
            return TRUE;
            
        }
        else
        {
            
            $sql_control_2 = "SELECT COUNT(*) AS cantidad_registros FROM tipificacion_llamadas WHERE (uniqueid = '$uniqueId') AND (id_dnis = $id_dnis) AND (msg_tipificacion = '$msg_tipificacion') AND (id_relacional = $idRelacional)";        
        
            $rs_control_2 = mysqli_query($conn, $sql_control_2) or die("Error: " . mysqli_error($conn));

            if ($rs_control_2 === FALSE)
            {
                mysqli_close($conn);
                throw new \Exception("Error al ejectutar la consulta : '$sql_control_2' . ");
            }

            $aux_respuesta_2 = $rs_control_2->fetch_object()->cantidad_registros;

            if($aux_respuesta_2 == 0)
            {
                
                //var_dump($aux_respuesta_2);
                                
                $sql_insercion = "INSERT INTO tipificacion_llamadas ( "
                . " uniqueid,"
                . " fecha,"
                . " numero_agente,"
                . " nombre_agente,"
                . " callerid,"
                . " id_dnis,"
                . " msg_tipificacion,"
                . " id_relacional,"
                . " extra_close,"
                . " id_usuario_sso"
                . ") "
                . "VALUES ( "
                . " '$uniqueId',"
                . " '$fecha',"
                . " '$numeroAgente',"
                . " '$nombreAgente',"
                . " '$callerId',"
                . " $id_dnis,"
                . " '$msg_tipificacion',"
                . " $idRelacional,"
                . "1";
                
                if(empty($id_usuario_sso))
                {
                    
                    $sql_insercion = $sql_insercion . ", NULL";
                    
                }
                else
                {
                    
                    $sql_insercion =  $sql_insercion . ", $id_usuario_sso";
                    
                }    
                                
                $sql_insercion =  $sql_insercion . ")";
                
                $rs_insercion = mysqli_query($conn, $sql_insercion) or die("Error: " . mysqli_error($conn));

                if ($rs_insercion === FALSE)
                {
                    mysqli_close($conn);

                    throw new \Exception("Error al insertar la tipificacion de la llamada con UniqueID = '$uniqueId' .-");

                }

                return TRUE;
                

            }   
        }
        
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

    /**
     * 
     * @return String
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }
    
    public function uniqueIDCheckPorUsuario($uniqueId, $id_dnis, $numero_agente)
    {
        $pasaValidacion = true;
        
        if (empty($uniqueId))
        {
            throw new \InvalidArgumentException("Empty uniqueid!");
        }

        if (empty($id_dnis))
        {
            throw new \InvalidArgumentException("Empty dnis!");
        }
        
        if (empty($numero_agente))
        {
            throw new \InvalidArgumentException("Empty numero_agente!");
        }
        
        
        $conn = $this->__getConnection();

        //Validation
        if (!$conn)
        {
            throw new \Exception("Error: " . mysqli_error($conn));
        }
        
        $sql = "SELECT COUNT(*) AS cantidad_registros FROM tipificacion_llamadas WHERE (uniqueid = '$uniqueId') AND (id_dnis = $id_dnis) AND (numero_agente = '$numero_agente')";
        
        $rs = mysqli_query($conn, $sql) or die("Error: " . mysqli_error($conn));
        
        if ($rs === FALSE)
        {
            mysqli_close($conn);
            throw new \Exception("Error al ejectutar la consulta.");
        }
        
        $aux_respuesta = $rs->fetch_object()->cantidad_registros;
        
        if($aux_respuesta > 0)
        {
            
            $pasaValidacion = false;
            
        }
        
        mysqli_close($conn);

        return $pasaValidacion;
        
    }    
    
    public function closePorAgente($uniqueId, $msg_tipificacion, $idRelacional, $id_dnis, $callerId = "-1", $numeroAgente = "-1", $nombreAgente = "N/A", $id_usuario_sso = NULL)
    {
        
        $fecha = date("Y-m-d H:i:s");
        
        if (empty($uniqueId))
        {
            throw new \InvalidArgumentException("Empty uniqueid!");
        }
        
        if (empty($id_dnis))
        {
            throw new \InvalidArgumentException("Empty id_dnis!");
        }
        
        if (empty($msg_tipificacion))
        {
            throw new \InvalidArgumentException("Empty msg_tipificacion!");
        }

        if (empty($idRelacional))
        {
            throw new \InvalidArgumentException("Empty idRelacional!");
        }
        

        $conn = $this->__getConnection();

        //Validation
        if (!$conn)
        {
            throw new \Exception("Error: " . mysqli_error($conn));
        }
        
        $sql_control_1 = "SELECT COUNT(*) AS cantidad_registros FROM tipificacion_llamadas WHERE (uniqueid = '$uniqueId') AND (numero_agente = '$numeroAgente') AND (id_dnis = $id_dnis) AND (msg_tipificacion IS NULL) AND (id_relacional IS NULL)";
        
        $rs_control_1 = mysqli_query($conn, $sql_control_1) or die("Error: " . mysqli_error($conn));
        
        if ($rs_control_1 === FALSE)
        {
            mysqli_close($conn);
            throw new \Exception("Error al ejectutar la consulta : '$sql_control_1'. ");
        }
        
        $aux_respuesta_1 = $rs_control_1->fetch_object()->cantidad_registros;
                
        if($aux_respuesta_1 == 1)
        {
            
            $sql_actualizacion = "UPDATE tipificacion_llamadas 
                                SET msg_tipificacion = '$msg_tipificacion',
                                id_relacional = $idRelacional";
                                    
            if(!empty($id_usuario_sso))
            {
                
                $sql_actualizacion = $sql_actualizacion . ", id_usuario_sso = $id_usuario_sso";
                
            }

            $sql_actualizacion = $sql_actualizacion . " WHERE (uniqueid = '$uniqueId') AND (numero_agente = '$numeroAgente') AND (id_dnis = $id_dnis) AND (msg_tipificacion IS NULL) AND (id_relacional IS NULL)";

            $rs_actualizacion = mysqli_query($conn, $sql_actualizacion) or die("Error: " . mysqli_error($conn));

            if ($rs_actualizacion === FALSE)
            {
                mysqli_close($conn);
                
                throw new \Exception("Error al insertar la tipificacion de la llamada con UniqueID = '$uniqueId' .-");
                
            }
            
            return TRUE;
            
        }
        else
        {
            
            $sql_control_2 = "SELECT COUNT(*) AS cantidad_registros FROM tipificacion_llamadas WHERE (uniqueid = '$uniqueId') AND (numero_agente = '$numeroAgente') AND (id_dnis = $id_dnis) AND (msg_tipificacion = '$msg_tipificacion') AND (id_relacional = $idRelacional)";        
        
            $rs_control_2 = mysqli_query($conn, $sql_control_2) or die("Error: " . mysqli_error($conn));

            if ($rs_control_2 === FALSE)
            {
                mysqli_close($conn);
                throw new \Exception("Error al ejectutar la consulta : '$sql_control_2'. ");
            }

            $aux_respuesta_2 = $rs_control_2->fetch_object()->cantidad_registros;

            if($aux_respuesta_2 == 0)
            {
                
                //var_dump($aux_respuesta_2);
                                
                $sql_insercion = "INSERT INTO tipificacion_llamadas ( "
                . " uniqueid,"
                . " fecha,"
                . " numero_agente,"
                . " nombre_agente,"
                . " callerid,"
                . " id_dnis,"
                . " msg_tipificacion,"
                . " id_relacional,"
                . " extra_close,"
                . " id_usuario_sso"
                . ") "
                . "VALUES ( "
                . " '$uniqueId',"
                . " '$fecha',"
                . " '$numeroAgente',"
                . " '$nombreAgente',"
                . " '$callerId',"
                . " $id_dnis,"
                . " '$msg_tipificacion',"
                . " $idRelacional,"
                . "1";
                
                if(empty($id_usuario_sso))
                {
                    
                    $sql_insercion = $sql_insercion . ", NULL";
                    
                }
                else
                {
                    
                    $sql_insercion =  $sql_insercion . ", $id_usuario_sso";
                    
                }    
                                
                $sql_insercion =  $sql_insercion . ")";
                
                $rs_insercion = mysqli_query($conn, $sql_insercion) or die("Error: " . mysqli_error($conn));

                if ($rs_insercion === FALSE)
                {
                    mysqli_close($conn);

                    throw new \Exception("Error al insertar la tipificacion de la llamada con UniqueID = '$uniqueId' .-");

                }

                return TRUE;
                

            }   
        }
        
    }    

}



?>
