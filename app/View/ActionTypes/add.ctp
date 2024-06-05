<div class="ActionTypes form">
<?php echo $this->Form->create('ActionType'); ?>
	<fieldset>
		<legend><?php echo __('Add Action Type'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('list_order');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class='actions'>
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('List Action Types'), array('action' => 'index'))."</li>";
	echo "</ul>";
?>
</div>
