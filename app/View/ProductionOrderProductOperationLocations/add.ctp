<div class="productionOrderProductOperationLocations form">
<?php echo $this->Form->create('ProductionOrderProductOperationLocation'); ?>
	<fieldset>
		<legend><?php echo __('Add Production Order Product Operation Location'); ?></legend>
	<?php
		echo $this->Form->input('production_order_product_id');
		echo $this->Form->input('operation_location_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Production Order Product Operation Locations'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Production Order Products'), array('controller' => 'production_order_products', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Production Order Product'), array('controller' => 'production_order_products', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Operation Locations'), array('controller' => 'operation_locations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Operation Location'), array('controller' => 'operation_locations', 'action' => 'add')); ?> </li>
	</ul>
</div>
