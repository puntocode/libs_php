<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DB_connect
 *
 * @author dannyc
 */

namespace skytel\sso;
require_once dirname(__FILE__) . '/config/config.php';
use \PDO;

class DB_connect {

    private  $_connection;
    protected $_result;
    private $_error;
    private $_statement;

    public function __construct() {
        $dsn = 'mysql:dbname=' . DBNAME . ';host=' . DBSERVER;
        try {
            $this->_connection = new PDO($dsn, DBUSER, DBPASS);
            $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $ex) {
            $this->_error = 'Fallo de conexion: ' . $ex->getMessage();
        }
    }

    public function _setParams($data) {
        if (!empty($data) && is_array($data)) {
            $count = 1;
            foreach ($data as $item):
                $this->_statement->bindValue($count, $item);
                $count++;
            endforeach;
        }
    }

    public function _preparedParams($data) {
        $this->_params = '';
        if (!empty($data) && is_array($data)) {
            $count = count($data);
            while ($count) {
                $this->_params .= '?,';
                $count --;
            }
        }
        $this->_params = substr($this->_params, 0, -1);
    }

    public function execProcedure($procedure, $params) {
        try {
            $this->_preparedParams($params);

            $this->_statement = $this->_connection->prepare("CALL $procedure($this->_params);");

            $this->_setParams($params);

            $result = $this->_statement->execute();

            //$error = $this->_pdo->errorCode().'. '.$this->_pdo->errorInfo();
            return $result;
        } catch (PDOException $exc) {
            $this->_error = $exc;
        }
    }

    public function _result_all_array() {
        $this->_result = $this->_statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function _result_all_obj() {
        $this->_result = $this->_statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function _result_obj() {
        $this->_result = $this->_statement->fetchObject();
    }
    
    public function _result_array() {
        $this->_result = $this->_statement->fetchArray();
    }

    public function _result_rowCount() {
        $this->_result = $this->_statement->rowCount();
    }
    
    public function _get_error_message(){
        return $this->_error;
    }

}
