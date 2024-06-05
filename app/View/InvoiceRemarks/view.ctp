<div class="invoiceRemarks view">
<?php 
	echo "<h2>".__('Invoice Remark')."</h2>";
	echo "<dl>";
		echo "<dt>".__('User')."</dt>";
		echo "<dd>".$this->Html->link($invoiceRemark['User']['username'], array('controller' => 'users', 'action' => 'view', $invoiceRemark['User']['id']))."</dd>";
		echo "<dt>".__('Invoice')."</dt>";
		echo "<dd>".$this->Html->link($invoiceRemark['Invoice']['invoice_code'], array('controller' => 'invoices', 'action' => 'view', $invoiceRemark['Invoice']['id']))."</dd>";
		echo "<dt>".__('Remark Datetime')."</dt>";
		echo "<dd>".h($invoiceRemark['InvoiceRemark']['remark_datetime'])."</dd>";
		echo "<dt>".__('Remark Text')."</dt>";
		echo "<dd>".h($invoiceRemark['InvoiceRemark']['remark_text'])."</dd>";
		echo "<dt>".__('Action Type')."</dt>";
		echo "<dd>".$this->Html->link($invoiceRemark['ActionType']['name'], array('controller' => 'action_types', 'action' => 'view', $invoiceRemark['ActionType']['id']))."</dd>";
		echo "<dt>".__('Working Days Before Reminder')."</dt>";
		echo "<dd>".h($invoiceRemark['InvoiceRemark']['working_days_before_reminder'])."</dd>";
		echo "<dt>".__('Reminder Date')."</dt>";
		echo "<dd>".h($invoiceRemark['InvoiceRemark']['reminder_date'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Invoice Remark'), array('action' => 'edit', $invoiceRemark['InvoiceRemark']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Invoice Remark'), array('action' => 'delete', $invoiceRemark['InvoiceRemark']['id']), array(), __('Are you sure you want to delete # %s?', $invoiceRemark['InvoiceRemark']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Invoice Remarks'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Invoice Remark'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Invoices'), array('controller' => 'invoices', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Invoice'), array('controller' => 'invoices', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Action Types'), array('controller' => 'action_types', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Action Type'), array('controller' => 'action_types', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
