<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Response{
    const STATUS_OK = 'OK';
    const STATUS_ERROR = 'ERROR';

    public $version = "1.0.0";
    public $status =  self::STATUS_OK;
    public $message = "";
    public $response_data = "";
    public $request_data = "";
    public $error_code = "";

    public function toString(){
        return json_encode($this);
    }
    
    public function __construct(){
        // Do something with $params
        $version = "1.0.0";
    }

    public static function getResponseJson($response){

        $result = 
         '{
            "VERSION":"'.$response->version.'",
            "STATUS":"'.$response->status.'",
            "ERROR_CODE":"'.$response->error_code.'",
            "DATA":{
                "message":"'.$response->message.'",
                "response_data":"'.$response->response_data.'",
                "request_data":"'.$_POST['request_json'].'",
                
            }   
                
        }';
        return $result;
    }
}

/* End of file Response.php */




