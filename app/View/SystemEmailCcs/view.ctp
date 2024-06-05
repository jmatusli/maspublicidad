<div class="systemEmailCcs view">
<?php 
	echo "<h2>".__('System Email Cc')."</h2>";
	echo "<dl>";
		echo "<dt>".__('System Email')."</dt>";
		echo "<dd>".$this->Html->link($systemEmailCc['SystemEmail']['id'], array('controller' => 'system_emails', 'action' => 'view', $systemEmailCc['SystemEmail']['id']))."</dd>";
		echo "<dt>".__('Email Cc')."</dt>";
		echo "<dd>".h($systemEmailCc['SystemEmailCc']['email_cc'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit System Email Cc'), array('action' => 'edit', $systemEmailCc['SystemEmailCc']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete System Email Cc'), array('action' => 'delete', $systemEmailCc['SystemEmailCc']['id']), array(), __('Are you sure you want to delete # %s?', $systemEmailCc['SystemEmailCc']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List System Email Ccs'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New System Email Cc'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List System Emails'), array('controller' => 'system_emails', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New System Email'), array('controller' => 'system_emails', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
