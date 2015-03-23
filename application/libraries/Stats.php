<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
2015年2月14日PHP
*/

//常用统计类

class Stats{
	
	//CI句柄
	private $_CI;
	
	public function __construct(){
		
		$this->_CI = & get_instance();
	
	}
	
	//分类数目
	public function count_categories(){
		return $this->_CI->metas_mdl->count_metas();
	}
	
	
	//计算文章个数
	public function count_posts($type='post',$status='publish',$uid=NULL){
		return $this->_CI->posts_mdl->get_posts($type,$status,$uid,10000,0)->num_rows();
	}
	
}

/*
End of file
Location:Stats.php
*/