<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
2015年2月15日
*/

class Auth{
    
    private $_user=array();
    
    private $_hasLogin=NULL;
    
    public $groups=array('administrator'=>0,'editor'=>1,'contributor'=>2);
    
    private $_CI;
    
    public function __construct(){
                       
        $this->_CI = & get_instance();       
        
        $this->_user = unserialize($this->_CI->session->userdata('user'));
        
        //log_message('debug', "STBLOG: Authentication library Class Initialized");
    }
    
    public function hasLogin(){
        
        if(NULL !== $this->_hasLogin){
            return $this->_hasLogin;
        }else {
            if(!empty($this->_user)&& NULL !== $this->_user['uid']){
                $user=$this->_CI->users_mdl->get_user_by_id($this->_user['uid']);
                if($user && $user['token'] == $this->_user['token']){
                    $user['activated'] = time();
                    //更新最后活跃时间
                    $this->_CI->users_mdl->update_user($this->_user['uid'],$user);
                    return ($this->_hasLogin = TRUE);
                }
            }
            
            return ($this->_hasLogin = FALSE);
            
        }
        
    }
    
    public function process_login($user){
        $this->_user=$user;
        $this->_user['logged'] = now();
        $this->_user['activated']=$user['logged'];
        $this->_user['token']=sha1(now().rand());        

		if($this->_CI->users_mdl->update_user($this->_user['uid'],$this->_user)){
			
			//设置session
			$this->_set_session();
			
			//是否登录设置成TRUE
			$this->_hasLogin = TRUE;
			return TRUE;
			
		}
		return FALSE;
    }
    
    public function _set_session(){
        $session_data=array('user'=>serialize($this->_user));
        $this->_CI->session->set_userdata($session_data);
    }
    
	public function exceed($group,$return = false){
		
		//如果权限验证通过,那么返回TRUE	
		
// 	    var_dump($this->_user);
	    
		if(array_key_exists($group,$this->groups) && $this->groups[$this->_user['group']] <= $this->groups[$group])
		{
			return TRUE;
		}
		
		//权限验证未通过，同时为返回模式
		if($return){
			return FALSE;
		}
		//非返回模式
		show_error('禁止访问,你的权限不足');
		return;
	}
	
	public function process_logout(){
	    $this->_CI->session->sess_destroy();
	    redirect('admin/login');
	}                   
}


/*
 End of file
 Location:Auth.php
 */