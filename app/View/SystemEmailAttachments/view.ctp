<div class="systemEmailAttachments view">
<?php 
	echo "<h2>".__('System Email Attachment')."</h2>";
	echo "<dl>";
		echo "<dt>".__('System Email')."</dt>";
		echo "<dd>".$this->Html->link($systemEmailAttachment['SystemEmail']['id'], array('controller' => 'system_emails', 'action' => 'view', $systemEmailAttachment['SystemEmail']['id']))."</dd>";
		echo "<dt>".__('Url Attachment')."</dt>";
		echo "<dd>".h($systemEmailAttachment['SystemEmailAttachment']['url_attachment'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit System Email Attachment'), array('action' => 'edit', $systemEmailAttachment['SystemEmailAttachment']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete System Email Attachment'), array('action' => 'delete', $systemEmailAttachment['SystemEmailAttachment']['id']), array(), __('Are you sure you want to delete # %s?', $systemEmailAttachment['SystemEmailAttachment']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List System Email Attachments'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New System Email Attachment'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List System Emails'), array('controller' => 'system_emails', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New System Email'), array('controller' => 'system_emails', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
