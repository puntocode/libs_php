<?php

namespace skytel\notificaciones;

/**
 * [LIBS_PHP] , Skytel. All Rights Reserved. [2015].
 *
 * @author Endrigo Rivas <endrigo.rivas@gmail.com>
 * 
 * $Id: SMSPersonalPY.php 17252 2015-12-03 20:12:54Z endrigo $
 */
?>
<?php

require_once dirname(__FILE__) . '/../../smpp/class.smpp.php';

class SMSPersonalPY {

    private $telefono;
    private $mensaje;
    private $parametros;
    private $errorMsg;

    /**
     * 
     * @param type $telefono
     * @param type $mensaje
     * @throws UnexpectedValueException
     */
    public function __construct($telefono, $mensaje) {

        $this->telefono = trim($telefono);
        $this->mensaje = trim($mensaje);
        $this->parametros = array();
        $this->errorMsg = "";
    }

    /**
     * 
     * @return boolean
     */
    public function enviar() {

        if ($this->telefono == "") {
            $this->errorMsg = "Empty phone number";
            return false;
        }

        $host = "200.3.250.148"; // Number provided by Personal
        $port = 30123; // Number provided by Personal
        $user = "mtel"; // Number provided by Personal
        $passw = "mtel15"; // Number provided by Personal

        $src = "0974130186"; // Number provided by Personal
        $dst = $this->telefono;
        $message = $this->__encodeMessage($this->mensaje);

        $s = new \smpp();
//        $s->debug = 1;

        $open = $s->open($host, $port, $user, $passw);
        if (!$open) {
            $this->errorMsg = "Problem connection to host!";
            return false;
        }

        $ok = $s->send_long($src, $dst, $message);
        if (!$ok) {
            $this->errorMsg = "Problem to send message!";
            return false;
        }

        $s->close();

        return true;
    }

    /**
     * 
     * @return type
     */
    public function getErrorMsg() {
        return $this->errorMsg;
    }


    /**
     * 
     * The message is sended using a HTTP Query.  The accents aren't allowed.
     *  
     * 
     *   Caracteres válidos:
     *   ---------------------
     *   . ; , : _ ! <> $ ^ * \" - ' = ? /
     *
     *   Caracteres inválidos:
     *   ---------------------
     *   ¡ º |  \\  + {} ¿  & % () ª # Ñ ñ áéíóúaäëïöüàèìòùçÇ
     *
     * 
     * 
     * @param type $message
     * @return String
     */
    private function __encodeMessage($message) {

        $someSpecialChars = array(
            "á", "é", "í", "ó", "ú",
            "Á", "É", "Í", "Ó", "Ú",
            "ä", "ë", "ï", "ö", "ü",
            "Ä", "Ë", "Ï", "Ö", "Ü",
            "à", "è", "ì", "ò", "ù",
            "À", "È", "Ì", "Ò", "Ù",
            "ç", "Ç", "ñ", "Ñ",
            "¡", "º", "|", "+", "{", "}", "¿", "&", "%", "(", ")", "ª", "#", "\\"
        );

        $replacementChars = array(
            "a", "e", "i", "o", "u",
            "A", "E", "I", "O", "U",
            "a", "e", "i", "o", "u",
            "A", "E", "I", "O", "U",
            "a", "e", "i", "o", "u",
            "A", "E", "I", "O", "U",
            "c", "C", "n", "N",
            "", "", "", "", "", "", "", "", "", "", "", "", "", ""
        );

        $replaced_string = str_replace($someSpecialChars, $replacementChars, $message);

        return $replaced_string;
//        return urlencode($replaced_string);
    }

}

?>