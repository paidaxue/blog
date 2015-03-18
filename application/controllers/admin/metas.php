<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
2015年2月25日
*/

class Metas extends ST_Auth_Controller{
    
    private $_data = array();    
    private $_mid = 0;
    private $_type = 'category';
    private $_map = array('category' => '分类', 'tag' => '标签');
    
    public function __construct(){
        
        parent::__construct();
        
        $this->load->model('metas_mdl');
        
        $this->auth->exceed('editor');
        
        $this->_data['page_title'] = '分类与标签';
        $this->_data['parentPage'] = 'manage-posts';
        $this->_data['currentPage'] = 'manage-metas';        
    }
    
    public function index(){      
       redirect('admin/metas/manage');
    }
    
    public function manage($type = 'category',$mid=NULL){
        
        $this->_data['type'] = $type;
        $this->_data[$type] = $this->metas_mdl->list_metas($type);
        
//         var_dump($this->_data[$type]);
        
        if($mid && is_numeric($mid)){
            
            $this->_data['mid'] = $mid;
            
            $meta = $this->metas_mdl->get_meta('BYID',$mid);
            
            $this->_data['name'] = $meta->name;
            $this->_data['slug'] = $meta->slug;
            $this->_data['description'] = $meta->description;
            
            unset($meta);
        }
        
        $this->_operate($type,$mid);
        $this->load->view('admin/manage_metas',$this->_data);                
    }
    
    //添加或者编辑metas
    public function _operate($type,$mid){
        
        $this->_type = $type;
        $this->_mid = $mid;
        
        $this->_load_validation_rules();
        
        if($this->form_validation->run() === FALSE){
            return;
        }else{            
            $action = $this->input->post('do',TRUE);
            $name = $this->input->post('name',TRUE);
            $slug = $this->input->post('slug',TRUE);
            $description = $this->input->post('description',TRUE);

            $data = array(
                'name' => $name,
                'type' => $type,
                'slug' => $slug,
                'description' => (!$description) ? NULL : $description                
            );            
            
            if('insert' == $action){
                $this->metas_mdl->add_meta($data);            
                $this->session->set_flashdata('success',$this->_map[$type].'添加成功');            
            }
            	
            if('update' == $action){
                $this->metas_mdl->update_meta($mid,$data);            
                $this->session->set_flashdata('success',$this->_map[$type].'更新成功');
            }
            
            redirect('admin/metas/manage/'.$this->_type);            
        }                
    }
    
    public function _load_validation_rules(){
        $this->form_validation->set_rules('name','名称','required|trim|htmlspecialchars');
        
        if('category' == $this->_type){
            $this->form_validation->set_rules('slug', '缩略名', 'trim|alpha_dash|htmlspecialchars');
        }else{
            $this->form_validation->set_rules('slug', '缩略名', 'trim|htmlspecialchars');
        }
        
        $this->form_validation->set_rules('description','描述','trim|htmlspecialchars');
        
    }
    
    public function operate($type,$mid,$do){
        switch ($do){
            case 'delete':
                $this->_remove($type,$mid);
                break;
            case 'refresh':
                echo "shuashuadddddadsfasdfe";
                break;
            default:
                exit;
                break;                                      
        }
    }
    
    private function _remove($type,$mid){
        $this->metas_mdl->remove_meta($mid);
        $res = $this->metas_mdl->remove_relationship('mid',$mid);
        
        $msg = $res ?$this->_map[$type].'删除成功' : $this->_map[$type].'没有删除';
        $notify = $res ? 'success' : 'error';
        
        redirect('admin/metas/manage');
    }
    
}


/*
 End of file
 Location:mstas.php
 */