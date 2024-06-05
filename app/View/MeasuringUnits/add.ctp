<div class="measuringUnits form">
<?php echo $this->Form->create('MeasuringUnit'); ?>
	<fieldset>
		<legend><?php echo __('Add Measuring Unit'); ?></legend>
	<?php
		echo $this->Form->input('abbreviation');
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Measuring Units'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Stock Item Logs'), array('controller' => 'stock_item_logs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Stock Item Log'), array('controller' => 'stock_item_logs', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Stock Items'), array('controller' => 'stock_items', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Stock Item'), array('controller' => 'stock_items', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Stock Movements'), array('controller' => 'stock_movements', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Stock Movement'), array('controller' => 'stock_movements', 'action' => 'add')); ?> </li>
	</ul>
</div>
