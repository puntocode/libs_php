<?php
namespace skytel\utils\file;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileUtils
 *
 * @author Endrigo
 */
class FileUtils {

    /**
     * Lee un archivo, y coloca el contenido en una variable.
     *
     * @param string $filename El archivo con el path incluido<p>
     * 
     * @return string Variable con todo el contenido del archivo. Incluido los caracteres
     * de escape.
     */
    public static function fileToString($filename) {

        $output = "";
        $file = fopen($filename, "r");
        while (!feof($file)) {

            //read file line by line into variable
            $output = $output . fgets($file, 4096);
        }

        fclose($file);
        return $output;
    }

}
