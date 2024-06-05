<div class="productionOrderProducts form">
<?php echo $this->Form->create('ProductionOrderProduct'); ?>
	<fieldset>
		<legend><?php echo __('Add Production Order Product'); ?></legend>
	<?php
		echo $this->Form->input('production_order_id');
		echo $this->Form->input('product_id');
		echo $this->Form->input('product_quantity');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Production Order Products'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Production Orders'), array('controller' => 'production_orders', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Production Order'), array('controller' => 'production_orders', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Production Order Product Operation Locations'), array('controller' => 'production_order_product_operation_locations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Production Order Product Operation Location'), array('controller' => 'production_order_product_operation_locations', 'action' => 'add')); ?> </li>
	</ul>
</div>
