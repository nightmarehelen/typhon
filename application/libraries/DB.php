<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
class DB{
    private $hostname;
    private $port;
    private $username;
    private $password;
    private $database;
    private $mysqli;
    public function __construct(){
        $this->hostname = "127.0.0.1";
        $this->port = "3306";
        $this->username = "root";
        $this->password = "123";
        $this->database = "dalaba";
        $mysqli = null;
    }

    public function connect(){
        $response = new Response();
        $this->mysqli = new mysqli($this->hostname.":".$this->port,$this->username,$this->password, $this->database);
        if (!$this->mysqli->connect_errno) {
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0007";
            $response->message = "数据库连接失败:connect_error=".$this->mysqli->connect_error;
            return $response;
        }

        return null;
    }
    

    public function executeQuery($sql){
        $result=$this->mysqli->query($sql);
        return $result;
    }
    
    public function executeUpdateAndInsert($sql){
        $response = new Response();
   
        $result = $this->mysqli->query($sql);

        if(!$result){
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0008";
            $response->message = "数据库操作失败:mysqli->errno=".$this->mysqli->errno."   mysqli->error = ".$this->mysqli->error;
            return $response;
        }

        return $this->mysqli->affected_rows;
    }

    function __destruct(){
        $this->mysqli->close();
    }

}

