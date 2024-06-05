<div class="inventoryDepartments form">
<?php echo $this->Form->create('InventoryDepartment'); ?>
	<fieldset>
		<legend><?php echo __('Add Inventory Department'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Inventory Departments'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Inventory Product Lines'), array('controller' => 'inventory_product_lines', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Inventory Product Line'), array('controller' => 'inventory_product_lines', 'action' => 'add')); ?> </li>
	</ul>
</div>
