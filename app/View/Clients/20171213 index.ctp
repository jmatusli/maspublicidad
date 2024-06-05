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
	echo "<h2>".__('Clients')."</h2>";	
	echo $this->Form->create('Report'); 
		echo "<fieldset>"; 
		echo "<div class='container-fluid'>";
			echo "<div class='rows'>";
				echo "<div class='col-md-6'>";	
				if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
					echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Cliente asociado con Usuario'),'options'=>$users,'default'=>$userId,'empty'=>array('0'=>__('Todos Usuarios'))));
				}
				else {
					echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Cliente asociado con Usuario'),'options'=>$users,'default'=>$userId,'type'=>'hidden'));
				}												
					echo $this->Form->input('Report.active_display_option_id',array('label'=>__('Clientes Activos'),'default'=>$activeDisplayOptionId));
					echo $this->Form->input('Report.vip_display_option_id',array('label'=>__('Clientes Vip'),'default'=>$vipDisplayOptionId));
        if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
          echo $this->Form->input('Report.aggregate_option_id',array('label'=>__('Mostrar y Ordenar Por'),'default'=>$aggregateOptionId));
        }
        else {
          echo $this->Form->input('Report.aggregate_option_id',array('label'=>__('Mostrar y Ordenar Por'),'default'=>AGGREGATES_NONE,'type'=>'hidden'));
        }
					echo $this->Form->input('Report.searchterm',array('label'=>__('Buscar')));
				echo "</div>";
				echo "<div class='col-md-6'>";
          if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) {   
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
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumen'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_add_permission){
			echo "<li>".$this->Html->link(__('New Client'), array('action' => 'add'))."</li>";
		}
		echo "<br/>";
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
			if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
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
			if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
        $excelHeader.="<th># Cotizaciones</th>";
        $excelHeader.="<th># Facturas</th>";
        $excelHeader.="<th>$ Cotizaciones</th>";
        $excelHeader.="<th>$ Facturas</th>";
      }
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	foreach ($clients as $client){ 
		//pr($client);
		$pageRow="";
			$pageRow.="<td>".$this->Html->link($client['Client']['name'].($client['Client']['bool_active']?"":" (Inactivo)"),array('action'=>'view',$client['Client']['id']))."</td>";
			$pageRow.="<td>".h($client['Client']['ruc'])."</td>";
			$pageRow.="<td>".h($client['Client']['address'])."</td>";
			$pageRow.="<td>".h($client['Client']['phone'])."</td>";
			$pageRow.="<td>".h($client['Client']['cell'])."</td>";
			//$pageRow.="<td>".($client['Client']['bool_vip']?__('Yes'):__('No'))."</td>";
      if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
        $pageRow.="<td>".count($client['Quotation'])."</td>";
        $pageRow.="<td>".count($client['Invoice'])."</td>";
        $pageRow.="<td class='".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$client['Client']['quotation_total']."</span></td>";
        $pageRow.="<td class='".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$client['Client']['invoice_total']."</span></td>";
			}
			$excelBody.=($client['Client']['bool_active']?"<tr>":"<tr class='italic'>").$pageRow."</tr>";
			//$pageRow.="<td class='actions'>";
			//	$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $client['Client']['id']));
			//	if ($bool_edit_permission){
			//		$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $client['Client']['id']));
			//	}
			//	//$pageRow.=->postLink(__('Delete'), array('action' => 'delete', $client['Client']['id']), array(), __('Are you sure you want to delete # %s?', $client['Client']['id']));
			//$pageRow.="</td>";

		$pageBody.=($client['Client']['bool_active']?"<tr>":"<tr class='italic'>").$pageRow."</tr>";
	}

	$pageBody="<tbody>".$pageBody."</tbody>";
  $excelBody="<tbody>".$excelBody."</tbody>";
	
	
	$table_id="Clientes";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo "<p class='comment clearleft fullwidth'>Los clientes están ordenados según el total de ventas por cliente en orden descendiente y no se pueden reordenar</p>";
	echo $pageOutput;
	
	//echo "<p>";
	//echo $this->Paginator->counter(array(
	//	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	//));
	//echo "</p>";
	//echo "<div class='paging'>";
	//	echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
	//	echo $this->Paginator->numbers(array('separator' => ''));
	//	echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	//echo "</div>";
	
	$excelOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	$_SESSION['resumen'] = $excelOutput;
?>
</div>