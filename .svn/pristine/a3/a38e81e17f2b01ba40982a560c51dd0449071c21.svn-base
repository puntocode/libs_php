<?php
namespace skytel\utils\string;

/**
 * Description of Ascii
 *
 * @author Endrigo
 */
class Ascii {

     /**
     * Convert text from UTF-8 to ASCII
     * 
     * @param string Text in utf-8 format.
     * @return string Text in ASCII format.
     */
    public function utf8TOASCII($string) {

        $ascii = NULL;
        for ($i = 0; $i < strlen($string); $i++) {
            $ascii .= ord($string[$i]);
        }

        return($ascii);
    }
}
