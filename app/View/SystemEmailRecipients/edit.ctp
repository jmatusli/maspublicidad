<div class="systemEmailRecipients form">
<?php echo $this->Form->create('SystemEmailRecipient'); ?>
	<fieldset>
		<legend><?php echo __('Edit System Email Recipient'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('system_email_id');
		echo $this->Form->input('email_recipient');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('SystemEmailRecipient.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('SystemEmailRecipient.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List System Email Recipients'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List System Emails'), array('controller' => 'system_emails', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New System Email'), array('controller' => 'system_emails', 'action' => 'add')); ?> </li>
	</ul>
</div>
