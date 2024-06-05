<div class="systemEmails form">
<?php echo $this->Form->create('SystemEmail'); ?>
	<fieldset>
		<legend><?php echo __('Edit System Email'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('from');
		echo $this->Form->input('subject');
		echo $this->Form->input('body');
		echo $this->Form->input('boundary');
		echo $this->Form->input('attachment');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('SystemEmail.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('SystemEmail.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List System Emails'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List System Email Attachments'), array('controller' => 'system_email_attachments', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New System Email Attachment'), array('controller' => 'system_email_attachments', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List System Email Bccs'), array('controller' => 'system_email_bccs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New System Email Bcc'), array('controller' => 'system_email_bccs', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List System Email Ccs'), array('controller' => 'system_email_ccs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New System Email Cc'), array('controller' => 'system_email_ccs', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List System Email Recipients'), array('controller' => 'system_email_recipients', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New System Email Recipient'), array('controller' => 'system_email_recipients', 'action' => 'add')); ?> </li>
	</ul>
</div>
