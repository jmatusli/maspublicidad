<div class="invoiceRemarks form">
<?php echo $this->Form->create('InvoiceRemark'); ?>
	<fieldset>
		<legend><?php echo __('Edit Invoice Remark'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_id');
		echo $this->Form->input('invoice_id');
		echo $this->Form->input('remark_datetime');
		echo $this->Form->input('remark_text');
		echo $this->Form->input('action_type_id');
		echo $this->Form->input('working_days_before_reminder');
		echo $this->Form->input('reminder_date');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('InvoiceRemark.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('InvoiceRemark.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Invoice Remarks'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Invoices'), array('controller' => 'invoices', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Invoice'), array('controller' => 'invoices', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Action Types'), array('controller' => 'action_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Action Type'), array('controller' => 'action_types', 'action' => 'add')); ?> </li>
	</ul>
</div>
