<div class="tasks form">
<?php echo $this->Form->create('Task'); ?>
	<fieldset>
		<legend><?php echo __('Add Task'); ?></legend>
	<?php
		echo $this->Form->input('requesting_user_id',['default'=>$loggedUserId]);
		echo $this->Form->input('task_type_id');
		echo $this->Form->input('requirements');
		//echo $this->Form->input('acting_user_id');
		//echo $this->Form->input('implementation');
		//echo $this->Form->input('date_executed');
		//echo $this->Form->input('receiving_user_id');
		//echo $this->Form->input('hours_estimated');
		//echo $this->Form->input('remarks');
		//echo $this->Form->input('priority');
		//echo $this->Form->input('bool_active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";

		echo "<li>".$this->Html->link(__('List Tasks'), array('action' => 'index'))."</li>";
    echo "<br>";
		echo "<li>".$this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Task Types'), array('controller' => 'task_types', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Task Type'), array('controller' => 'task_types', 'action' => 'add'))." </li>";
	echo "</ul>";
?>
</div>
