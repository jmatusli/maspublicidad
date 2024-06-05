<div class="entries index">
<?php 
	echo "<h2>".__('Entries')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate));
			echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate));
			echo $this->Form->input('Report.inventory_product_id',array('label'=>__('Product'),'dateFormat'=>'DMY','default'=>$inventory_product_id,'empty'=>array('0'=>'Todos')));
			echo $this->Form->input('Report.currency_id',array('label'=>__('Visualizar en moneda'),'default'=>$currency_id));
		echo "</fieldset>";
		echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
		echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumen'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_add_permission){
			echo "<li>".$this->Html->link(__('New Entry'), array('action' => 'add'))."</li>";
		}
		if ($bool_inventoryprovider_index_permission){
			echo "<br/>";
			echo "<li>".$this->Html->link(__('List Inventory Providers'), array('controller' => 'inventory_providers', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryprovider_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Provider'), array('controller' => 'inventory_providers', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
<div>
<?php
	$pageHeader="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('entry_date')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('entry_code')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('inventory_provider_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('due_date')."</th>";
			//$pageHeader.="<th class='centered'>".$this->Paginator->sort('bool_iva')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('cost_subtotal')."</th>";
			//$pageHeader.="<th>".$this->Paginator->sort('cost_iva')."</th>";
			//$pageHeader.="<th>".$this->Paginator->sort('cost_total')."</th>";
			$pageHeader.="<th class='centered'>".$this->Paginator->sort('bool_paid')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('observation')."</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader="<thead>";
		$excelHeader.="<tr>";
			$startDateTime=new DateTime($startDate);
			$endDateTime=new DateTime($endDate);
			$excelHeader.="<tr><th colspan='11' align='center'>".COMPANY_NAME."</th></tr>";
			$excelHeader.="<tr><th colspan='11' align='center'>".__('Reporte Entradas')." en ".($currency_id==CURRENCY_USD?"US$":"C$")." (".date('d-m-Y').")</th></tr>";
			$excelHeader.="<tr><th colspan='11' align='center'>Entradas desde ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</th></tr>";
			$excelHeader.="<th>".$this->Paginator->sort('entry_date')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('entry_code')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('inventory_provider_id')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('due_date')."</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('bool_iva')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('cost_subtotal')."</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('cost_iva')."</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('cost_total')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('bool_paid')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('observation')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('currency_id')."</th>";			
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	$totalSubTotal=0;
	$totalIva=0;
	$totalTotal=0;
	foreach ($entries as $entry){ 
		//pr($entry);
		$entryDateTime=new DateTime($entry['Entry']['entry_date']);
		$dueDateTime=new DateTime($entry['Entry']['due_date']);
		
		$boolAnnulled=$entry['Entry']['bool_annulled'];
		
		$pageRow="";
		
		$pageRow.="<td>".$entryDateTime->format('d-m-Y')."</td>";
		$pageRow.="<td>".h($entry['Entry']['entry_code']).($boolAnnulled?__(" (Anulada)"):"")."</td>";		
		$pageRow.="<td>".$this->Html->link($entry['InventoryProvider']['name'], array('controller' => 'inventory_providers', 'action' => 'view', $entry['InventoryProvider']['id']))."</td>";
		$pageRow.="<td>".$dueDateTime->format('d-m-Y')."</td>";
		
		//$pageRow.="<td class='centered'>".($entry['Entry']['bool_iva']?__("Yes"):__("No"))."</td>";
		if ($currency_id==CURRENCY_USD){
			if ($entry['Currency']['id']==CURRENCY_USD){
				$pageRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$entry['Entry']['cost_subtotal']."</span></td>";
				//$pageRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$entry['Entry']['cost_iva']."</span></td>";
				//$pageRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$entry['Entry']['cost_total']."</span></td>";
				
				$totalSubTotal+=$entry['Entry']['cost_subtotal'];
				$totalIva+=$entry['Entry']['cost_iva'];
				$totalTotal+=$entry['Entry']['cost_total'];
			}
			else {
				$pageRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".round($entry['Entry']['cost_subtotal']/$entry['Entry']['exchange_rate'],2)."</span></td>";
				//$pageRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".round($entry['Entry']['cost_iva']/$entry['Entry']['exchange_rate'],2)."</span></td>";
				//$pageRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".round($entry['Entry']['cost_total']/$entry['Entry']['exchange_rate'],2)."</span></td>";
				
				$totalSubTotal+=round($entry['Entry']['cost_subtotal']/$entry['Entry']['exchange_rate'],2);
				//$totalIva+=round($entry['Entry']['cost_iva']/$entry['Entry']['exchange_rate'],2);
				//$totalTotal+=round($entry['Entry']['cost_total']/$entry['Entry']['exchange_rate'],2);
			}
			
		}
		else {
			if ($entry['Currency']['id']==CURRENCY_USD){
				$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".round($entry['Entry']['cost_subtotal']*$entry['Entry']['exchange_rate'],2)."</span></td>";
				//$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".round($entry['Entry']['cost_iva']*$entry['Entry']['exchange_rate'],2)."</span></td>";
				//$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".round($entry['Entry']['cost_total']*$entry['Entry']['exchange_rate'],2)."</span></td>";
				
				$totalSubTotal+=round($entry['Entry']['cost_subtotal']*$entry['Entry']['exchange_rate'],2);
				//$totalIva+=round($entry['Entry']['cost_iva']*$entry['Entry']['exchange_rate'],2);
				//$totalTotal+=round($entry['Entry']['cost_total']*$entry['Entry']['exchange_rate'],2);
			}
			else {
				$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$entry['Entry']['cost_subtotal']."</span></td>";
				//$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$entry['Entry']['cost_iva']."</span></td>";
				//$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$entry['Entry']['cost_total']."</span></td>";
				
				$totalSubTotal+=$entry['Entry']['cost_subtotal'];
				//$totalIva+=$entry['Entry']['cost_iva'];
				//$totalTotal+=$entry['Entry']['cost_total'];
			}
		}
		$pageRow.="<td class='centered'>".($entry['Entry']['bool_paid']?__("Yes"):__("No"))."</td>";
		$pageRow.="<td>".h($entry['Entry']['observation'])."</td>";
		if ($boolAnnulled){
			$currencyColumn="<td>".(($currency_id==CURRENCY_USD)?"US$":"C$")."</td>";
			$excelBody.="<tr class='italic'>".$pageRow.$currencyColumn."</tr>";
		}
		else {
			$currencyColumn="<td>".(($currency_id==CURRENCY_USD)?"US$":"C$")."</td>";
			$excelBody.="<tr>".$pageRow.$currencyColumn."</tr>";
		}

		$pageRow.="<td class='actions'>";
			$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $entry['Entry']['id']));
			if ($entry['Entry']['bool_editable']){
				$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $entry['Entry']['id']));
				$pageRow.=$this->Form->postLink(__('Delete'), array('action' => 'delete', $entry['Entry']['id']), array(), __('Está seguro que quiere eliminar la entrada # %s?', $entry['Entry']['id']));
			}
			
		$pageRow.="</td>";

		if ($boolAnnulled){
			$pageBody.="<tr class='italic'>".$pageRow."</tr>";
		}
		else {
			$pageBody.="<tr>".$pageRow."</tr>";
		}
	}

	$pageTotalRow="";
	$pageTotalRow.="<tr class='totalrow'>";
		$pageTotalRow.="<td>Totales</td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		//$pageTotalRow.="<td></td>";
		if ($currency_id==CURRENCY_USD){
			$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$totalSubTotal."</span></td>";
			//$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$totalIva."</span></td>";
			//$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$totalTotal."</span></td>";
		}
		else {
			$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$totalSubTotal."</span></td>";
			//$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$totalIva."</span></td>";
			//$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$totalTotal."</span></td>";
		}
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
	$pageTotalRow.="</tr>";

	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	$table_id="entries";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo $pageOutput;
	$excelOutput="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	//echo $excelOutput;
	$_SESSION['resumenEntradas'] = $excelOutput;
?>
</div>
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