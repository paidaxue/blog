<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
2015年2月13日
*/

class Login extends CI_Controller{
    
    private $_data;
    
    public $referrer;
    
    public function __construct(){
        parent::__construct();
        
        $this->load->library('auth');
        
        $this->load->library('form_validation');
        
        $this->load->model('users_mdl','users');
        
        $this->load->library('common');
        
        $this->_check_referrer();
        
    }
    
    private function _check_referrer(){
        $ref = $this->input->get('ref',TRUE);
        
        $this->referrer = (!empty($ref))?$ref:'/admin/dashboard';
    }
    
    public function index(){
        
         if($this->auth->hasLogin()){
            redirect($this->referrer);
        }
        //前端验证输入的用户名密码
        $this->form_validation->set_rules('name','用户名','required');
        $this->form_validation->set_rules('password','密码','required');
        
        
        if($this->form_validation->run() == FALSE){
            $this->load->view('admin/login',$this->_data);
        }else {
            $user = $this->users->validate_user(
                $this->input->post('name',TRUE),
                $this->input->post('password',TRUE)
            );
            if(!empty($user)){
                if($this->auth->process_login($user)){
                    redirect($this->referrer);
    
                }
            }else{
				//先休眠3秒,可以稍微防止一下爆破
 				sleep(3);
				
				$this->session->set_flashdata('login_error','TRUE');
				$this->_data['login_error_msg'] = '用户名或密码无效';
				$this->load->view('admin/login',$this->_data);
            }
        }
        
    }
    
    //用户登出
    public function logout(){
        $this->auth->process_logout();
    }
    
}


/*
 End of file
 Location:login.php
 */