<div class="systemEmails view fullwidth">
<?php 
	echo "<h2>".__('System Email')."</h2>";
	echo "<dl>";
		echo "<dt>".__('From')."</dt>";
		echo "<dd>".h($systemEmail['SystemEmail']['email_from'])."</dd>";
		echo "<dt>".__('Subject')."</dt>";
		echo "<dd>".h($systemEmail['SystemEmail']['subject'])."</dd>";
		echo "<dt>".__('To')."</dt>";
		echo "<dd>";
		if (!empty($systemEmail['SystemEmailRecipient'])){
			foreach ($systemEmail['SystemEmailRecipient'] as $emailRecipient){
				echo $emailRecipient['email_recipient']."<br/>";
			}
		}
		else {
			echo "-";
		}
		echo "</dd>";
		echo "<dt>".__('Cc')."</dt>";
		echo "<dd>";
		if (!empty($systemEmail['SystemEmailCc'])){
			foreach ($systemEmail['SystemEmailCc'] as $emailCc){
				echo $emailCc['email_cc']."<br/>";
			}
		}
		else {
			echo "-";
		}
		echo "</dd>";
		echo "<dt>".__('Bcc')."</dt>";
		echo "<dd>";
		if (!empty($systemEmail['SystemEmailBcc'])){
			foreach ($systemEmail['SystemEmailBcc'] as $emailBcc){
				echo $emailBcc['email_bcc']."<br/>";
			}
		}
		else {
			echo "-";
		}
		echo "</dd>";
		//echo "<dt>".__('Boundary')."</dt>";
		//echo "<dd>".h($systemEmail['SystemEmail']['boundary'])."</dd>";
		echo "<dt>".__('Attachment')."</dt>";
		echo "<dd>";
		if (!empty($systemEmail['SystemEmailAttachment'])){
			foreach ($systemEmail['SystemEmailAttachment'] as $attachment){
				$url=$attachment['url_attachment'];						
				echo "<a href='".($this->Html->url('/').$url)."' target='_blank'>".substr($url,strrpos($url,"/")+1)."</a><br/>";
			}
		}
		echo "</dd>";
	echo "</dl>";
	echo "<br/>";
	echo "<p>".__('Body')."</p>";
	echo $systemEmail['SystemEmail']['body_html'];
		
?> 
</div>
