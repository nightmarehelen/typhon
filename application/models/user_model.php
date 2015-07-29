<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    private $email_pattern = "/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i";
    private $cellphone_pattern = "/1[3458]{1}\d{9}$/";
    public function __construct(){
        
    }

    
    /*user information format
    *
            "id"  : "ID",
            "name"  : "Name",
            "password"  : "Password",
            "cellphone"  : "Cellphone",
            "email"  : "Email",
            "position"  : "Position",
            "type"  : "Type",
            "credit_values"  : "Credit Values",
            "register_time"  : "Register Time",
            "last_login_time"  : "Last Login Time"
    */
    public function create_user($user_infor){
        Logger::getRootLogger()->debug("User_model::create_user");

        $response = $this->create_user_validate($user_infor);
        if($response !== null){
            return $response;
        }
        
        
        $user_infor["register_time"] = date('Y-m-d H:i:s');
        $user_infor['credit_values'] = 0;
        
        $db = new DB();
        $db->connect();
        $sql = "insert into user(name, password,cellphone, email,credit_values,register_time) values('".$user_infor["name"]."', '"
                                                                           .md5($user_infor["password"])."', '"
                                                                           .$user_infor["cellphone"]."', '"
                                                                           .$user_infor["email"]."',0,'"
                                                                           .$user_infor["register_time"]."')";
        Logger::getRootLogger()->debug("sql = ".$sql);                                                                   
        $response = $db->executeUpdateAndInsert($sql);
        if($response !== null){
            if(strpos($response->message, "mysqli->errno=1062")){
                $response->error_code = "0009";
                $response->message = $response->message."注册用户名已存在";
            }
            return $response;
        }
        
        $response = new Response();
        $response->status = Response::STATUS_OK;
        $response->message = "恭喜您注册成功";
        return $response;
    }

    //创建用户信息校验,如果没有错误，返回null
    public function create_user_validate($user_infor){
        $response = new Response();
        
        if(!isset($user_infor["name"]) || !isset($user_infor["password"]) ){
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0006";
            $response->message = "用户名密码不得为空";
            return $response;
        }

        if(!isset($user_infor["email"])){
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0002";
            $response->message = "email地址不得为空";
            return $response;
        }

        if(!isset($user_infor['cellphone'])){
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0003";
            $response->message = "手机号不得为空";
            return $response;
        }
        
        if(!preg_match($this->email_pattern,$user_infor['email'])){
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0004";
            $response->message = "邮箱格式不合法";
            return $response;
        }

        if(!preg_match($this->cellphone_pattern,$user_infor['cellphone'])){
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0005";
            $response->message = "无效的手机号码";
            return $response;
        }
        
        return null;
    }
    
    public function login($user_infor){
        Logger::getRootLogger()->debug("User_model::login");

        $response = $this->login_validate($user_infor);
        if($response !== null){
            return $response;
        }

        $db = new DB();
        $db->connect();
        $sql = "select * from user where name ='".$user_infor["name"]."'";
        Logger::getRootLogger()->debug("sql = ".$sql); 
        
        $res = $db->executeQuery($sql);
        if ($res->num_rows == 0){    
            $res->close();
            $response = new Response();
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0010";
            $response->message = "用户名不存在";
            return $response;
        }
        Logger::getRootLogger()->debug("res = ".Utils::var2str($res));
        

        $sql = "select id from user where name ='".$user_infor["name"]."' and password = '".md5($user_infor['password'])."'";
        Logger::getRootLogger()->debug("sql = ".$sql); 
        
        $res = $db->executeQuery($sql);
        if ($res->num_rows == 0){    
            $res->close();
            $response = new Response();
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0011";
            $response->message = "密码错误";
            return $response;
        }else{
            $row = $res->fetch_assoc();
            $_SESSION['user_id'] = $row['id'];
            Logger::getRootLogger()->debug("set session[user_id] = ".$_SESSION['user_id']);
        }

        Logger::getRootLogger()->debug("res = ".Utils::var2str($res));
        

        $response = new Response();
        $response->status = Response::STATUS_OK;
        $response->message = "恭喜您登陆成功";
        return $response;

    }
    
     //创建用户信息校验,如果没有错误，返回null
    public function login_validate($user_infor){
        $response = new Response();
        
        if(!isset($user_infor["name"]) || !isset($user_infor["password"]) ){
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0006";
            $response->message = "用户名密码不得为空";
            return $response;
        }

        return null;
    }

    public function getUserID($user_infor){
        $db = new DB();
        $db->connect();
        $sql = "select * from user where name ='".$user_infor["name"]."' and password = '".md5($user_infor['password'])."'";
        Logger::getRootLogger()->debug("sql = ".$sql); 
        
        $res = $db->executeQuery($sql);
        if ($res->num_rows == 0){    
            $res->close();
            $response = new Response();
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0011";
            $response->message = "密码错误";
            return $response;
        }
        Logger::getRootLogger()->debug("res = ".Utils::var2str($res));


    }

    public function update_user_infor($user_infor){
        Logger::getRootLogger()->debug("User_model::update_user_infor");
        $response = $this->update_user_validate($user_infor);
        if($response !== null){
            return $response;
        }
        
        $db = new DB();
        $db->connect();
        
        $response = null;
        if(isset($user_infor['email'])){
            $sql = "update user set email = '".$user_infor['email']."' where id = ".Utils::getCurrentUserID();
            Logger::getRootLogger()->debug($sql);
            $response = $db->executeUpdateAndInsert($sql);
            if($response instanceof Response)
                return $response;
        }
        
        if(isset($user_infor['cellphone'])){
            $sql = "update user set cellphone = '".$user_infor['cellphone']."' where id = ".Utils::getCurrentUserID();
            Logger::getRootLogger()->debug($sql);
            $response = $db->executeUpdateAndInsert($sql);
            if($response instanceof Response)
                return $response;
        }
        
        if($response == 0){
            $response = new Response();
            $response->status = Response::STATUS_ERROR;
            $response->message = "未找到对应用户，用户信息更新失败";
        }


        $response = new Response();
        $response->status = Response::STATUS_OK;
        $response->message = "恭喜您信息更新成功";
        return $response;
    }

    public function update_user_validate($user_infor){
        Logger::getRootLogger()->debug("User_model::update_user_validate");
        if(isset($user_infor['email']) && !preg_match($this->email_pattern,$user_infor['email'])){
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0004";
            $response->message = "邮箱格式不合法";
            return $response;
        }

        if(isset($user_infor['cellphone']) && !preg_match($this->cellphone_pattern,$user_infor['cellphone'])){
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0005";
            $response->message = "无效的手机号码";
            return $response;
        }
         return null;
    }
}