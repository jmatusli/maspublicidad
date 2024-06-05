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
	
  // 20171214 display of users should not depend on VIP status of client, as association applies to all clients
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
    <?php if ($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT) { ?>
      $('#VendorList').addClass('hidden');
    <?php } ?>
	});
</script>

<div class="clients form">
<?php 
	echo $this->Form->create('Client'); 
	echo "<fieldset>";
		echo "<legend>".__('Add Client')."</legend>";
		echo "<div class='container-fluid'>";
				echo "<div class='rows'>";	
					echo "<div class='col-sm-6'>";	
						echo $this->Form->input('name');
						echo $this->Form->input('ruc');
						echo $this->Form->input('address');
						echo $this->Form->input('phone');
						echo $this->Form->input('cell');
						echo $this->Form->input('bool_active',['checked'=>true,'div'=>['class'=>'hidden']]);
            echo $this->Form->input('bool_generic',['label'=>'Cliente genérico','checked'=>false]);
						if ($userrole == ROLE_ADMIN) { 
							echo $this->Form->input('bool_vip');
						}
						else {
							echo $this->Form->input('bool_vip',['onclick'=>'return false']);
						}
						echo $this->Form->input('creating_user_id',['value'=>$userid,'type'=>'hidden']);
					echo "</div>";	
					echo "<div class='col-sm-6'>";		
						echo "<div id='VendorList' style='width:45%;float:left;clear:none;'>";
						echo "<h3>Vendedores Relacionados</h3>";
						for ($u=0;$u<count($users);$u++){
              if ($users[$u]['User']['id']==$userid){
                $defaultValue=true;
              }
              else {
                $defaultValue=false;
              }
							echo $this->Form->input('User.'.$u.'.id',['type'=>'checkbox','default'=>$defaultValue,'label'=>$users[$u]['User']['first_name']." ".$users[$u]['User']['last_name'],'div'=>['class'=>'checkboxleftbig']]);
						}
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
		for ($i = 0;$i < MAX_CONTACTS;$i++) { 
			if ($i==0){
				echo "<tr row='".$i."'>";
			} 
			else {
				echo "<tr row='".$i."' class='hidden'>";
			} 
				echo "<td class='contactfirstname'>".$this->Form->input('Contact.'.$i.'.first_name',['label'=>false,'required'=>false])."</td>";
				echo "<td class='contactlastname'>".$this->Form->input('Contact.'.$i.'.last_name',['label'=>false,'required'=>false])."</td>";
				echo "<td class='contactphone'>".$this->Form->input('Contact.'.$i.'.phone',['label'=>false,'required'=>false])."</td>";
				echo "<td class='contactcell'>".$this->Form->input('Contact.'.$i.'.cell',['label'=>false,'required'=>false])."</td>";
				echo "<td class='contactemail'>".$this->Form->input('Contact.'.$i.'.email',['label'=>false,'required'=>false])."</td>";
				echo "<td class='contactdepartment'>".$this->Form->input('Contact.'.$i.'.department',['label'=>false,'required'=>false])."</td>";
				echo "<td class='contactactions'>";
					echo "<button type='button' class='removeContact'>".__('Remover Contacto')."</button>";
					echo "<button type='button' class='addContact' >".__('Añadir Contacto')."</button>";
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
