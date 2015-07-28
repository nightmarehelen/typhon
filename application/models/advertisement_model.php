<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Advertisement_model extends CI_Model {
    
    public function __construct(){
        $this->load->database();
    }

    
  
    public function create($adv_infor){
        Logger::getRootLogger()->debug("Advertisement_model::create");

        
        $response = $this->create_adv_validate($adv_infor);
        if($response !== null){
            return $response;
        }
        
        
        $uid = Utils::getCurrentUserID();
        
        $publish_time = date('Y-m-d H:i:s');
        $db = new DB();
        $db->connect();
        
        $sql = "insert into advertisement(uid, type,publish_position, publish_time,title,text_content,image, fresh_content) values($uid, '"
                                                                           .$adv_infor['type']."', '"
                                                                           .$adv_infor["publish_position"]."','"
                                                                           .$publish_time."','"
                                                                           .$adv_infor["title"]."','"
                                                                           .$adv_infor["text_content"]."','"
                                                                           .$adv_infor["image"]."','"
                                                                           .$adv_infor["fresh_content"]."')";
        Logger::getRootLogger()->debug("sql = ".$sql);                                                                   
        $response = $db->executeUpdateAndInsert($sql);

        if($response !== null){
            return $response;
        }
        
        

        return null;
    }


    public function create_adv_validate($adv_infor){
        $response = new Response();
        
        if(!Utils::isCurrentUserLogin()){
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0013";
            $response->message = "用户尚未登陆，没有发布广告的权限";
            return $response;
        }

        

        if(!isset($adv_infor["title"]) || !isset($adv_infor["text_content"])  || empty($adv_infor["title"]) || empty($adv_infor["text_content"])){
            $response->status = Response::STATUS_ERROR;
            $response->error_code = "0012";
            $response->message = "广告标题和广告文本内容不得为空";
            return $response;
        }
        
        return null;
    }
}