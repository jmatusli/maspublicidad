<div class="productionOrderProductDepartments form">
<?php echo $this->Form->create('ProductionOrderProductDepartment'); ?>
	<fieldset>
		<legend><?php echo __('Add Production Order Product Department'); ?></legend>
	<?php
		echo $this->Form->input('production_order_product_id');
		echo $this->Form->input('department_id');
		echo $this->Form->input('rank');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Production Order Product Departments'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Production Order Products'), array('controller' => 'production_order_products', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Production Order Product'), array('controller' => 'production_order_products', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Departments'), array('controller' => 'departments', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Department'), array('controller' => 'departments', 'action' => 'add')); ?> </li>
	</ul>
</div>
