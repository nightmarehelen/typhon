<?php

class Utils{

    //判断是否为合法的json格式
    public static function isJson($string){
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function var2str($var){
        //echo '<pre>'; // This is for correct handling of newlines
        ob_start();
        var_dump($var);
        $a=ob_get_contents();
        ob_end_clean();
        //echo htmlspecialchars($a,ENT_QUOTES); // Escape every HTML special chars (especially > and < )
        //echo '</pre>';
	    return $a;
    }

    public static function setSessionKey($id, $username, $password){
        Logger::getRootLogger()->debug("Utils::getSessionKey");
        $key = md5($id.$username.$password);
        Logger::getRootLogger()->debug("username = ".$username);
        Logger::getRootLogger()->debug("password = ".$password);
        Logger::getRootLogger()->debug("key = ".$key);
        
        //设置会话key
        Logger::getRootLogger()->debug("key = ".$key);
        Logger::getRootLogger()->debug("uid = ".$id);
        //Yii::app()->session->add($key, $id);
        $_SESSION[$key] = $id;
        Logger::getRootLogger()->debug("session = ".Utils::var2str($_SESSION));
        
        return $key;
    }

    public static function getSeesionKey(){
    	if(isset($_POST['request_json'])){
    		Logger::getRootLogger()->debug("_POST['request_json'] = ".$_POST['request_json']);
    		$request = json_decode($_POST['request_json'], true);
    		Logger::getRootLogger()->debug("request = ".Utils::var2str($request));
    		$session_key = $request["SESSION_KEY"];
    		Logger::getRootLogger()->debug("session_key = ".$session_key);
    		return $session_key;
    	}
    	return null;
    }
    
    
    public static function printRequestHeader(){
    	Logger::getRootLogger()->debug("Utils::printRequestHeader ");
    	$headers = array();
    	foreach ($_SERVER as $key => $value) {
    		if ('HTTP_' == substr($key, 0, 5)) {
    			$headers[str_replace('_', '-', substr($key, 5))] = $value;
    			Logger::getRootLogger()->debug("_SERVER[".$key."]  =".$value);
    		}
    	}
    }

    
    /*
    *校验输入报文，如果POST请求不包含request_json或者非法的json格式，返回错误
    */
    public static function validate_request(){
        $response = new Response();
        if(!isset($_POST['request_json'])){
            $response->status = Response::STATUS_ERROR;
            $response->message = "无request_json项";
            $response->error_code = "0001";
            Logger::getRootLogger()->error("无request_json项");
            return $response;
        }
        
        Logger::getRootLogger()->debug("request_json = ".$_POST['request_json']);

        if(!Utils::isJson($_POST['request_json'])){
            $response->status = Response::STATUS_ERROR;
            $response->message = "JSON文件格式错误";
            Logger::getRootLogger()->error("JSON文件格式错误:".$response->message.$user_register_infor);
            return $response;
        }
        
        return null;
    }


    public static function getCurrentUserID(){
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : -1;
    }
    
    public static function isCurrentUserLogin(){
        if(self::getCurrentUserID() === -1)
            return false;
        return true;
    }
}