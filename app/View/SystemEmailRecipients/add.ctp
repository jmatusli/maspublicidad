<div class="systemEmailRecipients form">
<?php echo $this->Form->create('SystemEmailRecipient'); ?>
	<fieldset>
		<legend><?php echo __('Add System Email Recipient'); ?></legend>
	<?php
		echo $this->Form->input('system_email_id');
		echo $this->Form->input('email_recipient');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List System Email Recipients'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List System Emails'), array('controller' => 'system_emails', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New System Email'), array('controller' => 'system_emails', 'action' => 'add')); ?> </li>
	</ul>
</div>
