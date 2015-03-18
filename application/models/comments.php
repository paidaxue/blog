<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
2015年3月1日
*/


//评论相关model
class Comments_mdl extends CI_Model{
    
    const TBL_USERS = 'users';
    const TBL_COMMENTS = 'comments';
    
    //类型：评论/引用
    private $_type = array('comment','trackback');
    
    //状态：通过/待审核/垃圾
    private $_status = array('approved','waiting','spam');
    
    
    public function __construct(){       
         
        parent::__construct();        
        log_message('debug',"STBLOG:Comments Model Class Initialized");        
    }
    
    /**
     * 获取评论列表，支持分页
     * 
     *@access public 
     *
     *@param int $pid 
     */
    
    
    
    
}








/*
 End of file
 Location:comments.php
 */