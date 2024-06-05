<script>
	//$('body').on('change','#ClientBoolVip',function(e){	
	//	displayUserList();
	//});	
	
	$('body').on('click','.addContact',function(){	
		var tableRow=$('#contactosParaCliente tbody tr.hidden:first');
		tableRow.removeClass("hidden");
	});

	$('body').on('click','.removeContact',function(e){	
		var tableRow=$(this).closest('tr').remove();
		return false;
	});	
	
	//function displayUserList(){
	//	if ($('#ClientBoolVip').is(':checked')){
	//		$('#VendorList').removeClass('hidden');
	//	}
	//	else {
	//		$('#VendorList').addClass('hidden');
	//	}
	//}
	
	$(document).ready(function(){
		//displayUserList();
	});
</script>
<div class="clients form">
<?php 
	echo $this->Form->create('Client'); 
		echo "<fieldset>";
			echo "<legend>".__('Edit Client')." ".$this->request->data['Client']['name']."</legend>";
			echo "<div class='container-fluid'>";
				echo "<div class='rows'>";	
					echo "<div class='col-md-6'>";
						echo $this->Form->input('id');
						echo $this->Form->input('name');
						echo $this->Form->input('ruc');
						echo $this->Form->input('address');
						echo $this->Form->input('phone');
						echo $this->Form->input('cell');
						echo $this->Form->input('bool_active');
						if ($userrole==ROLE_ADMIN) { 
							echo $this->Form->input('bool_vip');
						}
						else {
							echo $this->Form->input('bool_vip',array('onclick'=>'return false'));
						}
            echo $this->Form->input('creating_user_id',array('type'=>'hidden'));
					echo "</div>";
					echo "<div class='col-md-3'>";
					echo "<h3>Usuarios Ya Asociados</h3>";
					if (empty($usersAssociatedWithClient)){
						echo "<p>No hay usuarios asociados con este cliente aun</p>";
					}
					else {
					echo "<ul>";
						foreach ($usersAssociatedWithClient as $user){
							echo "<li>".$this->Html->Link($user['User']['first_name']." ".$user['User']['last_name'],array('controller'=>'user','action'=>'view',$user['User']['id']))."</li>";
						}
						echo "</ul>";
					}
				echo "</div>";
				echo "<div class='col-md-3'>";
					//pr($users);
					//echo "<div id='VendorList' style='width:45%;float:left;clear:none;' class='col-md-6'>";
					echo "<div id='VendorList'>"
						;echo "<h3>Vendedores Relacionados</h3>";
						for ($u=0;$u<count($users);$u++){
							$userChecked=false;
							if (!empty($users[$u]['ClientUser'])){
								//pr($users[$u]['ClientUser']);
								$userChecked=$users[$u]['ClientUser'][0]['bool_assigned'];
							}
							//pr($users[$u]);
							echo $this->Form->input('User.'.$u.'.id',array('type'=>'checkbox','checked'=>$userChecked,'label'=>$users[$u]['User']['first_name']." ".$users[$u]['User']['last_name'],'div'=>array('class'=>'checkboxleftbig')));
						}
					
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	echo "</fieldset>";
	

	echo "<h3>Contactos para este Cliente</h3>";
	echo "<table id='contactosParaCliente'>"; 
		echo "<thead>";
			echo "<tr>";
				echo "<th>".__('First Name')."</th>";
				echo "<th>".__('Last Name')."</th>";
				echo "<th>".__('Phone')."</th>";
				echo "<th>".__('Cell')."</th>";
				echo "<th>".__('Email')."</th>";
				echo "<th>".__('Department')."</th>";
				echo "<th style='width:20%;'>".__('Actions')."</th>";
			echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		for ($i=0;$i<count($existingContacts);$i++) { 
			echo "<tr row='".$i."'>";
				echo "<td class='contactfirstname'>".$this->Form->input('Contact.'.$i.'.first_name',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactlastname'>".$this->Form->input('Contact.'.$i.'.last_name',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactphone'>".$this->Form->input('Contact.'.$i.'.phone',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactcell'>".$this->Form->input('Contact.'.$i.'.cell',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactemail'>".$this->Form->input('Contact.'.$i.'.email',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactdepartment'>".$this->Form->input('Contact.'.$i.'.department',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactactions'>";
					echo "<button class='removeContact' type='button'>".__('Remover Contacto')."</button>";
					echo "<button class='addContact' type='button'>".__('Añadir Contacto')."</button>";
				echo "</td>";
			echo "</tr>";			
		}		
		for ($i=count($existingContacts);$i<25;$i++) { 
			if ($i==count($existingContacts)){
				echo "<tr row='".$i."'>";
			} 
			else {
				echo "<tr row='".$i."' class='hidden'>";
			} 
				echo "<td class='contactfirstname'>".$this->Form->input('Contact.'.$i.'.first_name',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactlastname'>".$this->Form->input('Contact.'.$i.'.last_name',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactphone'>".$this->Form->input('Contact.'.$i.'.phone',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactcell'>".$this->Form->input('Contact.'.$i.'.cell',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactemail'>".$this->Form->input('Contact.'.$i.'.email',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactdepartment'>".$this->Form->input('Contact.'.$i.'.department',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactactions'>";
					echo "<button type='button' class='removeContact'>".__('Remover Contacto')."</button>";
					echo "<button type='button' class='addContact'>".__('Añadir Contacto')."</button>";
				echo "</td>";
			echo "</tr>";			
		}		
		echo "</tbody>";
	echo "</table>";
	echo $this->Form->end(__('Submit')); 
?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<!--li><?php //echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Client.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('Client.id'))); ?></li-->
		<li><?php echo $this->Html->link(__('List Clients'), array('action' => 'index')); ?></li>
		<br/>
	<?php
		if ($bool_contact_index_permission){
			echo "<li>".$this->Html->link(__('List Contacts'), array('controller' => 'contacts', 'action' => 'index'))."</li>";
		}
		if ($bool_contact_add_permission){
			echo "<li>".$this->Html->link(__('New Contact'), array('controller' => 'contacts', 'action' => 'add'))."</li>";
		}
		if ($bool_invoice_index_permission){
			echo "<li>".$this->Html->link(__('List Invoices'), array('controller' => 'invoices', 'action' => 'index'))."</li>";
		}
		if ($bool_invoice_add_permission){
			echo "<li>".$this->Html->link(__('New Invoice'), array('controller' => 'invoices', 'action' => 'add'))."</li>";
		}
		if ($bool_quotation_index_permission){
			echo "<li>".$this->Html->link(__('List Quotations'), array('controller' => 'quotations', 'action' => 'index'))."</li>";
		}
		if ($bool_quotation_add_permission){
			echo "<li>".$this->Html->link(__('New Quotation'), array('controller' => 'quotations', 'action' => 'add'))."</li>";
		}
	?>
	</ul>
</div>
