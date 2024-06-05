<script>
	function formatNumbers(){
		$("td.number span.amountright").each(function(){
			if (Math.abs(parseFloat($(this).text()))<0.001){
				$(this).text("0");
			}
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,2,'.',',');
		});
	}
	
	function formatCSCurrencies(){
		$("td.CScurrency").each(function(){
			if (parseFloat($(this).find('.amountright').text())<0){
				$(this).find('.amountright').prepend("-");
			}
			$(this).find('.amountright').number(true,2);
			$(this).find('.currency').text("C$");
		});
	}
	
	function formatUSDCurrencies(){
		$("td.USDcurrency").each(function(){
			
			if (parseFloat($(this).find('.amountright').text())<0){
				$(this).find('.amountright').prepend("-");
			}
			$(this).find('.amountright').number(true,2);
			$(this).find('.currency').text("US$");
		});
	}
	
	$(document).ready(function(){
		formatNumbers();
		formatCSCurrencies();
		formatUSDCurrencies();
	});
</script>

<div class="users view">
<?php 
	echo "<h2>".__('User')." ".$user['User']['first_name']." ".$user['User']['last_name']." (".($user['User']['bool_active']?__('Activo'):__('Desactivado')).")</h2>";
	echo $this->Form->create('Report'); 
		echo "<fieldset>"; 
		echo "<div class='container-fluid'>";
			echo "<div class='rows'>";
        echo "<div class='col-md-6'>";	
          echo $this->Form->input('Report.unassociated_display_option_id',array('label'=>__('Clientes Asociados'),'default'=>$unassociatedDisplayOptionId));
					echo $this->Form->input('Report.active_display_option_id',array('label'=>__('Clientes Activos'),'default'=>$activeDisplayOptionId));
					echo $this->Form->input('Report.vip_display_option_id',array('label'=>__('Clientes Vip'),'default'=>$vipDisplayOptionId));
        if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
          echo $this->Form->input('Report.aggregate_option_id',array('label'=>__('Mostrar y Ordenar Por'),'default'=>$aggregateOptionId));
        }
        else {
          echo $this->Form->input('Report.aggregate_option_id',array('label'=>__('Mostrar y Ordenar Por'),'default'=>AGGREGATES_NONE,'type'=>'hidden'));
        }
          	echo $this->Form->input('Report.history_display_option_id',array('label'=>__('Mostrar Historial'),'default'=>$historyDisplayOptionId));
					echo $this->Form->input('Report.searchterm',array('label'=>__('Buscar')));
				echo "</div>";
				echo "<div class='col-md-6'>";
					echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2014,'maxYear'=>date('Y')));
					echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2014,'maxYear'=>date('Y')));
					echo $this->Form->input('Report.currency_id',array('label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId));
				echo "</div>";
			echo "</div>";
		echo "</div>";
		echo  "</fieldset>";
		echo "<button id='previousmonth' class='monthswitcher'>".__('Previous Month')."</button>"; 
		echo "<button id='nextmonth' class='monthswitcher'>".__('Next Month')."</button>"; 
    echo "<br/>";
	echo $this->Form->end(__('Refresh')); 
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarVistaUsuario',$user['User']['username']), array( 'class' => 'btn btn-primary'));
  echo "<br/>";
  $excelOutput="";
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
    echo "<dt>". __('Cantidad Cotizaciones en Período')."</dt>";
		echo "<dd>". number_format($user['User']['quotation_quantity'],0,".",",")."</dd>";
		echo "<dt>". __('Cantidad Facturas en Período')."</dt>";
		echo "<dd>". number_format($user['User']['invoice_quantity'],0,".",",")."</dd>";
    echo "<dt>". __('Total Cotizado en Período').($currencyId==CURRENCY_CS?" C$":" USD")."</dt>";
		echo "<dd>". number_format($user['User']['quotation_total'],2,".",",")."</dd>";
		echo "<dt>". __('Total Facturado en Período').($currencyId==CURRENCY_CS?" C$":" USD")."</dt>";
		echo "<dd>". number_format($user['User']['invoice_total'],2,".",",")."</dd>";
		//echo "<!--dt>". __('Created')."</dt-->";
		//echo "<!--dd>". h($user['User']['created'])."</dd-->";
		//echo "<!--dt>". __('Modified')."</dt-->";
		//echo "<!--dd>". h($user['User']['modified'])."</dd-->";
	echo "</dl>";
?>
</div>
<div class="actions">
<?php 
	echo '<h3>'.__('Actions').'</h3>';
	echo '<ul>';
		//if ($bool_edit_permission) {	
    if ($userRoleId == ROLE_ADMIN) {	
      echo '<li>'.$this->Html->link(__('Edit User'), ['action' => 'edit', $user['User']['id']]).'</li>';
      echo'<br/>';
		}
		echo '<li>'.$this->Html->link(__('List Users'), ['action' => 'index']).'</li>';
		if ($bool_add_permission) {
      echo '<li>'.$this->Html->link(__('New User'), ['action' => 'add']).'</li>';
		}
		
		if ($userRoleId == ROLE_ADMIN) {
      echo'<br/>';
      echo '<li>'.$this->Html->link(__('List User Logs'), ['controller' => 'user_logs', 'action' => 'resumen',$user['User']['id']]).'</li>';
		}	
		
	echo '</ul>';
?>  
</div>
<div class="related">
<?php 
	if(!empty($uniqueClients)){
    $tableHeader="";
    $tableHeader.="<thead>";
			$tableHeader.="<tr>";
				$tableHeader.="<th>".__('Name')."</th>";
				$tableHeader.="<th style='width:15%;'>".__('Address')."</th>";
				$tableHeader.="<th>".__('Ruc')."</th>";
				$tableHeader.="<th>".__('Phone')."</th>";
				$tableHeader.="<th>".__('Cell')."</th>";
        $tableHeader.=($historyDisplayOptionId?"<th style='width:15%;'>Historial de Asignaciones</th>":"");
				$tableHeader.="<th># Cotizaciones</th>";
				$tableHeader.="<th># Facturas</th>";
				$tableHeader.="<th>$ Cotizaciones</th>";
				$tableHeader.="<th>$ Facturas</th>";
				$tableHeader.="<th class='actions'>".__('Actions')."</th>";
			$tableHeader.="</tr>";
    $tableHeader.="</thead>";  
    $excelHeader="";
    $excelHeader.="<thead>";
			$excelHeader.="<tr>";
				$excelHeader.="<th>".__('Name')."</th>";
				$excelHeader.="<th style='width:15%;'>".__('Address')."</th>";
				$excelHeader.="<th>".__('Ruc')."</th>";
				$excelHeader.="<th>".__('Phone')."</th>";
				$excelHeader.="<th>".__('Cell')."</th>";
        $excelHeader.=($historyDisplayOptionId?"<th style='width:15%;'>Historial de Asignaciones</th>":"");
				$excelHeader.="<th># Cotizaciones</th>";
				$excelHeader.="<th># Facturas</th>";
				$excelHeader.="<th>$ Cotizaciones</th>";
				$excelHeader.="<th>$ Facturas</th>";
			$excelHeader.="</tr>";
    $excelHeader.="</thead>";  
    
    $tableBody="";
    $excelBody="";
    //20171213 note that technically this is not needed as we could take the total values for the user straight away, 
    //20171213 but this is a very helpful check to verify that the numbers add up
    $totalQuotationQuantity=0;
    $totalInvoiceQuantity=0;
    $totalQuotationAmount=0;
    $totalInvoiceAmount=0;
    
		foreach ($uniqueClients as $client){
      if ($unassociatedDisplayOptionId||$client['ClientUser'][0]['bool_assigned']){
        //if (count($client['Invoice'])>0){
        //  pr($client['Invoice']);
        //}
        $totalQuotationQuantity+=count($client['Quotation']);
        $totalInvoiceQuantity+=count($client['Invoice']);
        $totalQuotationAmount+=$client['Client']['quotation_total'];
        $totalInvoiceAmount+=$client['Client']['invoice_total'];
        
        $tableRow="";
        $tableRow.="<td>".$this->Html->link($client['Client']['name'].($client['Client']['bool_active']?"":" (Desactivado)"), array('controller' => 'clients', 'action' => 'view', $client['Client']['id']))."</td>";
        $tableRow.="<td>".$client['Client']['address']."</td>";
        $tableRow.="<td>".$client['Client']['ruc']."</td>";
        $tableRow.="<td>".$client['Client']['phone']."</td>";
        $tableRow.="<td>".$client['Client']['cell']."</td>";
        if ($historyDisplayOptionId){
          $tableRow.="<td>";
          foreach ($client['ClientUser'] as $clientUser){
            $assignmentDateTime=new DateTime($clientUser['assignment_datetime']);
            $tableRow.=($clientUser['bool_assigned']?"Asignado":"Desasignado")." el ".($assignmentDateTime->format('d-m-Y H:i:s'))."<br>";
          }  
          $tableRow.="</td>";
        }
        $tableRow.="<td>".count($client['Quotation'])."</td>";
        $tableRow.="<td>".count($client['Invoice'])."</td>";
        $tableRow.="<td class='".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$client['Client']['quotation_total']."</span></td>";
        $tableRow.="<td class='".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$client['Client']['invoice_total']."</span></td>";
        
        $excelBody.=($client['ClientUser'][0]['bool_assigned']?"<tr>":"<tr class='italic'>").$tableRow."</tr>";
        
        $tableRow.="<td class='actions'>".($bool_client_edit_permission?$this->Html->link(__('Edit'), array('controller' => 'clients', 'action' => 'edit', $client['Client']['id'])):"")."</td>";
        
        $tableBody.=($client['ClientUser'][0]['bool_assigned']?"<tr>":"<tr class='italic'>").$tableRow."</tr>";
      }
		}
    $totalRow=$excelTotalRow="";
    $excelTotalRow.="<td>".($currencyId==CURRENCY_CS?"Total C$":"Total USD")."</td>";
    $excelTotalRow.="<td></td>";
    $excelTotalRow.="<td></td>";
    $excelTotalRow.="<td></td>";
    $excelTotalRow.="<td></td>";
    $excelTotalRow.=($historyDisplayOptionId?"<td></td>":"");;
    $excelTotalRow.="<td>".$totalQuotationQuantity."</td>";	    
    $excelTotalRow.="<td>".$totalInvoiceQuantity."</td>";	    	
    $excelTotalRow.="<td class='".($currencyId==CURRENCY_CS?"CScurrency":"USDcurrency")."'><span class='currency'></span><span class='amountright'>".$totalQuotationAmount."</span></td>";	
    $excelTotalRow.="<td class='".($currencyId==CURRENCY_CS?"CScurrency":"USDcurrency")."'><span class='currency'></span><span class='amountright'>".$totalInvoiceAmount."</span></td>";	
    
    $totalRow=$excelTotalRow;    
    $excelTotalRow="<tr class='totalrow'>".$excelTotalRow."</tr>";
    $totalRow.="<td></td>";	
    $totalRow="<tr class='totalrow'>".$totalRow."</tr>";
    
		$table= "<table id='clientes_asignados' cellpadding = '0' cellspacing = '0'>".$tableHeader.$totalRow.$tableBody.$totalRow."</table>";
    echo "<h3>".__('Clientes asociados con este Usuario')."</h3>";
    echo $table;
    
    $excelOutput.="<table id='clientes_asignados' cellpadding = '0' cellspacing = '0'>".$excelHeader.$excelTotalRow.$excelBody.$excelTotalRow."</table>";
	}
?>
</div>
<div class="related">
<?php 
	//pr($user['Client']);
	if(!empty($user['Client'])){
    $tableHeader="";
    $tableHeader.="<thead>";
			$tableHeader.="<tr>";
				$tableHeader.="<th>".__('Name')."</th>";
				$tableHeader.="<th>".__('Address')."</th>";
				$tableHeader.="<th>".__('Ruc')."</th>";
				$tableHeader.="<th>".__('Phone')."</th>";
				$tableHeader.="<th>".__('Cell')."</th>";
				$tableHeader.="<th class='actions'>".__('Actions')."</th>";
			$tableHeader.="</tr>";
    $tableHeader.="</thead>";
    $tableHeader="";
    $excelHeader.="<thead>";
			$excelHeader.="<tr>";
				$excelHeader.="<th>".__('Name')."</th>";
				$excelHeader.="<th>".__('Address')."</th>";
				$excelHeader.="<th>".__('Ruc')."</th>";
				$excelHeader.="<th>".__('Phone')."</th>";
				$excelHeader.="<th>".__('Cell')."</th>";
			$tableHeader.="</tr>";
    $tableHeader.="</thead>";
    $tableBody=$excelBody="";
		foreach ($user['Client'] as $client){
      $tableRow="";
      $tableRow.="<td>".$this->Html->link($client['name'], array('controller' => 'clients', 'action' => 'view', $client['id']))."</td>";
      $tableRow.="<td>".$client['address']."</td>";
      $tableRow.="<td>".$client['ruc']."</td>";
      $tableRow.="<td>".$client['phone']."</td>";
      $tableRow.="<td>".$client['cell']."</td>";
      $excelBody.="<tr>".$tableRow."</tr>";
		  $tableRow.="<td class='actions'>".($bool_client_edit_permission?$this->Html->link(__('Edit'), array('controller' => 'clients', 'action' => 'edit', $client['id'])):"")."</td>";
			$tableBody="<tr>".$tableRow."</tr>";
		}
    
    echo "<h3>".__('Clientes CREADOS por este Usuario')."</h3>";
		echo "<table id='clientes_creados' cellpadding = '0' cellspacing = '0'>".$tableHeader.$tableBody."</table>";
    
    $excelOutput.="<table id='clientes_creados' cellpadding = '0' cellspacing = '0'>".$excelHeader.$excelBody."</table>";
	}
?>
</div>
<div class="related">
<?php 
	if (!empty($user['UserLog'])){
		$tableHeader="";
    $tableHeader.="<thead>";
      $tableHeader.="<tr>";
        $tableHeader.="<th>Fecha</th>";
        $tableHeader.="<th>Evento</th>";
      $tableHeader.="</tr>";
    $tableHeader.="</thead>";

    $tableBody="";  
    $tableBody.="<tbody>";
    foreach ($user['UserLog'] as $userLog){
      $createdDateTime=new DateTime($userLog['created']);
      $tableBody.="<tr>";
        $tableBody.="<td>".$createdDateTime->format('d-m-Y H:i:s')."</td>";
        $tableBody.="<td>".$userLog['event']."</td>";
      $tableBody.="</tr>";
    }
    $tableBody.="</tbody>";

		echo "<h3>Evento de Acceso</h3>";
		echo "<table id='acceso' cellpadding = '0' cellspacing = '0'>".$tableHeader.$tableBody."</table>";
    
    $excelOutput.="<table id='acceso' cellpadding = '0' cellspacing = '0'>".$tableHeader.$tableBody."</table>";
	}
?>
</div>
<?php 
  $_SESSION['tablasDeVistaUsuario'] = $excelOutput;