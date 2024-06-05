<div class="systemEmailCcs form">
<?php echo $this->Form->create('SystemEmailCc'); ?>
	<fieldset>
		<legend><?php echo __('Edit System Email Cc'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('system_email_id');
		echo $this->Form->input('email_cc');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('SystemEmailCc.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('SystemEmailCc.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List System Email Ccs'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List System Emails'), array('controller' => 'system_emails', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New System Email'), array('controller' => 'system_emails', 'action' => 'add')); ?> </li>
	</ul>
</div>
