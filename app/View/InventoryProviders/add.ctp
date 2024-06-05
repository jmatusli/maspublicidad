<div class="providers form">
<?php echo $this->Form->create('InventoryProvider'); ?>
	<fieldset>
		<legend><?php echo __('Add Inventory Provider'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('address');
		echo $this->Form->input('phone');
		echo $this->Form->input('email');
		echo $this->Form->input('bool_active',array('default'=>true,'div'=>array('class'=>'hidden')));
	?>
	</fieldset>
	<div id='InventoryProductList' style='width:45%;float:left;clear:none;' class='col-md-6'>
		<h3>Productos Relacionados</h3>
	<?php
		for ($p=0;$p<count($inventoryProducts);$p++){
			echo $this->Form->input('InventoryProduct.'.$p.'.inventory_product_id',array('type'=>'checkbox','checked'=>false,'label'=>$inventoryProducts[$p]['InventoryProduct']['name'].(!empty($inventoryProducts[$p]['InventoryProduct']['code'])?" (".$inventoryProducts[$p]['InventoryProduct']['code'].") ":" ").$inventoryProducts[$p]['InventoryProductLine']['name'],'div'=>array('class'=>'checkboxleft')));
		}
	?>
	</div>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
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
