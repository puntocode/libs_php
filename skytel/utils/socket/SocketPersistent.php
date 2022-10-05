<?php

namespace skytel\utils\socket;

/**
 * Esta clase establece la conexion.
 * Tiene métodos para escribir, y leer.
 * 
 * OBS: Cierra la conexion bajo pedido.
 * 
 * @author Endrigo Rivas <endrigo.rivas@gmail.com>
 * 
 * $Id: SocketPersistent.php 11965 2015-01-13 19:47:42Z endrigo $
 */
class SocketPersistent {

    private $ip;
    private $port;
    private $timeoutConnect;
    private $timeoutWrite;
    private $timeoutRead;
    private $lastError;
    private $socket;
    
    /**
     * 
     * Al generar la clase abre el socket.
     * 
     * @param type $IP
     * @param type $port
     * @param type $timeoutConnect
     * @param type $timeoutWrite
     * @param type $timeoutRead
     * @return boolean
     */
    function __construct($IP, $port, $timeoutConnect = 10, $timeoutWrite = 10, $timeoutRead = 10) {
        $this->ip = $IP;
        $this->port = $port;
        $this->timeoutConnect = $timeoutConnect;
        $this->timeoutWrite = $timeoutWrite;
        $this->timeoutRead = $timeoutRead;
        $this->lastError = "";

        //http://www.codeproject.com/Tips/418814/Socket-Programming-in-PHP
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false) {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            $this->lastError = "Could not create socket: [$errorcode] $errormsg";

            return false;
        }

        /**
         * Timeout seted before connect to socket.
         * http://www.php.net/manual/es/function.socket-get-option.php
         * 
         * sec (seconds)
         * usec (miliseconds)
         */
        ini_set("default_socket_timeout", $this->timeoutConnect);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $this->timeoutRead, 'usec' => 0));
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => $this->timeoutWrite, 'usec' => 0));

        $ok = socket_connect($this->socket, $this->ip, $this->port);
        if ($ok === false) {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            $this->lastError = "Could not connect to server: [$errorcode] $errormsg";

            // close socket
            socket_close($this->socket);

            return false;
        }

        //Tiempo de espera en que el socket enlaza con el puerto.
        sleep(1);
    }

    /**
     * Envía y lee el resultado del socket
     * 
     * @return string El string devuelto por el servidor. En caso de error, false.
     */
    public function socketTransmission($message) {

        if (empty($message)) {
            throw new InvalidArgumentException("Empty message!");
        }

        // send string to server
        $bytes = socket_write($this->socket, $message, strlen($message));
        if ($bytes === false) {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            $this->lastError = "Could not send data to server: [$errorcode] $errormsg";

            // close socket
            socket_close($this->socket);

            return false;
        }

        // get server response
        $result = socket_read($this->socket, 2048);
        if ($result === false) {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            $this->lastError = "Could not read server response: [$errorcode] $errormsg";

            // close socket
            socket_close($this->socket);

            return false;
        }

        //Message pumped
        //OBS: Only for debug!.
//        echo(var_dump($result));

        return $result;
    }
    
    /**
     * Cierra la conexion
     */
    public function close() {
        
        // close socket
        socket_close($this->socket);
    }

    /**
     * 
     * Return info about the last error ocurred.
     * 
     * @return type
     */
    public function lastError() {
        return $this->lastError;
    }

}
