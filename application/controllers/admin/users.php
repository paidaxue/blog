<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
2015年2月22日
*/

class Users extends ST_Auth_Controller{
    
    private $_data = array();
    
    private $_uid = 0;
    
    public function __construct(){
        parent::__construct();
        
        $this->load->library('auth');
        $this->load->library('form_validation');
        
        $this->auth->exceed('administrator');
        
        $this->_data['ParentPage'] = 'manage-posts';
        $this->_data['currentPage'] = 'manage-users';
        $this->_data['page_title'] = '管理用户';
    }
    
    public function index(){
        redirect('admin/users/manage');
    }
    
    public function manage(){
        
        $users = $this->users_mdl->get_users();
        
        $this->_data['users'] = $users;
        
        $this->load->view('admin/manage_users',$this->_data);        
    }
    
    public function user(){        
        if(FALSE === $this->uri->segment(4)){            
            $this->_add_user();
        }else{
            $uid = $this->security->xss_clean($this->uri->segment(4));
            is_numeric($uid) ? $this->_edit_user($uid) : show_error('禁止访问,危险操作');
        }                
    } 

    public function _add_user(){
                
        $this->_data['page_title'] = '增加用户';               
        $this->_data['group'] = 'contributor';
        
        $this->_load_validation_rules();
        
        if($this->form_validation->run() == FALSE){
            $this->load->view('admin/add_user',$this->_data);
        }else{
            $this->users_mdl->add_user(
                array(
                  'name' => $this->input->post('uname',TRUE),
                  'password' => $this->input->post('password',TRUE),
                  'mail' => $this->input->post('mail',TRUE),
                  'url' => $this->input->post('url',TRUE),
                  'screenName' => ($this->input->post('screenName')) ? $this->input->post('screenName',TRUE) : $this->input->post('uname',TRUE),
                  'created' => time(),
                  'activated' =>0,
                  'group' => $this->input->post('group',TRUE),
                )
                );
            $this->session->set_flashdata('success','成功添加一个用户账号');
            redirect('/admin/users/manage');
        }
        
        
    }
    
    private function _edit_user($uid){
        
        $this->_uid = $uid;
        $user = $this->users_mdl->get_user_by_id($uid);
        
        if(!$user){
            show_error('用户不存在或者已经被删除');
            exit();
        }
        
        $this->_data['uid'] = $user['uid'];
        $this->_data['uname'] = $user['name'];
        $this->_data['screenName'] = $user['screenName'];
        $this->_data['url'] = $user['url'];
        $this->_data['mail'] = $user['mail'];
        $this->_data['password'] = '';
        $this->_data['group'] = $user['group'];
        $this->_data['page_tiele'] = '编辑用户'.$user['name'];
        
        $this->_load_validation_rules();
        
        if($this->form_validation->run() == FALSE){
            $this->load->view('admin/add_user',$this->_data);
        }else{
            $this->users_mdl->update_user(
                $uid,
                array(
                    'password' => $this->input->post('password',TRUE),
                    'mail' => $this->input->post('mail',TRUE),
                    'url' => $this->input->post('url',TRUE),
                    'screenName' => ($this->input->post('screenName'))?$this->input->post('screenName',TRUE):$this->input->post('name',TRUE),
                    'group' => $this->input->post('group',TRUE),                    
                ),
                FALSE
                );
//             var_dump($this->users_mdl->get_user_by_id($uid));
            $this->session->set_flashdata('success', '成功修改用户'.$user['name'].'的账户信息');
            redirect('/admin/users/manage');
        }
    }
    
    public function remove($uid){
        
        if($uid == $this->user->uid){
            continue;
        }
        $this->users_mdl->remove_user($uid);
        $msg = ($deleted > 0)?'用户已经删除':'没有用户被删除';
        $notify = ($deleted > 0)?'success':'error';
        
        $this->session->set_flashdata($notify,$msg);
        redirect('/admin/users/manage');
    }
    
    private function _load_validation_rules(){
        $this->form_validation->set_rules('uname','用户名','required');
        $this->form_validation->set_rules('password','新密码','required|min_length[6]|trim|matches[confirm]');
        $this->form_validation->set_rules('confirm','确认密码','required|min_length[6]|trim');
        $this->form_validation->set_rules('screenName','昵称','trim');
        $this->form_validation->set_rules('url','个人主页','trim|prep_url');
        $this->form_validation->set_rules('mail','邮箱地址','required|trim|valid_email');
        $this->form_validation->set_rules('group','用户组','trim');
    }
}


/*
 End of file
 Location:users.php
 */