<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>


<ul>
<li><?php echo anchor(site_url('admin/users/manage/'),'用户管理',array('title'=>'用户管理'));?></li>
<li><?php echo anchor(site_url('admin/profile/'),$this->user->name);?></li>
<li><?php echo anchor(site_url('admin/posts/manage'),'文章管理',array('title'=>'文章管理'));?></li>
<li><?php echo anchor(site_url('admin/posts/write'),'写新文章',array('title'=>'写新文章'));?></li>
<li><?php echo anchor(site_url('admin/metas/manage/'),'标签和分类',array('title'=>'标签和分类'));?></li>
<li><?php echo anchor(site_url('admin/login/logout'),'登出',array('title'=>'安全登出后台'));?></li>
<li></li>
<li></li>
<li></li>
</ul>