<div class="users form">
<?php 
	echo $this->Form->create('User'); 
	echo "<fieldset>";
		echo "<legend>".__('Add User')."</legend>";
		echo "<div class='container-fluid'>";
			echo "<div class='rows'>";	
				echo "<div class='col-md-6'>";				
					echo $this->Form->input('username');
					echo $this->Form->input('password');
					echo $this->Form->input('role_id');
					echo $this->Form->input('department_id',array('default'=>0,'empty'=>array(0=>'Seleccione Departamento')));
					echo $this->Form->input('company_id',array('default'=>0,'empty'=>array(0=>'Seleccione Empresa')));
					echo $this->Form->input('first_name');
					echo $this->Form->input('last_name');
					echo $this->Form->input('abbreviation');
					echo $this->Form->input('email');
					echo $this->Form->input('phone');
          echo $this->Form->input('bool_active',array('value'=>1,'type'=>'hidden'));
					echo $this->Form->Submit(__('Submit'));
				echo "</div>";	
					echo "<div class='col-md-6'>";		
						echo "<div id='ClientList' style='width:45%;float:left;clear:none;'>";
						echo "<h3>Clientes Relacionados</h3>";
						for ($c=0;$c<count($clients);$c++){
							echo $this->Form->input('Client.'.$c.'.id',array('type'=>'checkbox','default'=>false,'label'=>$clients[$c]['Client']['name'],'div'=>array('class'=>'checkboxleftbig')));
						}
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
<script>
	$('#UserFirstName').change(function(){
		updateAbbreviation();
	});
	$('#UserLastName').change(function(){
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
