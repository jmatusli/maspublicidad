<div class="productionProcessRemarks form">
<?php echo $this->Form->create('ProductionProcessRemark'); ?>
	<fieldset>
		<legend><?php echo __('Edit Production Process Remark'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_id');
		echo $this->Form->input('production_process_id');
		echo $this->Form->input('remark_datetime');
		echo $this->Form->input('remark_text');
		echo $this->Form->input('action_type_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('ProductionProcessRemark.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('ProductionProcessRemark.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Production Process Remarks'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Production Processes'), array('controller' => 'production_processes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Production Process'), array('controller' => 'production_processes', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Action Types'), array('controller' => 'action_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Action Type'), array('controller' => 'action_types', 'action' => 'add')); ?> </li>
	</ul>
</div>
