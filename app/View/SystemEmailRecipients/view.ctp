<div class="systemEmailRecipients view">
<?php 
	echo "<h2>".__('System Email Recipient')."</h2>";
	echo "<dl>";
		echo "<dt>".__('System Email')."</dt>";
		echo "<dd>".$this->Html->link($systemEmailRecipient['SystemEmail']['id'], array('controller' => 'system_emails', 'action' => 'view', $systemEmailRecipient['SystemEmail']['id']))."</dd>";
		echo "<dt>".__('Email Recipient')."</dt>";
		echo "<dd>".h($systemEmailRecipient['SystemEmailRecipient']['email_recipient'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit System Email Recipient'), array('action' => 'edit', $systemEmailRecipient['SystemEmailRecipient']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete System Email Recipient'), array('action' => 'delete', $systemEmailRecipient['SystemEmailRecipient']['id']), array(), __('Are you sure you want to delete # %s?', $systemEmailRecipient['SystemEmailRecipient']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List System Email Recipients'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New System Email Recipient'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List System Emails'), array('controller' => 'system_emails', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New System Email'), array('controller' => 'system_emails', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
