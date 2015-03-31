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

class ST_Controller extends CI_Controller{
    
    function __construct(){
        
        parent::__construct();
        
        $this->load->switch_theme_off();

    }
    
    /**
     * 加载某个主题页面下的VIEW
     *
     * 第1/2/4个参数分别对应CI原有的load view中的第1/2/3参数，这里的第三个参数用于一些特殊场合：
     * 当整站缓存功能被开启时，为了避免当前被操作的页面缓存，可以设置第三个参数为FALSE避免。
     *
     *
     * @access   public
     * @param    string
     * @param    array
     * @param	 bool
     * @param    bool
     * @return   void
     */
    function load_theme_view($view,$vars=array(),$cached=TRUE,$return=FALSE){
    
        /** 加载对应主题下的view */
        //DIRECTORY_SEPARATOR:目录分隔符，是定义php的内置常量。在调试机器上，在windows我们习惯性的使用“\”作为文件分隔符，但是在linux上系统不认识这个标识，于是就要引入这个php内置常量了：DIRECTORY_SEPARATOR
        if(file_exists(FCPATH. ST_THEMES_DIR. DIRECTORY_SEPARATOR . setting_item('current_theme'). DIRECTORY_SEPARATOR . $view .'.php'))
        {
            echo $this->load->view($view, $vars,$return);
        }
        else
        {
            show_404();
        }
    
        /** 是否开启缓存? */
        if(1 == intval(setting_item('cache_enabled')) && $cached)
        {
            $cache_expired = setting_item('cache_expire_time');
            	
            $cache_expired = ($cache_expired && is_numeric($cache_expired)) ? intval($cache_expired) : 60;
            	
            /** 开启缓存 */
            $this->output->cache($cache_expired);
        }
    
    }
    
    
    
}


/*
 End of file
 Location:MY_Controller.php
 */