<?php

namespace skytel\sso;

require_once dirname(__FILE__) . '/config/config.php';
require_once dirname(__FILE__) . '/../../nusoap/nusoap.php';
require_once dirname(__FILE__) . '/../../nusoap/class.wsdlcache.php';

class VerificarCredencial_old {

    private $v_respuesta;

    /**
     * CONSTRUCTOR
     */
    public function __construct() {

        $this->v_respuesta = array('retorno' => false,
            'datos' => array(),
            'error' => false,
            'msg_error' => '',
            'token' => ' ');
    }

    /**
     * 
     * @param type $cliente
     * @param type $serverURL
     * @param type $login
     * @param type $password
     * @return type
     */
    private function obtenerCredencialesXUsuPassw($cliente, $serverURL, $login, $password) {

        $metodoALlamar = 'getCredentials';

        $params = [
            'login' => $login,
            'password' => $password
        ];

        // 1. Llamar a la funcion getRespuesta del servidor
        $result = $cliente->call(
                $metodoALlamar, // Funcion a llamar
                $params, // Parametros pasados a la funcion
                "uri:$serverURL", // namespace
                "uri:$serverURL/$metodoALlamar" // SOAPAction
        );


        // Verificacion que los parametros estan ok,
        // Si lo estan mostrar respuesta.
        if ($cliente->fault) {

            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $result;
        }

        $error = $cliente->getError();

        if ($error) {

            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $error;
        } else {
            $datos = json_decode($result);


            return $datos;
        }
    }

    /**
     * 
     * @param type $cliente
     * @param type $serverURL
     * @param type $acd
     * @return type
     */
    private function obtenerCredencialesXACD($cliente, $serverURL, $acd) {

        $metodoALlamar = 'getUsuarios';

        $params = ['acd' => $acd];

        // 1. Llamar a la funcion getRespuesta del servidor
        $result = $cliente->call(
                $metodoALlamar, // Funcion a llamar
                $params, // Parametros pasados a la funcion
                "uri:$serverURL", // namespace
                "uri:$serverURL/$metodoALlamar" // SOAPAction
        );


        // Verificacion que los parametros estan ok,
        // Si lo estan mostrar respuesta.

        if ($cliente->fault) {

            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $result;
        }

        $error = $cliente->getError();

        if ($error) {

            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $error;
        } else {
            $datos = json_decode($result);


            return $datos;
        }
    }

    /**
     * 
     * Verifica la credencial basadon en el login y el password
     * 
     * @param type $login
     * @param type $password
     * @param type $plataforma
     * @param type $cuenta
     * @param type $servicio
     * @return Array array('retorno' => false,
      'datos' => array(),
      'error' => false,
      'msg_error' => '')
     */
    public function verificarCredendialXUsuPassw($login, $password, $plataforma, $cuenta, $servicio) {


        $soapurl = WS_SSO_PLATAFORMA . "ws?wsdl";

        $cache = new \nusoap_wsdlcache($this->getPathCache(), 12000);

        $wsdl = $cache->get($soapurl);

        if (is_null($wsdl)) {
            $wsdl = new \wsdl($soapurl);
            $cache->put($wsdl);
        }


        $cliente = new \nusoap_client($soapurl, true);

        $error = $cliente->getError();

        if ($error) {
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $error;
        }

        $array_datos_login = $this->obtenerCredencialesXUsuPassw($cliente, WS_SSO_PLATAFORMA . "ws", $login, $password);

        if ($array_datos_login->data) {

            $cantidad_filas = count($array_datos_login->data);

            for ($indice_array = 0; $indice_array < ($cantidad_filas - 2); $indice_array++) {

                if ((strtoupper(trim($array_datos_login->data[$indice_array]->plataforma)) == strtoupper(trim($plataforma))) && (strtoupper(trim($array_datos_login->data[$indice_array]->cuenta)) == strtoupper(trim($cuenta))) && (strtoupper(trim($array_datos_login->data[$indice_array]->servicio)) == strtoupper(trim($servicio)))) {

                    $this->v_respuesta['retorno'] = true;
                    $this->v_respuesta['datos'] = $array_datos_login->data[$indice_array];

                    break;
                }
            }
        }

        return $this->v_respuesta;
    }

