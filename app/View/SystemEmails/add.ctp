<script>
	$('body').on('submit','#SystemEmailAddForm',function(e){	
		var i=0;
		//alert("the value of file "+i+" with identifier #DocumentUrlFile0 is "+$('#DocumentUrlFile0').val());
		while (i < <?php echo NUM_FILES; ?>){
			//alert("the value of file "+i+" with identifier #DocumentUrlFile"+i+" is "+$('#DocumentUrlFile'+i).val());
			if ($('#DocumentUrlFile'+i).val().length>0){
				if($('#DocumentUrlFile'+i)[0].files[0].size > 5242880){
					alert("La imagen excede 5MB!  No se puede guardar la cotización!");        
					e.preventDefault();    
				}
				else {
					//var file_extension=get_extension($('#DocumentUrlFile'+i).val());
					////alert ("the file extension is "+file_extension);
					//var bool_valid_extension=check_validity_extension(file_extension);
					//if (!bool_valid_extension){
					//	alert("Solamente se permiten archivos jpg, jpeg, png y pdf!  No se puede guardar la cotización!");        
					//	e.preventDefault();    
					//}
				}
			}
			i++;
		}
	});
	
	$('body').on('click','.addAttachment',function(){
		var tableRow=$('#attachments tbody tr.hidden:eq(1)');
		tableRow.removeClass("hidden");
	});
	$('body').on('click','.removeAttachment',function(){
		var tableRow=$(this).closest('tr').remove();
		calculateTotal();
	});
	
	//function get_extension(filename) {    
	//	var parts = filename.split('.');    
	//	return parts[parts.length - 1].toLowerCase();
	//}
	//function check_validity_extension(file_extension) {    
	//	if (file_extension=="jpg" ||file_extension=="jpeg" ||file_extension=="png" ||file_extension=="pdf"){
	//		return true;
	//	} 
	//	else {
	//		return false;
	//	}
	//}
	$(document).ready(function(){
		$('textarea.editor' ).ckeditor();
		$('select.fixed option:not(:selected)').attr('disabled', true);
	});
</script>
<div class="system_emails form fullwidth">
<?php 
	//pr($selectedContacts);
	
	$ccEmails="";
	foreach ($defaultCC as $cc){
		$ccEmails.=$cc."\r\n";
	}
	
	echo $this->Form->create('SystemEmail', array('enctype' => 'multipart/form-data')); 
	echo "<fieldset>";
		echo "<legend>".__('Enviar Correo de Sistema')."</legend>";
		echo "<div class='container-fluid'>";
			echo "<div class='row'>";
				echo "<div class='col-md-6'>";
					echo "<h3>Correos Electrónicos</h3>";
					if ($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT) { 
						echo $this->Form->input('email_from',array('default'=>$emailFrom,'options'=>$availableEmailAddresses, 'class'=>'fixed'));
					}
					else {
						echo $this->Form->input('email_from',array('default'=>$emailFrom,'options'=>$availableEmailAddresses));
					}
					echo $this->Form->input('email_recipient',array('default'=>$emailRecipient));
					echo "<p class='comment'>CC: ponga aquí las direcciones de personas que tienen que estar avisados</p>";
					echo "<p class='comment'>Ponga cada correo en una línea separada o utilice semicolon (\";\") o comas (\",\") para separarlos</p>";
					echo $this->Form->input('email_cc',array('label'=>'CC','default'=>$emailCC,'rows'=>4,'type'=>'textarea'));
					echo "<p class='comment'>BCC: correos que reciben el mensaje pero que no miren los demás destinatarios</p>";
					echo "<p class='comment'>Ponga cada correo en una línea separada o utilice semicolon (\";\") o comas (\",\") para separarlos</p>";
					echo $this->Form->input('email_bcc',array('label'=>'BCC','rows'=>4,'type'=>'textarea'));
				echo "</div>";
				echo "<div class='col-md-6'>";
					echo "<h3>Asunto y Mensaje del Correo</h3>";
					echo $this->Form->input('subject',array('default'=>$subject,'required'=>'required'));
					echo $this->Form->textarea('body_html',array('default'=>$body,'rows'=>'15','class'=>'editor'));					
					echo "<p class='comment'>Puede añadir hasta ".NUM_FILES." archivos</p>";
					echo "<table id='attachments'>"; 
						echo "<thead>";
							echo "<tr>";
								echo "<th>".__('File')."</th>";
								echo "<th>".__('Remover')."</th>";
								echo "<th>".__('Otro')."</th>";
							echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						
						for ($i=0;$i<=NUM_FILES;$i++){
							// 20170512 FOR SOME BIZAR REASON JQUERY DID NOT READ THE VALUE OF AND THEREFORE DID NOT VALIDATE THE FIRST FILE INPUT
							if ($i==0){
								echo "<tr row='".$i."' class='hidden'>";
							}
							elseif ($i==1){
								echo "<tr row='".$i."'>";
							}
							else {
								echo "<tr row='".$i."' class='hidden'>";
							}
								echo "<td class='attachment'>".$this->Form->input('Document.url_file.'.$i,array('label'=>'Cargar Archivo','type'=>'file'))."</td>";
								echo "<td><button class='removeAttachment' type='button'>".__('Remover Archivo Adjunto')."</button></td>";
								if ($i==NUM_FILES){
									echo "<td>&nbsp;</td>";
								}
								else{
									echo "<td ><button class='addAttachment' type='button'>".__('Añadir Otro Archivo Adjunto')."</button></td>";
								}
							echo "</tr>";
						}
						echo "</tbody>";
					echo "</table>";	
				echo "</div>";
			echo "</div>";
		echo "</div>";	
		
	echo "</fieldset>";
	echo $this->Form->Submit(__('Send')); 
	echo $this->Form->end(); 
?>
</div>