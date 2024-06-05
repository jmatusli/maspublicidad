<div class="providers form">
<?php echo $this->Form->create('Provider'); ?>
	<fieldset>
		<legend><?php echo __('Edit Provider'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('address');
		echo $this->Form->input('phone');
		echo $this->Form->input('email');
		echo $this->Form->input('bool_active');
		
	?>
	</fieldset>
	<div id='ProductList' style='width:45%;float:left;clear:none;' class='col-md-6'>
		<h3>Productos Relacionados</h3>
	<?php
		//pr($products);
		for ($p=0;$p<count($products);$p++){
			$productChecked=false;
			if (!empty($products[$p]['ProductProvider'])){
				$productChecked=true;
			}
			echo $this->Form->input('Product.'.$p.'.product_id',array('type'=>'checkbox','checked'=>$productChecked,'label'=>$products[$p]['Product']['name'],'div'=>array('class'=>'checkboxleft')));
		}
	?>
	</div>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<!--li><?php //echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Provider.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('Provider.id'))); ?></li-->
		<li><?php echo $this->Html->link(__('List Providers'), array('action' => 'index')); ?></li>
		<br/>
	<?php	
		if ($bool_product_index_permission){
			echo "<li>".$this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index'))."</li>";
		}
		if ($bool_product_add_permission){
			echo "<li>".$this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add'))."</li>";
		}	
	?>
	</ul>
</div>
