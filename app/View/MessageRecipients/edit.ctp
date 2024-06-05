<div class="messageRecipients form">
<?php echo $this->Form->create('MessageRecipient'); ?>
	<fieldset>
		<legend><?php echo __('Edit Message Recipient'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('message_id');
		echo $this->Form->input('recipient_user_id');
		echo $this->Form->input('bool_read');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('MessageRecipient.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('MessageRecipient.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Message Recipients'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Messages'), array('controller' => 'messages', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Message'), array('controller' => 'messages', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Recipient User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>
