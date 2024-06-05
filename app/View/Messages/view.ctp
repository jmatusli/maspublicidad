<div class="messages view fullwidth">
<?php 
	echo "<h2>".__('Message')."</h2>";
	echo "<dl>";
		$messageDateTie=new DateTime($message['Message']['created']);
		echo "<dt>".__('From User')."</dt>";
		echo "<dd>".$message['FromUser']['username']."</dd>";
		echo "<dt>".__('Subject')."</dt>";
		echo "<dd>".h($message['Message']['subject'])."</dd>";
		echo "<dt>".__('Body Text')."</dt>";
		echo "<dd>".h($message['Message']['body_text'])."</dd>";
		echo "<dt>".__('Archivo Adjunto')."</dt>";
		if (!empty($message['Message']['url_image'])){
			$url=$message['Message']['url_image'];
			$url=$message['Message']['url_image'];
			echo "<dd><a href='".$this->Html->url('/').$url."' target='_blank'>".$url."</a></dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
	echo "</dl>";
?> 
</div>
<!-- div class='actions'>
<?php 
	/*
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Message'), array('action' => 'edit', $message['Message']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Message'), array('action' => 'delete', $message['Message']['id']), array(), __('Are you sure you want to delete # %s?', $message['Message']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Messages'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Message'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New From User'), array('controller' => 'users', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Message Recipients'), array('controller' => 'message_recipients', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Message Recipient'), array('controller' => 'message_recipients', 'action' => 'add'))."</li>";
	echo "</ul>";
	*/
?> 
</div -->
<div class="related">
<?php 
	if ((!empty($message['MessageRecipient'])) && ($message['Message']['from_user_id']==$userid)){
		echo "<h3>".__('Destinatarios')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				//echo "<th>".__('Message Id')."</th>";
				echo "<th>".__('Destinatarios')."</th>";
				echo "<th>".__('Bool Read')."</th>";
				//echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($message['MessageRecipient'] as $messageRecipient){ 
			echo "<tr>";
				//echo "<td>".$messageRecipient['message_id']."</td>";
				echo "<td>".$messageRecipient['RecipientUser']['username']."</td>";
				echo "<td>".$messageRecipient['bool_read']."</td>";
				//echo "<td class='actions'>";
					//echo $this->Html->link(__('View'), array('controller' => 'message_recipients', 'action' => 'view', $messageRecipient['id']));
					//echo $this->Html->link(__('Edit'), array('controller' => 'message_recipients', 'action' => 'edit', $messageRecipient['id']));
					//echo $this->Form->postLink(__('Delete'), array('controller' => 'message_recipients', 'action' => 'delete', $messageRecipient['id']), array(), __('Are you sure you want to delete # %s?', $messageRecipient['id']));
				//echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
