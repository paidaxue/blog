<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
2015年2月25日
*/
class Metas_mdl extends CI_Model{
    
    const TBL_METAS = 'metas';
    const TBL_RELATIONSHIPS = 'relationships';
    const TBL_POSTS = 'posts';
    
    private $_type = array('category','tag');
    
    public $metas = NULL;
    
    public function __construct(){
        parent::__construct();               
    }
    
    //获取所有metas
    public function list_metas($type = 'category'){
        
        if(in_array($type,$this->_type)){
            $this->db->where(self::TBL_METAS.'.type',$type);
        }
        
        return $this->db->get(self::TBL_METAS);
    }
    
    public function get_meta($type = 'category',$name = ''){
        
        if(empty($name)){
            exit();
        }
        
        if($type && in_array($type,$this->_type)){
            $this->db->where(self::TBL_METAS.'.type',$type);
            $this->db->where(self::TBL_METAS.'.name',$name);
            $this->db->where(self::TBL_METAS.'.mid',intval($name));
        }
        
        if($type == 'BYID'){
            $this->db->where(self::TBL_METAS.'.mid',intval($name));
        }
                
        return $this->db->get(self::TBL_METAS)->row();                                                
    }
    
    //添加meta
    public function add_meta($meta_data){
        $this->db->insert(self::TBL_METAS,$meta_data);
        return ($this->db->affected_rows() == 1) ? $this->db->insert_id() : FALSE;   
    }
    
    public function  update_meta($mid,$data){
        $this->db->where('mid',intval($mid));
        $this->db->update(self::TBL_METAS,$data);
        
        return ($this->db->affected_rows() == 1) ? TRUE : FALSE;
    }
    
    public function remove_meta($mid){
        $this->db->delete(self::TBL_METAS,array('mid' =>intval($mid)));
        
        return ($this->db->affected_rows() == 1) ? TRUE : FALSE;
    }
    
    public function remove_relationship($column = 'pid',$value){
        $this->db->delete(self::TBL_METAS,array($column => intval($value)));
        
        return ($this->db->affected_rows() == 1) ? TRUE : FALSE;
    }
    
    //根据post id获取分类数据
    public function get_metas($pid=0,$return=FALSE){
        
        $this->metas = NULL;
        
        $metas = array();
        
        if(!empty($pid)){

            //查询metas表和relationships的pid列
            //select * from metas and relationships.pid            
            $this->db->select(self::TBL_METAS.'.*,'.self::TBL_RELATIONSHIPS.'.pid');
            //如果你需要指定 JOIN 的类型，你可以通过本函数的第三个参数来指定。可选项包括：left, right, outer, inner, left outer, 以及 right outer.		这里是inner
            //INNER JOIN (relationships ON relationships.mid = metas.mid AND relationships.pid = $pid );
            $this->db->join(self::TBL_RELATIONSHIPS,self::TBL_RELATIONSHIPS.'.mid = '.self::TBL_METAS.'.mid AND '.self::TBL_RELATIONSHIPS.'.pid='.intval($pid), 'INNER');            
        }
        
        $query = $this->db->get(self::TBL_METAS);
        //$query = $this->db->get(self::TBL_METAS);
        
        if($query->num_rows() > 0){
            $metas = $query->result_array();
        }
        
        $query->free_result();
        
        if($return){
            return $metas;
        }
        
        foreach ($this->_type as $type){
            $this->metas[$type] = array();
        }
        
        if(!empty($metas))
        {
            //根据不同的metas类型自动push进对应的数组
            foreach($metas as $meta)
            {
                foreach($this->_type as $type)
                {
                    if($type == $meta['type'])
                    {
                        array_push($this->metas[$type], $meta);
                    }
                }
            }
        } 
    }
    
    /**
     * 删除关系
     *
     * @access public
     * @param  int   $pid  内容ID
     * @param  int 	 $mid  meta ID
     * @return boolean 成功与否
     */
    public function remove_relationship_strict($pid,$mid)
    {
        $this->db->delete(self::TBL_RELATIONSHIPS,
                          array(
                              'pid'=>intval($pid),
                              'mid'=>intval($mid)
                          ));
        return ($this->db->affected_rows() == 1) ? TRUE : FALSE;
    }
    
    
    /**
     * meta个数自减一
     *
     * @access public
     * @param int $mid meta id
     * @return void
     */    
    public function meta_num_minus($mid)
    {
        $this->db->query('UPDATE '.self::TBL_METAS.' SET `count` = `count`-1 WHERE `mid`='.$mid.'');
    }
    
    
    /**
     * 添加元数据/内容关系
     *
     * @access public
     * @param  array $relation_data  内容
     * @return boolean 成功与否
     */
    public function add_relationship($relation_data)
    {
        $this->db->insert(self::TBL_RELATIONSHIPS, $relation_data);
    
        return ($this->db->affected_rows()==1) ? $this->db->insert_id() : FALSE;
    }
    
    
    /**
     * meta个数自增一
     *
     * @access public
     * @param int $mid meta id
     * @return void
     */
    public function meta_num_plus($mid)
    {
        $this->db->query('UPDATE '.self::TBL_METAS.' SET `count` = `count`+1 WHERE `mid`='.$mid.'');
    }
    
    /**
     * 根据tag获取ID
     *
     * @access public
     * @param  mixed $inputTags 标签名
     * @return mixed
     */
    public function scan_tags($inputTags)
    {
        $tags = is_array($inputTags) ? $inputTags : array($inputTags);
        $result = array();
    
        foreach ($tags as $tag)
        {
            if (empty($tag))
            {
                continue;
            }
    
            $row = $this->db->select('*')
            ->from(self::TBL_METAS)
            ->where('type','tag')
            ->where('name',$tag)
            ->limit(1)
            ->get()
            ->row();
    
            if ($row)
            {
                $result[] = $row->mid;
            }
            else
            {
                $slug = Common::repair_slugName($tag);
    
                if ($slug)
                {
                    $result[] = $this->add_meta(array(
                        'name'  =>  $tag,
                        'slug'  =>  $slug,
                        'type'  =>  'tag',
                        'count' =>  0,
                        'order' =>  0,
                    ));
                }
            }
        }
    
        return is_array($inputTags) ? $result : current($result);
    }
}



/*
 End of file
 Location:metas_mdl.php
 */