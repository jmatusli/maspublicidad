<div class="providers form">
<?php echo $this->Form->create('InventoryProvider'); ?>
	<fieldset>
		<legend><?php echo __('Edit Inventory Provider'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('address');
		echo $this->Form->input('phone');
		echo $this->Form->input('email');
		echo $this->Form->input('bool_active');
	?>
	</fieldset>
	<div id='InventoryProductList' style='width:45%;float:left;clear:none;' class='col-md-6'>
		<h3>Productos Relacionados</h3>
	<?php
		//pr($inventoryProducts);
		for ($p=0;$p<count($inventoryProducts);$p++){
			//pr($inventoryProducts[$p]);
			$inventoryProductChecked=false;
			if (!empty($inventoryProducts[$p]['InventoryProductInventoryProvider'])){
				$inventoryProductChecked=true;
			}
			echo $this->Form->input('InventoryProduct.'.$p.'.inventory_product_id',array('type'=>'checkbox','checked'=>$inventoryProductChecked,'label'=>$inventoryProducts[$p]['InventoryProduct']['name'].(!empty($inventoryProducts[$p]['InventoryProduct']['code'])?" (".$inventoryProducts[$p]['InventoryProduct']['code'].") ":" ").$inventoryProducts[$p]['InventoryProductLine']['name'],'div'=>array('class'=>'checkboxleft')));
		}
	?>
	</div>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<!--li><?php //echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('InventoryProvider.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('InventoryProvider.id'))); ?></li-->
		<li><?php echo $this->Html->link(__('List Inventory Providers'), array('action' => 'index')); ?></li>
		<br/>
	<?php	
		if ($bool_inventoryproduct_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Products'), array('controller' => 'inventory_products', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryproduct_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Product'), array('controller' => 'inventory_products', 'action' => 'add'))."</li>";
		}	
	?>
	</ul>
</div>