    public function obtenerDatosUsuarioXUsuPassw($login, $password) {


        $soapurl = WS_SSO_PLATAFORMA . "ws?wsdl";

        $cache = new \nusoap_wsdlcache($this->getPathCache(), 12000);

        $wsdl = $cache->get($soapurl);

        if (is_null($wsdl)) {
            $wsdl = new \wsdl($soapurl);
            $cache->put($wsdl);
        }


        $cliente = new \nusoap_client($soapurl, true);

        $error = $cliente->getError();

        if ($error) {
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $error;
        }

        $cantidad_filas = 0;

        $array_datos_login = $this->obtenerCredencialesXUsuPassw($cliente, WS_SSO_PLATAFORMA . "ws", $login, $password);

        if ($array_datos_login->data) {

            $cantidad_filas = count($array_datos_login->data);

            $this->v_respuesta['retorno'] = true;

            $aux_array = array();

            for ($indice_array = 0; $indice_array < ($cantidad_filas - 1); $indice_array++) {

                $aux_array[$indice_array] = $array_datos_login->data[$indice_array];
            }

            $this->v_respuesta['datos'] = $aux_array;
        }


        return $this->v_respuesta;
    }

    /**
     * Verifica la credencial basadon en el ACD
     * 
     * @param type $acd
     * @param type $plataforma
     * @param type $cuenta
     * @param type $servicio
     * @return Array array('retorno' => false,
      'datos' => array(),
      'error' => false,
      'msg_error' => '')
     */
    public function verificarCredendialXACD($acd, $plataforma, $cuenta, $servicio) {

        $soapurl = WS_SSO_PLATAFORMA . "ws_acd?wsdl";

        $cache = new \nusoap_wsdlcache($this->getPathCache(), 12000);

        $wsdl = $cache->get($soapurl);

        if (is_null($wsdl)) {
            $wsdl = new \wsdl($soapurl);
            $cache->put($wsdl);
        }


        $cliente = new \nusoap_client($soapurl, true);
        //$cliente = new nusoap_client(WS_SSO_PLATAFORMA" . ?wsdl", 'wsdl');

        $error = $cliente->getError();

        if ($error) {
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $error;
        }

        $array_datos_login = $this->obtenerCredencialesXACD($cliente, WS_SSO_PLATAFORMA . "ws_acd", $acd);

        if ($array_datos_login->data) {

            $cantidad_filas = count($array_datos_login->data);

            for ($indice_array = 0; $indice_array < $cantidad_filas; $indice_array++) {

                if ((strtoupper(trim($array_datos_login->data[$indice_array]->plataforma)) == strtoupper(trim($plataforma))) && (strtoupper(trim($array_datos_login->data[$indice_array]->cuenta)) == strtoupper(trim($cuenta))) && (strtoupper(trim($array_datos_login->data[$indice_array]->servicio)) == strtoupper(trim($servicio)))) {

                    $this->v_respuesta['retorno'] = true;
                    $this->v_respuesta['datos'] = $array_datos_login->data[$indice_array];

                    break;
                }
            }
        }

        return $this->v_respuesta;
    }

    /*
     * 
     */

    private function getPathCache() {

        $pathCache = "/tmp";


        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $pathCache = "C:\\temp";

            if (!file_exists($pathCache)) {
                mkdir($pathCache);
            }
        }

