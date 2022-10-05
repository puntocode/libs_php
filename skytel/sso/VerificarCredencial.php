<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VerificarCredencialNew
 *
 * @author dannyc
 */

namespace skytel\sso;

include_once 'DB_connect.php';

class VerificarCredencial extends DB_connect {

    private $v_respuesta;

    public function __construct() {
        parent::__construct();
        $this->v_respuesta = array('retorno' => false, 'datos' => array(), 'error' => false, 'msg_error' => '', 'token' => ' ');
    }

    public function obtenerDatosUsuarioXUsuPassw($login, $password) {
        $params = [$login, $password];
        $proc = 'get_credenciales';
        if ($this->execProcedure($proc, $params)) {
            $this->_result_all_obj();
            $this->v_respuesta['retorno'] = true;
            $this->v_respuesta['datos'] = $this->_result;
            $token = $this->_get_token($this->_result[0]->id);
            $this->v_respuesta['datos'][] = $token[0];
        } else {
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $this->_get_error_message();
        }
        return $this->v_respuesta;
    }

    public function obtenerDatosUsuarioXUsuPasswToken($login, $password) {
        $params = [$login, $password];
        $proc = 'get_credenciales';
        if ($this->execProcedure($proc, $params)) {
            $this->_result_all_obj();
            $this->v_respuesta['retorno'] = true;
            $this->v_respuesta['datos'] = $this->__utf8_converter($this->_result);
            $token = $this->_get_token($this->_result[0]->id);
            $this->v_respuesta['datos'][] = $token[0];
            $this->v_respuesta['datos'][] = $token[1];
        } else {
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $this->_get_error_message();
        }
        return $this->v_respuesta;
    }

    public function verificarCredendialXACD($acd, $plataforma, $cuenta, $servicio) {
        $params = [$acd];
        $proc = 'get_usuario_acd';
        if ($this->execProcedure($proc, $params)) {
            $this->_result_all_obj();
            foreach ($this->_result as $row) {
                if ((strtoupper(trim($row->plataforma)) == strtoupper(trim($plataforma))) &&
                        (strtoupper(trim($row->cuenta)) == strtoupper(trim($cuenta))) &&
                        (strtoupper(trim($row->servicio)) == strtoupper(trim($servicio)))) {
                    $this->v_respuesta['retorno'] = true;
                    $this->v_respuesta['datos'] = $row;
                }
            }
        } else {
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $this->_get_error_message();
        }
        return $this->v_respuesta;
    }

    public function verificarCredendialXUsuPassw($login, $password, $plataforma, $cuenta, $servicio) {
        $params = [$login, $password];
        $proc = 'get_credenciales';
        if ($this->execProcedure($proc, $params)) {
            $this->_result_all_obj();
            foreach ($this->_result as $row) {
                if ((strtoupper(trim($row->plataforma)) == strtoupper(trim($plataforma))) &&
                        (strtoupper(trim($row->cuenta)) == strtoupper(trim($cuenta))) &&
                        (strtoupper(trim($row->servicio)) == strtoupper(trim($servicio)))) {
                    $this->v_respuesta['retorno'] = true;
                    $this->v_respuesta['datos'] = $row;
                }
            }
        } else {
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $this->_get_error_message();
        }
        return $this->v_respuesta;
    }

    public function verificarCredendialX_ACD_IDS($acd, $plataforma, $id_cuenta, $id_servicio) {
        $params = [$acd];
        $proc = 'get_usuario_token_acd';
        if ($this->execProcedure($proc, $params)) {
            $this->_result_all_obj();
            $result = $this->_result;
            $token = (count($result)) ? $this->_get_token($this->_result[0]->id) : array();
            $this->v_respuesta['url_logo'] = $token[1];
            $this->v_respuesta['token'] = $token[0];
            foreach ($result as $row) {
                if ((strtoupper(trim($row->plataforma)) == strtoupper(trim($plataforma))) && ($row->id_cuenta == $id_cuenta) && ($row->id_servicio == $id_servicio)) {
                    $this->v_respuesta['retorno'] = true;
                    $this->v_respuesta['datos'] = $row;
                    $this->v_respuesta['acceso'] = 1;
                    break;
                } else {
                    $this->v_respuesta['retorno'] = false;
                    $this->v_respuesta['acceso'] = 0;
                    $this->v_respuesta['datos'] = $row;
                }
            }
        } else {
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $this->_get_error_message();
        }
        return $this->v_respuesta;
    }

    private function _get_token($id_credencial) {
        $params = [$id_credencial];
        $proc = 'ins_token';
        if ($this->execProcedure($proc, $params)) {
            $this->_result_obj();
            $token = new \stdClass();
            $token->token = $this->_result->token;
            $url_logo = new \stdClass();
            $url_logo->url_logo = "http://salientes.skytel.com.py/sso_plataforma/logos/";
//            return new \stdClass(['token'=>$this->_result->token],['url_logo' => "http://salientes.skytel.com.py/sso_plataforma/logos/"]);
            return [$token, $url_logo];
        } else {
            $token = new \stdClass();
            $token->token = "NO SE GENERO EL TOKEN";
            $url_logo = new \stdClass();
            $url_logo->url_logo = "http://salientes.skytel.com.py/sso_plataforma/logos/";
//            return new \stdClass(['token'=>"NO SE GENERO EL TOKEN"],['url_logo' => "http://salientes.skytel.com.py/sso_plataforma/logos/"]);
            return [$token, $url_logo];
        }
    }

    private function __utf8_converter($array) {

        array_walk_recursive($array, function(&$item, $key) {
            if (is_object($item)) {
                foreach ($item as $key => $value) {
                    $this->__toUTF8($item->$key);
                }
            } else {
                $this->__toUTF8($item);
            }
        });

        return $array;
    }

    private function __toUTF8(&$item) {

        if (!mb_detect_encoding($item, 'utf-8', true)) {
            $item = utf8_encode($item);
        }
//    return $item;
    }

}
