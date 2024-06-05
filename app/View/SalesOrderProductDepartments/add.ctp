<div class="salesOrderProductDepartments form">
<?php echo $this->Form->create('SalesOrderProductDepartment'); ?>
	<fieldset>
		<legend><?php echo __('Add Sales Order Product Department'); ?></legend>
	<?php
		echo $this->Form->input('sales_order_product_id');
		echo $this->Form->input('department_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Sales Order Product Departments'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Sales Order Products'), array('controller' => 'sales_order_products', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Sales Order Product'), array('controller' => 'sales_order_products', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Departments'), array('controller' => 'departments', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Department'), array('controller' => 'departments', 'action' => 'add')); ?> </li>
	</ul>
</div>
