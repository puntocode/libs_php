<?php
namespace skytel\utils\url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of URLUtils
 *
 * @author Endrigo
 */
class Redirect {

    /**
     * 
     */
    public static function redirectToHTTPS() {

        $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (!headers_sent()) {
            header("Location: $redirect");
            exit;
        } else {
            echo "<meta http-equiv=\"refresh\" content=\"0;url=$redirect\">\r\n";
            exit;
        }
    }

    /**
     * This function send headers, but first analize if headers was sended.
     * 
     * Use the function header(), or otherwise use <meta>
     *  
     * @param String $to 
     */
    public static function redirectTo($to) {

        $url = "http://" . $_SERVER["SERVER_NAME"] . "/$to";

        if (!headers_sent()) {
            header("Location: {$url}");
            exit;
        } else {
            echo "<meta http-equiv=\"refresh\" content=\"0;url={$url}\">\r\n";
            exit;
        }
    }
}
