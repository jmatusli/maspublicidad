<script>
	$('body').on('change','#UserFirstName',function(){
		updateAbbreviation();
	});
	$('body').on('change','#UserLastName',change(function(){
		updateAbbreviation();
	});
	function updateAbbreviation(){
		var firstname=$('#UserFirstName').val();
		var lastname=$('#UserLastName').val();
		var abbreviation="";
		if (firstname.length>0){
			abbreviation+=(firstname.charAt(0)).toUpperCase();
		}
		if (lastname.length>0){
			abbreviation+=(lastname.charAt(0)).toUpperCase();
		}
		$('#UserAbbreviation').val(abbreviation);
	}

</script>
<div class="users form">
<?php 
	echo $this->Form->create('User'); 
	echo "<fieldset>";
		echo "<legend>".__('Edit User')." ".$this->request->data['User']['first_name']." ".$this->request->data['User']['last_name']."</legend>";
		echo "<div class='container-fluid'>";
			echo "<div class='rows'>";	
				echo "<div class='col-md-6'>";
					echo $this->Form->input('id');
					echo $this->Form->input('username');
					echo $this->Form->input('pwd',array('value'=>'','required'=>false,'label'=>__('Password'),'type'=>'password'));
					echo $this->Form->input('role_id');
					echo $this->Form->input('department_id',array('empty'=>array(0=>'Seleccione Departamento')));
					echo $this->Form->input('company_id',array('empty'=>array(0=>'Seleccione Empresa')));
					echo $this->Form->input('first_name');
					echo $this->Form->input('last_name');
					echo $this->Form->input('abbreviation');
					echo $this->Form->input('email');
					echo $this->Form->input('phone');
          echo $this->Form->input('bool_active',['div'=>['class'=>'checkboxleft']]);
          echo $this->Form->input('bool_show_in_list',['label'=>'Mostar en lista','div'=>['class'=>'checkboxleft']]);
					echo $this->Form->Submit(__('Submit'));
				echo "</div>";
				echo "<div class='col-md-3'>";
					echo "<h3>Clientes Ya Asociados</h3>";
					echo "<ul>";
					foreach ($clientsAssociatedWithUser as $client){
						echo "<li>".$this->Html->Link($client['Client']['name'],array('controller'=>'clients','action'=>'view',$client['Client']['id']))."</li>";
					}
					echo "</ul>";
					if (!empty($clientsCreatedByUser)){
						echo "<h3>Clientes Creados por este Usuario</h3>";
						echo "<table>";
							echo "<thead>";
								echo "<tr>";
									echo "<th>Cliente</th>";
									echo "<th>Fecha de Creación</th>";
								echo "</tr>";								
							echo "</thead>";
							echo "<tbody>";
							foreach ($clientsCreatedByUser as $client){
								$createdDateTime=new DateTime($client['Client']['created']);
								echo "<tr>";
									echo "<td>".$this->Html->Link($client['Client']['name'],array('controller'=>'clients','action'=>'view',$client['Client']['id']))."</td>";
									echo "<td>".$createdDateTime->format('d-m-Y')."</td>";
								echo "</tr>";										
							}
							echo "</tbody>";
						echo "</table>";
					}
				echo "</div>";
				echo "<div class='col-md-3'>";
					//echo "<div id='ClientList' style='width:45%;float:left;clear:none;' class='col-md-6'>";
					echo "<div id='ClientList'>";
						echo "<h3>Asociar con Clientes</h3>";
						for ($c=0;$c<count($clients);$c++){
							$clientChecked=false;
							if (!empty($clients[$c]['ClientUser'])){
								$clientChecked=$clients[$c]['ClientUser'][0]['bool_assigned'];;
							}
							echo $this->Form->input('Client.'.$c.'.id',array('type'=>'checkbox','checked'=>$clientChecked,'label'=>$clients[$c]['Client']['name'],'div'=>array('class'=>'checkboxleftbig')));
						}
					
					echo "</div>";
				echo "</div>";
			echo "</div>";	
		echo "</div>";
	echo "</fieldset>";
	echo $this->Form->Submit(__('Submit'));
	echo $this->Form->end(); 
?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<!--li><?php // echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('User.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('User.id'))); ?></li-->
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?></li>
		<!--li><?php echo $this->Html->link(__('List Roles'), array('controller' => 'roles', 'action' => 'index')); ?> </li-->
		<!--li><?php echo $this->Html->link(__('New Role'), array('controller' => 'roles', 'action' => 'add')); ?> </li-->
		<br/>
		<?php if ($userrole==ROLE_ADMIN) { ?>	
		<li><?php echo $this->Html->link(__('List User Logs'), array('controller' => 'user_logs', 'action' => 'index')); ?> </li>
		<?php } ?>	
		<!--li><?php echo $this->Html->link(__('New User Log'), array('controller' => 'user_logs', 'action' => 'add')); ?> </li-->
	</ul>
</div>
