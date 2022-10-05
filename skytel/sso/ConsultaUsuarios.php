<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConsultaUsuariosNew
 *
 * @author dannyc
 */

namespace skytel\sso;
include_once 'DB_connect.php';

class ConsultaUsuarios extends DB_connect{
    private $v_respuesta;

    public function __construct() {
        parent::__construct();
        $this->v_respuesta = array('retorno' => false,'datos' => array(),'error' => false,'msg_error' => '');
    }
    
    public function listaUsuarios($plataforma, $cuenta, $servicio){
        $params = [$plataforma,$cuenta,$servicio];
        $proc = 'get_lista_usuarios';
        if($this->execProcedure($proc, $params)){
            $this->_result_all_array(); 
            $this->v_respuesta['retorno'] = true;
            $this->v_respuesta['datos'] = $this->_result;
        } else {
            $this->v_respuesta['error'] = true; 
            $this->v_respuesta['msg_error'] = $this->_get_error_message();
        }
        return $this->v_respuesta;
    }
    
    public function getCredencialSSO($usuario, $password){
        $params = [$usuario, $password];
        $proc = 'get_credencial_SSO';
        if($this->execProcedure($proc, $params)){
            $this->_result_all_array();
            $this->v_respuesta['retorno'] = true;
            $this->v_respuesta['datos'] = $this->_result;
            
        } else {
            $this->v_respuesta['error'] = true; 
            $this->v_respuesta['msg_error'] = $this->_get_error_message();
        }
        return $this->v_respuesta;
    }

}
