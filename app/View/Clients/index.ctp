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
<div class="clients index">
<?php 
	echo "<h1>".__('Clients')."</h1>";	
	echo $this->Form->create('Report'); 
		echo "<fieldset>"; 
		echo "<div class='container-fluid'>";
			echo "<div class='rows'>";
				echo "<div class='col-sm-6'>";	
          if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) { 
            echo $this->Form->input('Report.user_id',['label'=>'Mostrar Usuario','options'=>$users,'default'=>$userId,'empty'=>['0'=>'Todos Usuarios']]);
          }
          else {
            echo $this->Form->input('Report.user_id',['label'=>'Mostrar Usuario','options'=>$users,'default'=>$userId,'type'=>'hidden']);
          }												
					echo $this->Form->input('Report.sales_display_option_id',['label'=>'Clientes con Ventas','default'=>$salesDisplayOptionId]);
        if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) { 
          echo $this->Form->input('Report.aggregate_option_id',array('label'=>__('Mostrar y Ordenar Por'),'default'=>$aggregateOptionId));
        }
        else {
          echo $this->Form->input('Report.aggregate_option_id',array('label'=>__('Mostrar y Ordenar Por'),'default'=>AGGREGATES_NONE,'type'=>'hidden'));
        }
          echo $this->Form->input('Report.active_display_option_id',array('label'=>__('Clientes Activos'),'default'=>$activeDisplayOptionId));
					echo $this->Form->input('Report.vip_display_option_id',array('label'=>__('Clientes Vip'),'default'=>$vipDisplayOptionId));
					echo $this->Form->input('Report.searchterm',array('label'=>__('Buscar')));
				echo "</div>";
				echo "<div class='col-sm-6'>";
          if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) {
            echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015,'maxYear'=>date('Y')));
            echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015,'maxYear'=>date('Y')));
            echo $this->Form->input('Report.currency_id',array('label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId));
          }
				echo "</div>";
			echo "</div>";
		echo "</div>";
		echo  "</fieldset>";
	echo $this->Form->end(__('Refresh')); 
	echo "<br>";
	echo $this->Html->link(__('Guardar como Excel'), ['action' => 'guardarResumen'], ['class' => 'btn btn-primary']);
?> 
</div>
<div class='actions'>
<?php 
	echo "<h2>".__('Actions')."</h2>";
	echo "<ul>";
		if ($bool_add_permission){
			echo "<li>".$this->Html->link(__('New Client'), ['action' => 'add'])."</li>";
		}
		echo "<br/>";
		if ($bool_contact_index_permission){
			echo "<li>".$this->Html->link(__('List Contacts'), ['controller' => 'contacts', 'action' => 'index'])."</li>";
		}
		if ($bool_contact_add_permission){
			echo "<li>".$this->Html->link(__('New Contact'), ['controller' => 'contacts', 'action' => 'add'])."</li>";
		}
		if ($bool_invoice_index_permission){
			echo "<li>".$this->Html->link(__('List Invoices'), ['controller' => 'invoices', 'action' => 'index'])."</li>";
		}
		if ($bool_invoice_add_permission){
			echo "<li>".$this->Html->link(__('New Invoice'), ['controller' => 'invoices', 'action' => 'add'])."</li>";
		}
		if ($bool_quotation_index_permission){
			echo "<li>".$this->Html->link(__('List Quotations'), ['controller' => 'quotations', 'action' => 'index'])."</li>";
		}
		if ($bool_quotation_add_permission){
			echo "<li>".$this->Html->link(__('New Quotation'), ['controller' => 'quotations', 'action' => 'add'])."</li>";
		}
	echo "</ul>";
?>
</div>
<div class='clearleft fullwidth' style='float:left;'>
<?php
	$pageHeader="";
	$excelHeader="";
	$pageHeader.="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('name')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('ruc')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('address')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('phone')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('cell')."</th>";
			//$pageHeader.="<th>".$this->Paginator->sort('bool_vip')."</th>";
			if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) { 
        $pageHeader.="<th># Cotizaciones</th>";
        $pageHeader.="<th># Facturas</th>";
        $pageHeader.="<th>$ Cotizaciones</th>";
        $pageHeader.="<th>$ Facturas</th>";
			}
			//$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader.="<thead>";
		$excelHeader.="<tr><th colspan='7' align='center'>".COMPANY_NAME."</th></tr>";	
		$excelHeader.="<tr><th colspan='7' align='center'>".__('Resumen de Clientes').($vipDisplayOptionId==SHOW_CLIENT_VIP_YES?" VIP":"").($activeDisplayOptionId==SHOW_CLIENT_ACTIVE_YES?" Activos":($activeDisplayOptionId==SHOW_CLIENT_ACTIVE_YES?" Desactivados":""))."</th></tr>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('name')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('ruc')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('address')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('phone')."</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('bool_vip')."</th>";
			if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) {
        $excelHeader.="<th># Cotizaciones</th>";
        $excelHeader.="<th># Facturas</th>";
        $excelHeader.="<th>$ Cotizaciones</th>";
        $excelHeader.="<th>$ Facturas</th>";
      }
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	foreach ($clients['Registered'] as $client){
    if (!empty($client['Quotation']) || !empty($client['Invoice']) || in_array($salesDisplayOptionId,[SHOW_CLIENT_SALES_OR_NOT,SHOW_CLIENT_SALES_NO])){
		//pr($client);
      $pageRow="";
        $pageRow.="<td>".$this->Html->link($client['Client']['name'].($client['Client']['bool_active']?"":" (Inactivo)"),array('action'=>'view',$client['Client']['id']))."</td>";
        $pageRow.="<td>".h($client['Client']['ruc'])."</td>";
        $pageRow.="<td>".h($client['Client']['address'])."</td>";
        $pageRow.="<td>".h($client['Client']['phone'])."</td>";
        $pageRow.="<td>".h($client['Client']['cell'])."</td>";
        //$pageRow.="<td>".($client['Client']['bool_vip']?__('Yes'):__('No'))."</td>";
        if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) {
          $pageRow.="<td>".count($client['Quotation'])."</td>";
          $pageRow.="<td>".count($client['Invoice'])."</td>";
          $pageRow.="<td class='".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$client['Client']['quotation_total']."</span></td>";
          $pageRow.="<td class='".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$client['Client']['invoice_total']."</span></td>";
        }
        $excelBody.=($client['Client']['bool_active']?"<tr>":"<tr class='italic'>").$pageRow."</tr>";
      $pageBody.=($client['Client']['bool_active']?"<tr>":"<tr class='italic'>").$pageRow."</tr>";
    }  
	}

	$pageBody="<tbody>".$pageBody."</tbody>";
  $excelBody="<tbody>".$excelBody."</tbody>";
	
	$table_id="Clientes registrados";
	$pageOutput="<table id='".$table_id."'>".$pageHeader.$pageBody."</table>";
  echo '<h2>Clientes registrados</h2>';
	echo "<p class='comment clearleft fullwidth'>Los clientes están ordenados según el total de ventas por cliente en orden descendiente y no se pueden reordenar</p>";
	echo $pageOutput;
	
	$excelOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
  
  
  $genericBody="";
	
	foreach ($clients['Generic'] as $client){
    foreach ($client['Client']['generic_client_data'] as $genericClientName=>$genericClientData){
      //pr($client);
      $genericRow="";
      $genericRow.="<td>".$genericClientName."</td>";
      $genericRow.="<td>".h($genericClientData['ruc'])."</td>";
      $genericRow.="<td>".h($genericClientData['address'])."</td>";
      $genericRow.="<td>".h($genericClientData['phone'])."</td>";
      $genericRow.="<td>".h($genericClientData['cell'])."</td>";
      if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) {
        $genericRow.="<td>".count($client['Quotation'])."</td>";
        $genericRow.="<td>".$genericClientData['invoice_quantity']."</td>";
        $genericRow.="<td class='".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$client['Client']['quotation_total']."</span></td>";
        $genericRow.="<td class='".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$genericClientData['invoice_total']."</span></td>";
      }
      $genericBody.=($client['Client']['bool_active']?"<tr>":"<tr class='italic'>").$genericRow."</tr>";
    }
    
	}

	$pageBody="<tbody>".$genericBody."</tbody>";
  $excelBody="<tbody>".$genericBody."</tbody>";
	
	$table_id="Clientes genéricos";
	$genericTable="<table id='".$table_id."'>".$pageHeader.$genericBody."</table>";
  echo '<h2>Clientes genéricos</h2>';
	echo "<p class='comment clearleft fullwidth'>Los clientes están ordenados según el total de ventas por cliente en orden descendiente y no se pueden reordenar</p>";
	echo $genericTable;
	
	$excelOutput.=$genericTable;
  
	$_SESSION['resumenClientes'] = $excelOutput;
?>
</div>