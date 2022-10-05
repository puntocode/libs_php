<?php

namespace skytel\utils\string;

/**
 * Description of StringUtils
 *
 * @author endrigo
 */
class FormatOutput {

    /**
     * Trunca el texto en la cantidad de caracteres especificado. Si es mayor,
     * agrega el sufijo al final.
     * 
     * Si el texto es vacio, retorna vacio.
     * 
     * @param type $text
     * @param type $lenght
     * @param type $suffix Por defecto ...
     * @return type
     */
    public static function truncateText($text, $lenght, $suffix = "...") {
        if(empty($text)) {
            return $text;
        }
        
        return substr($text, 0, $lenght) . (strlen($text) > $lenght ? $suffix : '');
    }

}
