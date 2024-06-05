<div class="productionProcessProductOperationLocations form">
<?php echo $this->Form->create('ProductionProcessProductOperationLocation'); ?>
	<fieldset>
		<legend><?php echo __('Edit Production Process Product Operation Location'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('production_process_product_id');
		echo $this->Form->input('operation_location_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('ProductionProcessProductOperationLocation.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('ProductionProcessProductOperationLocation.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Production Process Product Operation Locations'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Production Process Products'), array('controller' => 'production_process_products', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Production Process Product'), array('controller' => 'production_process_products', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Operation Locations'), array('controller' => 'operation_locations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Operation Location'), array('controller' => 'operation_locations', 'action' => 'add')); ?> </li>
	</ul>
</div>
