<?php
namespace skytel\utils\string;

/**
 * Description of StringUtils
 *
 * @author endrigo
 */
class Match {

    /**
     *
     * $str='|apples}';
     *
     * echo startsWith($str,'|'); //Returns true
     * 
     * @param String $needle
     * @param String $haystack
     * @param boolean $case
     * 
     * @return boolean
     */
    public static function startsWith($haystack, $needle, $case = true) {
        if ($case) {
            return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
        }
        return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }

    /**
     *
     * $str='|apples}';
     *
     * echo endsWith($str,'}'); //Returns true
     * 
     * @param String $needle
     * @param String $haystack
     * @param boolean $case
     * 
     * @return boolean 
     */
    public static function endsWith($haystack, $needle, $case = true) {
        if ($case) {
            return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
        }
        return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
    }

}

?>
