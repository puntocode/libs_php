1) Incluir las librerías
//LIBS_PHP
require_once dirname(__FILE__) . '/config.libs.skytel.php';
require_once 'skytel/dreamPBX/GetCallDetail.php';
//require_once 'skytel/dreamPBX/GetCallDetailAuth.php';

2) Ejemplo de uso:
 //Obtiene todos los datos de la llamada
 //
if ($uniqueId !== "" && $uniqueId !== NULL) {
    sleep(2); //El retardo es necesario para que funcione el API de la DREAMPBX
    $callObj = new \skytel\dreamPBX\GetCallDetailAuth($uniqueId, SOCKET_IP_PBX, SOCKET_PORT_PBX, GET_CALL_AUTH_PBX);
    $callerid = $callObj->getANI();
}
