<div class="inventoryProductLines form">
<?php echo $this->Form->create('InventoryProductLine'); ?>
	<fieldset>
		<legend><?php echo __('Edit Inventory Product Line'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('department_id');
		echo $this->Form->input('bool_promotion',array('label'=>'Producto Promocional'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete Inventory Product Line'), array('action' => 'delete', $this->Form->value('InventoryProductLine.id')), array(), __('Está seguro que quiere eliminar la línea de producto %s?', $this->Form->value('InventoryProductLine.name')))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Inventory Product Lines'), array('action' => 'index'))."</li>";
		if ($bool_department_index_permission){
			echo "<li>".$this->Html->link(__('List Departments'), array('controller' => 'departments', 'action' => 'index'))."</li>";
		}
		if ($bool_department_add_permission){
			echo "<li>".$this->Html->link(__('New Department'), array('controller' => 'departments', 'action' => 'add'))."</li>";
		}
		if ($bool_inventoryproduct_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Products'), array('controller' => 'inventory_products', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryproduct_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Product'), array('controller' => 'inventory_products', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
