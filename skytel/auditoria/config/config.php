<?php

/**
 * [PANTALLAS_STAR] , Skytel. All Rights Reserved. [2013].
 *
 * @author Endrigo Rivas <endrigo.rivas@gmail.com>
 * 
 * $Id: config.php 14889 2015-07-21 16:10:17Z endrigo $
 */
?>
<?php

$IP_SERVER_CLUSTER = ["172.30.2.18", "172.30.2.19", "172.30.2.20"];
$IP_SERVER_PRODUCCION = "172.30.1.37"; //IP del servidor de produccion
$IP_SERVER_TESTING = "172.30.1.18"; //IP del servidor de testing
$ipServer = filter_input(INPUT_SERVER, 'SERVER_ADDR'); 

/**
 * Configuración por entorno 
 */
switch ($ipServer) {
    case in_array($ipServer, $IP_SERVER_CLUSTER):
        require_once dirname(__FILE__) . '/config.produccion.cluster.php';
        break;
    case $IP_SERVER_PRODUCCION:
        require_once dirname(__FILE__) . '/config.produccion.php';
        break;
    case $IP_SERVER_TESTING:
        require_once dirname(__FILE__) . '/config.testing.php';
        break;
    default:
        require_once dirname(__FILE__) . '/config.desarrollo.php';
}
?>

