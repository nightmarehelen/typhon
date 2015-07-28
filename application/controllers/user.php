<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('user_model');
    }
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function login()
	{
        Logger::getRootLogger()->debug("User::login");
        $response = Utils::validate_request();
        if(Utils::validate_request() !== null){
            echo Response::getResponseJson($response);
            return;
        }
        
        $user_login_infor = $_POST['request_json'];
        Logger::getRootLogger()->debug("user_login_infor = ".$user_login_infor);

        $user_infor_array = json_decode($user_login_infor, true);
        Logger::getRootLogger()->debug("dump user_infor_array:".Utils::var2str($user_infor_array));
        
        $response = $this->user_model->login($user_infor_array["DATA"]);

        
        echo Response::getResponseJson($response);
	}

    public function register()
	{
        Logger::getRootLogger()->debug("User::register");

        $response = Utils::validate_request();
        if(Utils::validate_request() !== null){
            echo Response::getResponseJson($response);
            return;
        }

       
        $user_register_infor = $_POST['request_json'];
        Logger::getRootLogger()->debug("user_register_infor = ".$user_register_infor);

        $user_infor_array = json_decode($user_register_infor, true);
        Logger::getRootLogger()->debug("dump user_infor_array:".Utils::var2str($user_infor_array));

        $response = $this->user_model->create_user($user_infor_array["DATA"]);
        
        echo Response::getResponseJson($response);
	}


    public function logout(){
        Logger::getRootLogger()->debug("User::logout");
        unset($_SESSION['user_id']);
        $response = new Response();
        $response->status = Response::STATUS_OK;
        $response->message = "注销成功";
        echo Response::getResponseJson($response);
    }



    public function queryCurrentLoginUserInfor(){
        Logger::getRootLogger()->debug("User::queryCurrentLoginUserInfor");
        Logger::getRootLogger()->debug("_SESSION['user_id'] = ".$_SESSION['user_id']);
        $response = new Response();
        $response->status = Response::STATUS_OK;
        $response->message = "_SESSION['user_id'] = ".$_SESSION['user_id'];
        echo Response::getResponseJson($response);
    }

    
    public function update(){
        Logger::getRootLogger()->debug("User::update");
        
        $response = Utils::validate_request();
        if(Utils::validate_request() !== null){
            echo Response::getResponseJson($response);
            return;
        }
      
        $user_update_infor = $_POST['request_json'];
        Logger::getRootLogger()->debug("user_update_infor = ".$user_update_infor);

        $user_infor_array = json_decode($user_update_infor, true);
        Logger::getRootLogger()->debug("dump user_infor_array:".Utils::var2str($user_infor_array));

        $response = $this->user_model->update_user_infor($user_infor_array["DATA"]);
        echo Response::getResponseJson($response);
    }
}
