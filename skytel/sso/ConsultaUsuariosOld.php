<?php
namespace skytel\sso;

require_once dirname(__FILE__) . '/config/config.php';
require_once dirname(__FILE__) . '/../../nusoap/nusoap.php';
require_once dirname(__FILE__) . '/../../nusoap/class.wsdlcache.php';

class ConsultaUsuariosOld
{
    private $v_respuesta;

    /**
     * CONSTRUCTOR
     */
    public function __construct()
    {
        
        $this->v_respuesta = array('retorno' => false,
                                'datos' => array(),
                                'error' => false,
                                'msg_error' => '');        
    }

    private function obtenerListaUsuarios($cliente, $serverURL, $plataforma, $cuenta, $servicio) 
    {

        $metodoALlamar = 'getListaUsuarios';

        $params = [  
            'plataforma' => $plataforma,
            'cuenta' => $cuenta,
            'servicio' => $servicio
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
        if ($cliente->fault)
        {            
            
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $result;
        
        }

        $error = $cliente->getError();

        if ($error)
        {

            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $error;
            
        } 
        else
        {
            $datos = json_decode($result);
                     

            return $datos;
        }
        
    }

    private function obtenerCredencialSSO($cliente, $serverURL, $usuario, $password) 
    {

        $metodoALlamar = 'getCredencialSSO';

        $params = [  
            'usuario' => $usuario,
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
        if ($cliente->fault)
        {            
            
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $result;
        
        }

        $error = $cliente->getError();

        if ($error)
        {

            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $error;
            
        } 
        else
        {
            $datos = json_decode($result);
                     

            return $datos;
        }
        
    }    
    
     
    
    public function listaUsuarios($plataforma, $cuenta, $servicio)
    {

 
        $soapurl = WS_SSO_PLATAFORMA . "ws_consulta_usuarios?wsdl";

        $cache = new \nusoap_wsdlcache($this->getPathCache(), 12000);

        $wsdl = $cache->get($soapurl);

        if (is_null($wsdl))
        {
            $wsdl = new \wsdl($soapurl);
            $cache->put($wsdl);
        }


        $cliente = new \nusoap_client($soapurl, true);
        
        $error = $cliente->getError();

        if ($error)
        {
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $error;
            
        }

        $array_datos_login = $this->obtenerListaUsuarios($cliente, WS_SSO_PLATAFORMA . "ws_consulta_usuarios", $plataforma, $cuenta, $servicio);
        

        if ($array_datos_login)
        {
                       
            $cantidad_filas = count($array_datos_login->data);
            
            if($cantidad_filas > 0)
            {    
            
                if ((strtoupper(trim($array_datos_login->data[0]->plataforma)) == strtoupper(trim($plataforma))) && (strtoupper(trim($array_datos_login->data[0]->cuenta)) == strtoupper(trim($cuenta))) && (strtoupper(trim($array_datos_login->data[0]->servicio)) == strtoupper(trim($servicio))))
                {

                       $this->v_respuesta['retorno'] = true;
                       $this->v_respuesta['datos'] = $array_datos_login->data;

                }
            }
            
        }
        
        return  $this->v_respuesta;
        
    }
        
    public function getCredencialSSO($usuario, $password)
    {

 
        $soapurl = WS_SSO_PLATAFORMA . "ws_consulta_usuarios?wsdl";

        $cache = new \nusoap_wsdlcache($this->getPathCache(), 12000);

        $wsdl = $cache->get($soapurl);

        if (is_null($wsdl))
        {
            $wsdl = new \wsdl($soapurl);
            $cache->put($wsdl);
        }


        
        $cliente = new \nusoap_client($soapurl, true);
        
        $error = $cliente->getError();

        if ($error)
        {
            $this->v_respuesta['error'] = true;
            $this->v_respuesta['msg_error'] = $error;
            
        }
        
        $array_datos_login = $this->obtenerCredencialSSO($cliente, WS_SSO_PLATAFORMA . "ws_consulta_usuarios", $usuario, $password);
        

        if ($array_datos_login->data)
        {
                       
            $cantidad_filas = count($array_datos_login->data);
            
            if($cantidad_filas > 0)
            {    
            
               
                $this->v_respuesta['retorno'] = true;
                $this->v_respuesta['datos'] = $array_datos_login->data;
               
            }
            
        }
        
        return  $this->v_respuesta;
        
    }  
    

    private function getPathCache()
    {

        $pathCache = "/tmp";

        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        {
            $pathCache = "C:\\temp";
            
            if (!file_exists($pathCache))
            {
                mkdir($pathCache);
                
            }           
            
        }
        
        return $pathCache;
        
    }

}

?>
