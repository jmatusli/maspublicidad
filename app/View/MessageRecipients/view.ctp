<div class="messageRecipients view">
<?php 
	echo "<h2>".__('Message Recipient')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Message')."</dt>";
		echo "<dd>".$this->Html->link($messageRecipient['Message']['id'], array('controller' => 'messages', 'action' => 'view', $messageRecipient['Message']['id']))."</dd>";
		echo "<dt>".__('Recipient User')."</dt>";
		echo "<dd>".$this->Html->link($messageRecipient['RecipientUser']['username'], array('controller' => 'users', 'action' => 'view', $messageRecipient['RecipientUser']['id']))."</dd>";
		echo "<dt>".__('Bool Read')."</dt>";
		echo "<dd>".h($messageRecipient['MessageRecipient']['bool_read'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Message Recipient'), array('action' => 'edit', $messageRecipient['MessageRecipient']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Message Recipient'), array('action' => 'delete', $messageRecipient['MessageRecipient']['id']), array(), __('Are you sure you want to delete # %s?', $messageRecipient['MessageRecipient']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Message Recipients'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Message Recipient'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Messages'), array('controller' => 'messages', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Message'), array('controller' => 'messages', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Recipient User'), array('controller' => 'users', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
