<?php

class Pages extends CI_Controller {

    public function printRequestHeader()
    {
    	$headers = array();
    	foreach ($_SERVER as $key => $value) {
    		if ('HTTP_' == substr($key, 0, 5)) {
    			$headers[str_replace('_', '-', substr($key, 5))] = $value;
    			echo ("_SERVER[".$key."]  =".$value."<br>");
    		}
    	}
    }

    public function a(){


    }

  public function set($page = 'home')
  {
        $this->printRequestHeader();
        echo "What a fucking day!";
        $this->load->library('session');
        $array = array("name" => "chenyang");
        $_SESSION['name'] ="chenyang" ;
        //$this->session->set_userdata($array);
        
  }

   public function get(){
      $this->printRequestHeader();
      $this->load->library('session');
      echo $this->session->userdata('name');
      //echo $_SESSION['name'];
      //session_destroy();
   }
}