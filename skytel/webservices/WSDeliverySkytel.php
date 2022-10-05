<?php
require_once '../../nusoap/nusoap.php';

class WSSkytel {
    
  public $wsdl;
  public $param;
  public $function_name;

  public function __construct($wsdl,$param,$function_name){
      
    $this->wsdl = $wsdl;
    $this->param = $param;
    $this->function_name = $function_name;
  }
  
  public function get_result_calidad() {
      
        try {$cliente = new nusoap_client("$this->wsdl", 'wsdl');}
        catch (Exception $e) {
            $error = $cliente->getError();
            if ($error) {
                $_SESSION['fault_sync'] = $error.'.</br>'.htmlspecialchars($cliente->getDebug(), ENT_QUOTES).'</br>';
                return false;
            }
        }

        $result = $cliente->call( $this->function_name, $this->param );
        return($result);
  }
  
}