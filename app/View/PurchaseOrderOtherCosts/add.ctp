<div class="purchaseOrderOtherCosts form">
<?php echo $this->Form->create('PurchaseOrderOtherCost'); ?>
	<fieldset>
		<legend><?php echo __('Add Purchase Order Other Cost'); ?></legend>
	<?php
		echo $this->Form->input('purchase_order_id');
		echo $this->Form->input('task_description');
		echo $this->Form->input('department_id');
		echo $this->Form->input('task_quantity');
		echo $this->Form->input('task_unit_cost');
		echo $this->Form->input('task_total_cost');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Purchase Order Other Costs'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Purchase Orders'), array('controller' => 'purchase_orders', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Purchase Order'), array('controller' => 'purchase_orders', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Departments'), array('controller' => 'departments', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Department'), array('controller' => 'departments', 'action' => 'add')); ?> </li>
	</ul>
</div>
