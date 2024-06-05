<div class="operationLocations form">
<?php echo $this->Form->create('OperationLocation'); ?>
	<fieldset>
		<legend><?php echo __('Add Operation Location'); ?></legend>
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
		<li><?php echo $this->Html->link(__('List Operation Locations'), array('action' => 'index')); ?></li>
	</ul>
</div>
