<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
2015年3月1日
*/
class Posts_mdl extends CI_Model{
    
    const TBL_POSTS = 'posts';
    const TBL_METAS = 'metas';
    const TBL_RELATIONSHIPS = 'relationship';
    const TBL_COMMENTS = 'comments';
    
    //内容类型 日志/附件/独立页面
    private $_post_type = array('post','attachment','page');
    
    //内容状态:	发布/草稿/未归档/等待审核
    private $_post_status = array('publish','draft','unattached','waiting');
    
    //内容的唯一栏:pid/slug
    private $_post_unique_field = array('pid','slug');
    
    public function __construct(){        
        parent::__construct();                
    }
    
    public function add_post($content_data){
        $this->db->insert(self::TBL_POSTS,$content_data);
        return ($this->db->affected_rows() == 1 ? $this->db->insert_id() : FALSE);
    }
    
    public function get_posts($type='post',$status='publish',$author_id=NULL,$limit=NULL,$offset=NULL,$category_filter=0,$title_filter='',$feed_filter=FALSE)
    {
        
        $this->db->select('posts.*,users.screenName');
        $this->db->join('users','users.uid=posts.authorId');
        
        if($type && in_array($type,$this->_post_type)){
            $this->db->where('posts.type',$type);
        }
        
        if($status && in_array($status,$this->_post_status)){
            $this->db->where('posts.status',$status);
        }
   
        
        if(!empty($author_id)){
            $this->db->where('posts.authorId',intval($author_id));
        }
        
        if(!empty($title_filter)){
            $this->db->like('posts.title',$title_filter);
        }
        
        if($feed_filter){
            $this->db->where('allowFeed',1);
        }
        
        //对查询结果排序
        $this->db->order_by('posts.created','DESC');
        
        if($limit && is_numeric($limit)){
            $this->db->limit(intval($limit));
        }
        
        if($offset && is_numeric($offset)){
            $this->db->offset(intval($offset));
        }
        
        return $this->db->get(self::TBL_POSTS);        
    }
    
    //更新文章
    public function update_post($pid,$data){
        $this->db->where('pid',intval($pid));
        $this->db->update(self::TBL_POSTS,$data);
        
        return ($this->db->affected_rows() == 1) ? TRUE : FALSE;
    }
    
    //处理slug重复问题
    public function get_slug_name($slug, $pid){
        $result = $slug;
        $count =1;
        
        while($this->db->select('pid')->where('slug',$result)->where('pid <>',$pid)->get(self::TBL_POSTS)->num_rows()>0){
            
            $result = $slug.'_'.$count;
            $count ++;
        }
        
        return $result;
    }
    
    
    /**
     * 根据唯一键获取单个内容信息
     *
     * @access public
     * @param  string $identity 内容标识栏位：{"pid"｜"slug"}
     * @param  mixed  $value    标识栏位对应的值
     * @return array  内容信息
     */
    public function get_post_by_id($identity,$value)
    {
        if(!in_array($identity,$this->_post_unique_field))
        {
            return FALSE;
        }
        
        $this->db->select('posts.*,users.screenName');
        $this->db->join('users','users.uid = posts.authorId');
        $this->db->where($identity,$value);
        
        return $this->db->get(self::TBL_POSTS)->row();
    }
}



/*
 End of file
 Location:posts.php
 */