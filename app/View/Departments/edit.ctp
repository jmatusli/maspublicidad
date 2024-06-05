<div class="departments form">
<?php echo $this->Form->create('Department'); ?>
	<fieldset>
		<legend><?php echo __('Edit Department'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('abbreviation');
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Department.id')), array(), __('Está seguro que quiere eliminar el departamento %s?', $this->Form->value('Department.id')))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Departments'), array('action' => 'index'))."</li>";
		echo "<br/>";
		if ($bool_inventoryproductline_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Product Lines'), array('controller' => 'inventory_product_lines', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryproductline_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Product Line'), array('controller' => 'inventory_product_lines', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
