<?php
namespace skytel\notificaciones;

/**
 * [LIBS_PHP] , Skytel. All Rights Reserved. [2013].
 *
 * @author Endrigo Rivas <endrigo.rivas@gmail.com>
 * 
 * $Id: config.php 22292 2017-01-10 20:22:13Z daniel $
 */
?>
<?php

require_once dirname(__FILE__) . '/../SMS.php';
require_once dirname(__FILE__) . '/../Beeper.php';
require_once dirname(__FILE__) . '/../../../phpmailer/class.phpmailer.php';

/* * ****************************************************************************
 * Dispositivos de comunicacion
 * *************************************************************************** */

//Email
define('libs_SMTP_HOST_SKYTEL', "172.30.2.34");
define('libs_SMTP_FROM_SKYTEL', "no-reply@post-skytel.com");

//define('libs_SMTP_HOST_SKYTEL', "190.228.84.44");
//define('libs_SMTP_FROM_SKYTEL', "no-reply@post-skytel.com.ar");

//Beeper
define("libs_GATEWAY_BEEPER", "190.228.84.70");

//SMS
define("libs_WSDL_SMS_PY", "http://172.30.1.223/SMSCampaignWebApplication/ws/MessagesWS.asmx?wsdl");
//define("libs_WSDL_SMS_AR", "http://10.10.2.103/SMSCampaignWebApplication/ws/MessagesWS.asmx?wsdl");
define("libs_WSDL_SMS_AR", "http://servicio.smsmasivos.com.ar/ws/SMSMasivosAPI.asmx?WSDL");
//define('libs_MAX_LENGHT_SMS', 230);
define('libs_MAX_LENGHT_SMS', 160);
define("libs_PARAGUAY", "0");
define("libs_ARGENTINA", "1");

//WS
//define("libs_URL_WS", "http://notificacionesplataforma/apps/ws/ws.php");
//define("libs_URL_WS", "http://172.30.1.18/notificaciones_plataforma/apps/ws/ws.php");
define("libs_URL_WS", "http://multi.skytel.com.py/notificaciones_plataforma/apps/ws/ws.php");

?>
