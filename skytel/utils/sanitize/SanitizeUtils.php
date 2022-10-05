<?php
namespace skytel\utils\sanitize;

/**
 * Metodos de utilería para manejo de Strings
 *
 * @author Endrigo Rivas <endrigo.rivas@gmail.com>
 * 
 * $Id: SanitizeUtils.php 11979 2015-01-13 21:05:25Z endrigo $
 */
class SanitizeUtils {

    /**
     * http://codeassembly.com/How-to-sanitize-your-php-input/ <p>
     * Sanitize only one variable. <p>
     * Returns the variable sanitized according to the desired type.
     * 
     * If $var isn't defined then return empty value.
     *  
     * Types:
     * 
     *  int <p>
     *  string <p>
     *  allhtml <p>
     *  upper_word <p>
     *  ucfirst <p>
     *  lower <p>
     *  urle <p>
     *  urld <p>
     *  sql <p>
     * 
     * @param string $var The variable itself <p>
     * @param string $type A string containing the desired variable type <p>
     * 
     * @return The sanitized variable or true/false
     */
    public static function sanitizeOne($var, $type) {

        if (!isset($var) || !isset($type))
            return "";

        switch ($type) {

            case 'int': // integer
                $var = (int) $var;
                break;

            case 'string': // trim string
                $var = trim($var);
                break;

            case 'allhtml': // trim string, no HTML allowed
                $var = htmlentities(trim($var), ENT_QUOTES);
                break;

            case 'upper_word': // trim string, upper case words
                $var = ucwords(strtolower(trim($var)));
                break;

            case 'ucfirst': // trim string, upper case first word
                $var = ucfirst(strtolower(trim($var)));
                break;

            case 'lower': // trim string, lower case words
                $var = strtolower(trim($var));
                break;

            case 'urle': // trim string, url encoded
                $var = urlencode(trim($var));
                break;

            case 'urld': // trim string, url decoded
                $var = urldecode(trim($var));
                break;

            case 'sql': // the given string is SQL injection safe
                // si magic_quotes_gpc esta activado primero aplicar stripslashes()
                // de lo contrario los datos seran escapados dos veces
                if (get_magic_quotes_gpc()) {
                    $var = stripslashes($var);
                }

                return trim(SanitizeUtils::mysql_escape_mimic($var));
                break;
        }

        return $var;
    }

    /**
     * http://codeassembly.com/How-to-sanitize-your-php-input/
     *
     * Sanitize an array.
     *
     * sanitizeArray($_POST, array('id'=>'int', 'name' => 'str'));
     * sanitizeArray($customArray, array('id'=>'int', 'name' => 'str'));
     *
     * @param array $data
     * @param array $whatToKeep
     */
    public static function sanitizeArray(&$data, $whatToKeep) {

        $data = array_intersect_key($data, $whatToKeep);
        foreach ($data as $key => $value) {
            $data[$key] = sanitizeOne($data[$key], $whatToKeep[$key]);
        }
    }

    /**
     * Esta funcion es un reemplazo a mysql_real_escape_string,
     * que no pide una conexion activa a la base de datos.
     *
     * @param string $consulta El string con los valores que deben ser controlados <p>
     * @return string El string con los valores controlados ('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z')
     */
    public static function mysql_escape_mimic($consulta) {
        if (is_array($consulta))
            return array_map(__METHOD__, $consulta);

        if (!empty($consulta) && is_string($consulta)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $consulta);
        }

        return $consulta;
    }

}
