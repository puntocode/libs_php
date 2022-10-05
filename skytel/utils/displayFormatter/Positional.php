<?php

namespace skytel\utils\displayFormatter;

/**
 * Description of Utils
 *
 * @author Endrigo
 */
class Positional {

    /**
     * 
     * @param type $NUM
     * @return string
     */
    public static function ordinal_number($NUM) {
        //TENS
        if (strlen($NUM) > 1 && substr($NUM, -2, 1) == 1) {
            $ord = "th";
        }
        //ALL OTHERS
        else {
            $num = substr($NUM, -1); // GET LAST NUMBER
            if ($num == 1) {
                $ord = "st";
            }
            if ($num == 2) {
                $ord = "nd";
            }
            if ($num == 3) {
                $ord = "rd";
            }
            if ($num == 4) {
                $ord = "th";
            }
            if ($num == 5) {
                $ord = "th";
            }
            if ($num == 6) {
                $ord = "th";
            }
            if ($num == 7) {
                $ord = "th";
            }
            if ($num == 8) {
                $ord = "th";
            }
            if ($num == 9) {
                $ord = "th";
            }
            if ($num == 0) {
                $ord = "th";
            }
        }
        return $ord;
    }



}
