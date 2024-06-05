<script>
	$(document).ready(function(){
		$( 'textarea.editor' ).ckeditor();
	});
</script>
<div class="contacts form fullwidth">
<?php 
	//pr($selectedContacts);
	
	$emailsForContacts="";
	foreach ($selectedContacts as $selectedContact){
		$emailsForContacts.=$selectedContact['Contact']['email']."\r\n";
	}
	$ccEmails="";
	foreach ($defaultCC as $cc){
		$ccEmails.=$cc."\r\n";
	}
	
	echo $this->Form->create('Form'); 
	echo "<fieldset>";
		echo "<legend>".__('Send System Email')."</legend>";
		echo "<div class='container-fluid'>";
			echo "<div class='row'>";
				echo "<div class='col-md-6'>";
					echo "<h3>Email addresses</h3>";
					echo $this->Form->input('email_from',array('default'=>$emailTo,'options'=>$availableEmailAddresses));
					echo $this->Form->input('email_to',array('default'=>UWC_EMAIL_SHORT_COURSES));
					echo "<p>CC: put here the email addresses of UWC volunteers that are involved and need to be kept in the loop</p>";
					echo "<p>Put each email on a separate line or use semicolon (\";\") or commas (\",\") to separate them</p>";
					echo $this->Form->textarea('cc',array('default'=>$ccEmails,'rows'=>6));
					echo "<p>BCC put here the email addresses of people you want to address</p>";
					echo "<p>Put each email on a separate line or use semicolon (\";\") or commas (\",\") to separate them</p>";
					echo $this->Form->textarea('bcc',array('default'=>$emailsForContacts,'rows'=>6));
				echo "</div>";
				echo "<div class='col-md-6'>";
					echo "<h3>Email subject and body</h3>";
					echo $this->Form->input('subject',array('default'=>'UWC Belgium informs you that...','required'=>'required'));
					echo $this->Form->textarea('body',array('rows'=>'15','class'=>'editor'));					
				echo "</div>";
			echo "</div>";
		echo "</div>";	
		
	echo "</fieldset>";
	echo $this->Form->Submit(__('Submit')); 
	echo $this->Form->end(); 
		
	
?>
</div>

<?php 		
	switch ($this->Session->read('User.role_id')) {
		case ROLE_SC_APPLICANT:
		case ROLE_SC_ADMIN:
		case ROLE_SC_READER:
			break;
		case ROLE_SC_APPLICANT:
		case ROLE_SC_ADMIN:
		case ROLE_SC_READER:
			echo "<div class='actions'>";
				echo "<h3>".__('Actions')."</h3>";
				echo "<ul>";
					
					echo "<br>";
					echo "<li>".$this->Html->link(__('List Reader Scores'), array('controller' => 'reader_scores', 'action' => 'index'))." </li>";
					echo "<li>".$this->Html->link(__('New Reader Score'), array('controller' => 'reader_scores', 'action' => 'add'))." </li>";
					echo "<!--li>".$this->Html->link(__('List Applicants'), array('controller' => 'applicants', 'action' => 'index'))." </li-->";
					echo "<!--li>".$this->Html->link(__('New Applicant'), array('controller' => 'applicants', 'action' => 'add'))." </li-->";
				echo "</ul>";
			echo "</div>";
			break;
	}
?>
		
