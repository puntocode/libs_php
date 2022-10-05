<?php
namespace skytel\utils\url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Parse
 *
 * @author Endrigo
 */
class Parse {

    /**
     * Return the URI in each position of array
     * 
     * @param String $rootURI The first position of URI.
     * @return Array the URI in each position of array.
     */
    public static function returnURL($rootURI) {


        $array1 = $_SERVER['REQUEST_URI'];
        $array1 = str_replace('%20', ' ', $array1);
        $array1 = preg_split('#/#', $array1);

        $array2 = preg_split('#/#', $rootURI);
        $result = array_diff($array1, $array2);

//        echo "<br>--";
//        print_r($array1);
//        echo "<br>--";
//        print_r($array2);
//        echo "<br>--";
//        print_r($result);

        $arrayAux = array();
        foreach ($result as $results) {
            if (!empty($results) && $results != "")
                $arrayAux[] = $results;
        }

        return $arrayAux;
    }

}
