<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
2015年2月13日
*/

class Users_mdl extends CI_Model{
    
    const TBL_USERS = 'users';
    
    private $_unique_key = array('name','screenName','mail');
    
    public function __construct(){           
        parent::__construct();
            
        $this->load->database();
    }
    
    //检查用户是否通过验证,传入参数:输入的用户名,密码
    public function validate_user($username,$password){
        
        $data = FALSE;
        $this->db->where('name',$username);
        $query = $this->db->get(self::TBL_USERS);
        
        if($query->num_rows() == 1){
            $data = $query->row_array();
        }       
		if(!empty($data)){
			//调用公共类Common的方法,验证密码,hash_Validate(输入的密码,数据库查询来的密码)对比
			//如果密码正确,那么返回用户信息$data,如果密码错误,$data=FALSE;
			$data = (Common::hash_Validate($password,$data['password'])) ? $data:FALSE;
		}		
		//释放掉这个查询
		$query->free_result();	
		return $data;
    }
    
    //修改用户信息
    public function update_user($uid,$data,$hashed=TRUE){
        
        //如果密码没有加密,那么给密码加密
        if(!$hashed){
            $data['password']=Common::do_hash($data['password']);
        }
        
        $this->db->where('uid',intval($uid));
        $this->db->update(self::TBL_USERS,$data);
        
        //如果更新的条数>0,返回true
        return ($this->db->affected_rows()>0)?TRUE:FALSE;
    }
    
    //通过$uid获取单个用户信息
    public function get_user_by_id($uid){
        
        $data=array();
        
        $this->db->select('*')->from(self::TBL_USERS)->where('uid',$uid)->limit(1);
        $query=$this->db->get();
        if($query->num_rows() == 1){
            $data=$query->row_array();            
        }
        $query->free_result();
        return $data;
    }
    
    //获取所有用户信息
    public function get_users(){
        return $this->db->get(self::TBL_USERS);
    }
    
    public function add_user($data){
        
        $data['password'] = Common::do_hash($data['password']);
        $this->db->insert(self::TBL_USERS,$data);
        return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
    } 
    
    public function  remove_user($uid){
        $this->db->delete(self::TBL_USERS,array('uid' => intval($uid)));
        return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
    }
}


/*
 End of file
 Location:users_mdl.php
 */