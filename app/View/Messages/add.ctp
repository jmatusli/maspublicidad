<div class="messages form">
<?php echo $this->Form->create('Message'); ?>
	<fieldset>
		<legend><?php echo __('Add Message'); ?></legend>
	<?php
		echo $this->Form->input('from_user_id');
		echo $this->Form->input('subject');
		echo $this->Form->input('body_text');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Messages'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New From User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Message Recipients'), array('controller' => 'message_recipients', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Message Recipient'), array('controller' => 'message_recipients', 'action' => 'add')); ?> </li>
	</ul>
</div>
