<div class="systemEmailBccs view">
<?php 
	echo "<h2>".__('System Email Bcc')."</h2>";
	echo "<dl>";
		echo "<dt>".__('System Email')."</dt>";
		echo "<dd>".$this->Html->link($systemEmailBcc['SystemEmail']['id'], array('controller' => 'system_emails', 'action' => 'view', $systemEmailBcc['SystemEmail']['id']))."</dd>";
		echo "<dt>".__('Email Bcc')."</dt>";
		echo "<dd>".h($systemEmailBcc['SystemEmailBcc']['email_bcc'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit System Email Bcc'), array('action' => 'edit', $systemEmailBcc['SystemEmailBcc']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete System Email Bcc'), array('action' => 'delete', $systemEmailBcc['SystemEmailBcc']['id']), array(), __('Are you sure you want to delete # %s?', $systemEmailBcc['SystemEmailBcc']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List System Email Bccs'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New System Email Bcc'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List System Emails'), array('controller' => 'system_emails', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New System Email'), array('controller' => 'system_emails', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
