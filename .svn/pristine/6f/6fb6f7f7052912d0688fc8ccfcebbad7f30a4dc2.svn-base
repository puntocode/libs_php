<?php
namespace skytel\utils\fecha;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Format
 *
 * @author Endrigo
 */
class Format {

    /**
     * Obtiene los datos según el formato de origen
     *  
     * @param string $_fecha
     * @param string $_formato. 
     * @param string $_tipo. Opciones: maq,dmy,mdy,ymd. Por defecto maq
     * @return string
     */
    public function cambiarFormato($_fecha, $_formato = 'd-m-Y', $_tipo = 'maq') {

        $r = new \stdClass();

        // Obtiene los datos según el formato de origen
        switch ($_tipo):
            case 'maq':
                if (!preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/', $_fecha, $reg))
                    if (!preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $_fecha, $reg))
                        preg_match('/()()()([0-9]{2}):([0-9]{2}):([0-9]{2})/', $_fecha, $reg);
                $r->fecha = $this->validate($reg, 0);
                $r->ano = $this->validate($reg, 1);
                $r->mes = $this->validate($reg, 2);
                $r->dia = $this->validate($reg, 3);
                $r->hora = $this->validate($reg, 4);
                $r->minuto = $this->validate($reg, 5);
                $r->segundo = $this->validate($reg, 6);
                break;
            case 'dmy';
                preg_match('/([0-9]{1,2})(-|\/|\|)([0-9]{1,2})(-|\/|\|)([0-9]{2,4})/', $_fecha, $reg);
                $r->fecha = $this->validate($reg, 0);
                $r->ano = $this->validate($reg, 5);
                $r->mes = $this->validate($reg, 3);
                $r->dia = $this->validate($reg, 1);
                $r->hora = 0;
                $r->minuto = 0;
                $r->segundo = 0;
                break;
            case 'mdy';
                preg_match('/([0-9]{1,2})(-|\/|\|)([0-9]{1,2})(-|\/|\|)([0-9]{2,4})/', $_fecha, $reg);
                $r->fecha = $this->validate($reg, 0);
                $r->ano = $this->validate($reg, 5);
                $r->mes = $this->validate($reg, 1);
                $r->dia = $this->validate($reg, 3);
                $r->hora = 0;
                $r->minuto = 0;
                $r->segundo = 0;
                break;
            case 'ymd';
                preg_match('/([0-9]{2,4})(-|\/|\|)([0-9]{1,2})(-|\/|\|)([0-9]{1,2})/', $_fecha, $reg);
                $r->fecha = $this->validate($reg, 0);
                $r->ano = $this->validate($reg, 1);
                $r->mes = $this->validate($reg, 3);
                $r->dia = $this->validate($reg, 5);
                $r->hora = 0;
                $r->minuto = 0;
                $r->segundo = 0;
                break;
        endswitch;

        // Devuelve los datos según el formato de destino, o una cadena vacía si la fecha no es válida
        if ($r->mes > 0 && $r->dia > 0 && !checkdate((int) $r->mes, (int) $r->dia, (int) $r->ano)) {
            $return = '';
        } else {
            $return = date($_formato, mktime((int) $r->hora, (int) $r->minuto, (int) $r->segundo, (int) $r->mes, (int) $r->dia, (int) $r->ano));
        }
        return $return;
    }
    
    /**
     * 
     * @param type $array
     * @param type $pos
     * @return type
     */
    private function validate($array, $pos) {
        return (isset($array[$pos])) ? $array[$pos] : "";
    }

}