        return $pathCache;
    }

    public function verificarCredendialX_ACD_IDS($acd, $plataforma, $id_cuenta, $id_servicio) {

        $soapurl = WS_SSO_PLATAFORMA . "ws_acdToken?wsdl";

        $cache = new \nusoap_wsdlcache($this->getPathCache(), 12000);

        $wsdl = $cache->get($soapurl);

        if (is_null($wsdl)) {
            $wsdl = new \wsdl($soapurl);
            $cache->put($wsdl);
        }


        $cliente = new \nusoap_client($soapurl, true);
        //$cliente = new nusoap_client(WS_SSO_PLATAFORMA" . ?wsdl", 'wsdl');

        $error = $cliente->getError();

        if ($error) {
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $error;
        }

        $array_datos_login = $this->obtenerCredencialesXACDToken($cliente, WS_SSO_PLATAFORMA . "ws_acdtoken", $acd);

        if ($array_datos_login->data) {
            //$this->v_respuesta['retorno'] = true;
            //$this->v_respuesta['datos'] = $array_datos_login->data;            

            $cantidad_filas = count($array_datos_login->data);

            for ($indice_array = 0; $indice_array < ($cantidad_filas - 2); $indice_array++) {

                if ((strtoupper(trim($array_datos_login->data[$indice_array]->plataforma)) == strtoupper(trim($plataforma))) && ($array_datos_login->data[$indice_array]->id_cuenta == $id_cuenta) && ($array_datos_login->data[$indice_array]->id_servicio == $id_servicio)) {

                    $this->v_respuesta['retorno'] = true;
                    $this->v_respuesta['datos'] = $array_datos_login->data[$indice_array];
                    $this->v_respuesta['token'] = $array_datos_login->data[$cantidad_filas - 2];
                    $this->v_respuesta['url_logo'] = $array_datos_login->data[$cantidad_filas - 1];
                    $this->v_respuesta['acceso'] = 1;
                    break;
                } else {
                    $this->v_respuesta['retorno'] = false;
                    $this->v_respuesta['acceso'] = 0;
                    $this->v_respuesta['datos'] = $array_datos_login->data[0];
                    $this->v_respuesta['url_logo'] = $array_datos_login->data[$cantidad_filas - 1];
                    $this->v_respuesta['token'] = $array_datos_login->data[$cantidad_filas - 2];
                }
            }
        }

        return $this->v_respuesta;
    }

    public function obtenerDatosUsuarioXUsuPasswToken($login, $password) {


        $soapurl = WS_SSO_PLATAFORMA . "ws?wsdl";

        $cache = new \nusoap_wsdlcache($this->getPathCache(), 12000);

        $wsdl = $cache->get($soapurl);

        if (is_null($wsdl)) {
            $wsdl = new \wsdl($soapurl);
            $cache->put($wsdl);
        }


        $cliente = new \nusoap_client($soapurl, true);

        $error = $cliente->getError();

        if ($error) {
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $error;
        }

        $cantidad_filas = 0;

        $array_datos_login = $this->obtenerCredencialesXUsuPassw($cliente, WS_SSO_PLATAFORMA . "ws", $login, $password);

        if ($array_datos_login->data) {

//            $cantidad_filas = count($array_datos_login->data);

            $this->v_respuesta['retorno'] = true;

//            $aux_array = array();
//                        
//            for ($indice_array = 0; $indice_array < ($cantidad_filas - 12); $indice_array++)
//            {
//                
//                $aux_array[$indice_array] = $array_datos_login->data[$indice_array];
//                
//            }

            $this->v_respuesta['datos'] = $array_datos_login->data;
        }


        return $this->v_respuesta;
    }

    private function obtenerCredencialesXACDToken($cliente, $serverURL, $acd) {

        $metodoALlamar = 'getUsuariosToken';

        $params = ['acd' => $acd];

        // 1. Llamar a la funcion getRespuesta del servidor
        $result = $cliente->call(
                $metodoALlamar, // Funcion a llamar
                $params, // Parametros pasados a la funcion
                "uri:$serverURL", // namespace
                "uri:$serverURL/$metodoALlamar" // SOAPAction
        );


        // Verificacion que los parametros estan ok,
        // Si lo estan mostrar respuesta.

        if ($cliente->fault) {

            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $result;
        }

        $error = $cliente->getError();

        if ($error) {

            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $error;
        } else {

            $datos = json_decode($result);


            return $datos;
        }
    }

}

?>
