<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$this->load->view('admin/header');
$this->load->view('admin/menu');
?>
<div>
<div>

  <div>
    <button type="button"><?php echo anchor(site_url('admin/metas/manage'),'分类');?></button>
    <button type="button"><?php echo anchor(site_url('admin/metas/manage/tag'),'标签');?></button>
  </div>

</div>
</div>
<br>


<div>
<div>

<div>
<?php if('category' == $type):?>
<form role="form" method="post" action="">
<div >
<table>
	<thead>
		<tr>
			<th>名称</th>
			<th></th>
			<th>url缩略名</th>
			<th>文章数</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if($category->num_rows() > 0): ?>	
			<?php foreach($category->result() as $cate): ?>
				<tr>
					<td>
						<?php echo anchor('admin/metas/manage/category/'.$cate->mid,$cate->name);?>
					</td>
					<td>
					</td>
					<td>
						<?php echo $cate->slug; ?>
					</td>
					<td>
					</td>	
					<td>
						<?php echo anchor('admin/metas/operate/'.$type.'/'.$cate->mid.'/delete','删除');?>			
					</td>
				</tr>
			<?php endforeach; ?>
		<?php else: ?>
			<tr>
				<td>没有任何分类</td>
			</tr>
		<?php endif;?>
	</tbody>
</table>
</div>
</form>


<?php else:?>
<form role="form" method="post" action="">
<div>
<table>

	<thead>
		<tr>
			<th>标签名称</th>
			<th></th>
			<th>url缩略名</th>
			<th>文章数</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if($tag->num_rows() > 0): ?>
			
			<?php foreach($tag->result() as $t): ?>
			
			<tr>
				<th><?php echo anchor('admin/metas/manage/tag/'.$t->mid,$t->name);?></th>
				<th></th>
				<th><?php echo $t->slug; ?></th>
				<th>文章数</th>
				<th><?php echo anchor('admin/metas/operate/'.$type.'/'.$t->mid.'/delete','删除');?>		</th>
			</tr>		
			<?php endforeach; ?>	
		<?php else: ?>
			<tr>
				<td>没有任何标签</td>
			</tr>	
		<?php endif;?>
	</tbody>
</table>
</div>
</form>
<?php endif; ?>
</div>


<div>
<table>
	  <?php if('category' == $type):?>
      	<?php $this->load->view('admin/metas_cate_form');?>
      <?php endif;?>
      <?php if('tag' == $type):?>
		<?php $this->load->view('admin/metas_tag_form');?>
      <?php endif;?>
	</table>
</table>
</div>




</div>
</div>





<?php echo $this->load->view('admin/footer');?>