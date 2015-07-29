<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Advertisement extends CI_Controller {
    private $temp_img_dir;
    private $img_file_prefix;

    public function __construct(){
        parent::__construct();
        $this->temp_img_dir=  $_SERVER['DOCUMENT_ROOT']."\data\img";
        $this->img_file_prefix = "adv";
        $this->load->model('advertisement_model');
    }

    public function create(){
        Logger::getRootLogger()->debug("Advertisement::create");

        $response = Utils::validate_request();
        if(Utils::validate_request() !== null){
            echo Response::getResponseJson($response);
            return;
        }
        
        $adv_infor = $_POST['request_json'];
        Logger::getRootLogger()->debug("adv_infor = ".$adv_infor);

        $adv_infor_array = json_decode($adv_infor, true);
        Logger::getRootLogger()->debug("dump adv_infor_array:".Utils::var2str($adv_infor_array));
        
        $new_name = null;
        //该广告包含图片
        if(isset($adv_infor_array["DATA"]["image"]) && $adv_infor_array["DATA"]["image"] == "true"){
            //获取上传图片
            Logger::getRootLogger()->debug("_FILES:".Utils::var2str($_FILES));
            $new_name = $this->get_update_img();
            if(!$new_name){
                $response = new Response();
                $response->status = Response::STATUS_ERROR;
                $response->error_code = "0014";
                $response->message = "获取上传图片失败";
                echo Response::getResponseJson($response);
                return;
            }
            
            $adv_infor_array["DATA"]['image'] = "data/img/".basename($new_name);
        }
        
        
        Logger::getRootLogger()->debug("adv_infor_array[DATA][image]:".Utils::var2str($adv_infor_array["DATA"]['image']));
        $response = $this->advertisement_model->create($adv_infor_array["DATA"]);
        
        if($response != null){
            echo Response::getResponseJson($response);
            return;
        }
        
        $response = new Response();
        $response->status = Response::STATUS_OK;
        $response->message = "恭喜您广告发布成功";
        echo Response::getResponseJson($response);
    }
 

    public function update(){
        Logger::getRootLogger()->debug("Advertisement::update");

        $response = Utils::validate_request();
        if(Utils::validate_request() !== null){
            echo Response::getResponseJson($response);
            return;
        }
        
        $adv_infor = $_POST['request_json'];
        Logger::getRootLogger()->debug("adv_infor = ".$adv_infor);

        $adv_infor_array = json_decode($adv_infor, true);
        Logger::getRootLogger()->debug("dump adv_infor_array:".Utils::var2str($adv_infor_array));
        
        $new_name = null;
        //该广告包含图片
        if(isset($adv_infor_array["DATA"]["image"]) && $adv_infor_array["DATA"]["image"] == "true"){
            //获取上传图片
            Logger::getRootLogger()->debug("_FILES:".Utils::var2str($_FILES));
            $new_name = $this->get_update_img();
            if(!$new_name){
                $response = new Response();
                $response->status = Response::STATUS_ERROR;
                $response->error_code = "0014";
                $response->message = "获取上传图片失败";
                echo Response::getResponseJson($response);
                return;
            }
            
            $adv_infor_array["DATA"]['image'] = "data/img/".basename($new_name);
        }
        
        
        Logger::getRootLogger()->debug("adv_infor_array[DATA][image]:".Utils::var2str($adv_infor_array["DATA"]['image']));
        $response = $this->advertisement_model->update($adv_infor_array["DATA"]);
        
        if($response != null){
            echo Response::getResponseJson($response);
            return;
        }
        
        $response = new Response();
        $response->status = Response::STATUS_OK;
        $response->message = "恭喜您广告更新成功";
        echo Response::getResponseJson($response); 
    }


    public function get_update_img(){
        if(!isset($_FILES['adv_img1']))
            return false;

        if ($_FILES["adv_img1"]["error"] > 0){
            Logger::getRootLogger()->debug("get upload file failed:". $_FILES["adv_img1"]["error"]);
            return false;
        }

        $filename = tempnam($this->temp_img_dir,$this->img_file_prefix);
        if (!unlink($filename)){
            Logger::getRootLogger()->error("delete temp file failed");
            return false;
        }

        if(!$filename){
            Logger::getRootLogger()->error("get temp file name failed:". $_FILES["adv_img1"]["error"]);
            return false;
        }
        
        $file_path = pathinfo($_FILES["adv_img1"]["name"]);
        Logger::getRootLogger()->debug("dump file_path:".Utils::var2str($file_path));
        $file_path2 = pathinfo($filename);
        Logger::getRootLogger()->debug("dump file_path2:".Utils::var2str($file_path2));
        
        $new_name = "";
        if(!empty($file_path['extension']))
            $new_name = str_replace("\\", "/", $file_path2['dirname'])."/".$file_path2['filename'].".".$file_path['extension'];
        else
            $new_name = str_replace("\\", "/", $file_path2['dirname'])."/".$file_path2['filename'];
        
        Logger::getRootLogger()->debug("dump new_name:".$new_name);
        
        if(!move_uploaded_file($_FILES["adv_img1"]["tmp_name"], $new_name)){
            Logger::getRootLogger()->error("move_uploaded_file file failed");
            return false;
        }

        return $new_name;
    }
}