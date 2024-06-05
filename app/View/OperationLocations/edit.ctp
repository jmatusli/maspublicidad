<div class="operationLocations form">
<?php echo $this->Form->create('OperationLocation'); ?>
	<fieldset>
		<legend><?php echo __('Edit Operation Location'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
<?php	
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Eliminar'), array('action' => 'delete', $this->Form->value('OperationLocation.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('OperationLocation.name')))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Operation Locations'), array('action' => 'index'))."</li>";
	echo "</ul>";
?>
</div>
