<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
2015年2月15日
*/

class ST_Auth_Controller extends CI_Controller{
    
    public function __construct(){
        parent::__construct();
        
        $this->load->library('auth');
        $this->load->library('form_validation');
        $this->load->library('user');
        $this->load->model('users_mdl');
        
        if(!$this->auth->hasLogin()){
            redirect('admin/login');
        }
    }
}


/*
 End of file
 Location:MY_Controller.php
 */