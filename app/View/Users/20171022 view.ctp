<div class="users view">
<?php 
	echo "<h2>".__('User')." ".$user['User']['first_name']." ".$user['User']['last_name']." (".($user['User']['bool_active']?__('Activo'):__('Desactivado')).")</h2>";
	echo $this->Form->create('Report'); 
		echo "<fieldset>"; 
		echo "<div class='container-fluid'>";
			echo "<div class='rows'>";
				echo "<div class='col-md-12'>";
					echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2014,'maxYear'=>date('Y')));
					echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2014,'maxYear'=>date('Y')));
					echo $this->Form->input('Report.currency_id',array('label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId));
				echo "</div>";
			echo "</div>";
		echo "</div>";
		echo  "</fieldset>";
		echo "<button id='previousmonth' class='monthswitcher'>".__('Previous Month')."</button>"; 
		echo "<button id='nextmonth' class='monthswitcher'>".__('Next Month')."</button>"; 
	echo $this->Form->end(__('Refresh')); 
	echo "</br>";
	//echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumen'), array( 'class' => 'btn btn-primary'));
	echo "<dl>";
		echo "<dt>". __('Username')."</dt>";
		echo "<dd>". h($user['User']['username'])."</dd>";
		echo "<dt>". __('Role')."</dt>";
		echo "<dd>". $this->Html->link($user['Role']['name'], array('controller' => 'roles', 'action' => 'view', $user['Role']['id']))."</dd>";
		echo "<dt>". __('Department')."</dt>";
		if (!empty($user['Department']['id'])){
			echo "<dd>". $this->Html->link($user['Department']['name'], array('controller' => 'departments', 'action' => 'view', $user['Department']['id']))."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>". __('Company')."</dt>";
		if (!empty($user['Company']['id'])){
			echo "<dd>". $this->Html->link($user['Company']['name'], array('controller' => 'companies', 'action' => 'view', $user['Company']['id']))."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>". __('First Name')."</dt>";
		echo "<dd>". h($user['User']['first_name'])."</dd>";
		echo "<dt>". __('Last Name')."</dt>";
		echo "<dd>". h($user['User']['last_name'])."</dd>";
		echo "<dt>". __('Email')."</dt>";
		if (!empty($user['User']['email'])){
			echo "<dd>". h($user['User']['email'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>". __('Phone')."</dt>";
		if (!empty($user['User']['phone'])){
			echo "<dd>". h($user['User']['phone'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>". __('Total Cotizado en Período')."</dt>";
		echo "<dd>". h($user['User']['quotation_total'])."</dd>";
		echo "<dt>". __('Total Facturado en Período')."</dt>";
		echo "<dd>". h($user['User']['invoice_total'])."</dd>";
		//echo "<!--dt>". __('Created')."</dt-->";
		//echo "<!--dd>". h($user['User']['created'])."</dd-->";
		//echo "<!--dt>". __('Modified')."</dt-->";
		//echo "<!--dd>". h($user['User']['modified'])."</dd-->";
	echo "</dl>";
?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<?php if ($userrole==ROLE_ADMIN) { ?>	
		<li><?php echo $this->Html->link(__('Edit User'), array('action' => 'edit', $user['User']['id'])); ?> </li>
		<?php } ?>	
		<!--li><?php // echo $this->Form->postLink(__('Delete User'), array('action' => 'delete', $user['User']['id']), array(), __('Are you sure you want to delete # %s?', $user['User']['id'])); ?> </li-->
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?> </li>
		<?php if ($userrole==ROLE_ADMIN) { ?>	
		<li><?php echo $this->Html->link(__('New User'), array('action' => 'add')); ?> </li>
		<?php } ?>	
		<!--li><?php echo $this->Html->link(__('List Roles'), array('controller' => 'roles', 'action' => 'index')); ?> </li-->
		<!--li><?php echo $this->Html->link(__('New Role'), array('controller' => 'roles', 'action' => 'add')); ?> </li-->
		<br/>
		<?php if ($userrole==ROLE_ADMIN) { ?>	
		<li><?php echo $this->Html->link(__('List User Logs'), array('controller' => 'user_logs', 'action' => 'index')); ?> </li>
		<?php } ?>	
		<!--li><?php echo $this->Html->link(__('New User Log'), array('controller' => 'user_logs', 'action' => 'add')); ?> </li-->
	</ul>
</div>
<div class="related">
<?php 
	if(!empty($user['ClientUser'])){
		echo "<h3>".__('Clientes asociados con este Usuario')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Name')."</th>";
				echo "<th>".__('Address')."</th>";
				echo "<th>".__('Ruc')."</th>";
				echo "<th>".__('Phone')."</th>";
				echo "<th>".__('Cell')."</th>";
				echo "<th># Cotizaciones</th>";
				echo "<th># Facturas</th>";
				echo "<th>$ Cotizaciones</th>";
				echo "<th>$ Facturas</th>";
				echo "<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($user['ClientUser'] as $clientUser){
			//pr($clientUser);
			echo "<tr>";
				echo "<td>".$this->Html->link($clientUser['Client']['name'], array('controller' => 'clients', 'action' => 'view', $clientUser['Client']['id']))."</td>";
				echo "<td>".$clientUser['Client']['address']."</td>";
				echo "<td>".$clientUser['Client']['ruc']."</td>";
				echo "<td>".$clientUser['Client']['phone']."</td>";
				echo "<td>".$clientUser['Client']['cell']."</td>";
				echo "<td>".count($clientUser['Client']['Quotation'])."</td>";
				echo "<td>".count($clientUser['Client']['Invoice'])."</td>";
				echo "<td class='".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$clientUser['Client']['quotation_total']."</span></td>";
				echo "<td class='".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$clientUser['Client']['invoice_total']."</span></td>";
				echo "<td class='actions'>";
					if ($bool_client_edit_permission){
						echo $this->Html->link(__('Edit'), array('controller' => 'clients', 'action' => 'edit', $clientUser['Client']['id']));
					}
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
<div class="related">
<?php 
	//pr($user['Client']);
	if(!empty($user['Client'])){
		echo "<h3>".__('Clientes CREADOS por este Usuario')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Name')."</th>";
				echo "<th>".__('Address')."</th>";
				echo "<th>".__('Ruc')."</th>";
				echo "<th>".__('Phone')."</th>";
				echo "<th>".__('Cell')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($user['Client'] as $client){
			echo "<tr>";
				echo "<td>".$this->Html->link($client['name'], array('controller' => 'clients', 'action' => 'view', $client['id']))."</td>";
				echo "<td>".$client['address']."</td>";
				echo "<td>".$client['ruc']."</td>";
				echo "<td>".$client['phone']."</td>";
				echo "<td>".$client['cell']."</td>";
				echo "<td class='actions'>";
					if ($bool_client_edit_permission){
						echo $this->Html->link(__('Edit'), array('controller' => 'clients', 'action' => 'edit', $client['id']));
					}
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>

<div class="related">
<?php 
	if (!empty($user['UserLog'])){
		echo "<h3>Evento de Acceso</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>Fecha</th>";
					echo "<th>Evento</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			foreach ($user['UserLog'] as $userLog){
				$createdDateTime=new DateTime($userLog['created']);
				echo "<tr>";
					echo "<td>".$createdDateTime->format('d-m-Y H:i:s')."</td>";
					echo "<td>".$userLog['event']."</td>";
				echo "</tr>";
			}
			echo "</tbody>";
		echo "</table>";
	}
?>

</div>
