<div class="productionProcessProducts form">
<?php echo $this->Form->create('ProductionProcessProduct'); ?>
	<fieldset>
		<legend><?php echo __('Edit Production Process Product'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('production_process_id');
		echo $this->Form->input('product_id');
		echo $this->Form->input('product_description');
		echo $this->Form->input('product_quantity');
		echo $this->Form->input('operator_id');
		echo $this->Form->input('machine_id');
		echo $this->Form->input('sales_order_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('ProductionProcessProduct.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('ProductionProcessProduct.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Production Process Products'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Production Processes'), array('controller' => 'production_processes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Production Process'), array('controller' => 'production_processes', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Operator'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Machines'), array('controller' => 'machines', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Machine'), array('controller' => 'machines', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Sales Orders'), array('controller' => 'sales_orders', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Sales Order'), array('controller' => 'sales_orders', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Production Process Product Operation Locations'), array('controller' => 'production_process_product_operation_locations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Production Process Product Operation Location'), array('controller' => 'production_process_product_operation_locations', 'action' => 'add')); ?> </li>
	</ul>
</div>
