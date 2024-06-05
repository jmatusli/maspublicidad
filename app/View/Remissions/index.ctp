<div class="remissions index">
<?php 
	echo "<h2>".__('Remissions')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate));
			echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate));
			echo $this->Form->input('Report.inventory_product_id',array('label'=>__('Product'),'dateFormat'=>'DMY','default'=>$inventoryProductId,'empty'=>array('0'=>'Todos')));
			echo $this->Form->input('Report.currency_id',array('label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId));			
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
			echo "<li>".$this->Html->link(__('New Remission'), array('action' => 'add'))."</li>";
			echo "<br/>";
		}
		if ($bool_client_index_permission){
			echo "<li>".$this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index'))."</li>";
		}
		if  ($bool_client_add_permission){
			echo "<li>".$this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
<div>
<?php
	$pageHeader="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('remission_date')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('remission_code')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('client_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('due_date')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('bool_iva')."</th>";
			$pageHeader.="<th class='centered'>".$this->Paginator->sort('price_subtotal')."</th>";
			$pageHeader.="<th class='centered'>".$this->Paginator->sort('price_iva')."</th>";
			$pageHeader.="<th class='centered'>".$this->Paginator->sort('price_total')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('bool_paid')."</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader="<thead>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('remission_date')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('remission_code')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('client_id')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('due_date')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('bool_iva')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('price_subtotal')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('price_iva')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('price_total')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('bool_paid')."</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	$subtotalCS=0;
	$ivaCS=0;
	$totalCS=0;
	$subtotalUSD=0;
	$ivaUSD=0;
	$totalUSD=0;
	
	foreach ($remissions as $remission){ 
		$remissionDateTime = new DateTime($remission['Remission']['remission_date']);
		$dueDateTime = new DateTime($remission['Remission']['due_date']);
		
		$pageRow="";
		$pageRow.="<td>".$remissionDateTime->format('d-m-Y')."</td>";
		$pageRow.="<td>".$this->Html->link($remission['Remission']['remission_code'].($remission['Remission']['bool_annulled']?" (Anulada)":""),array('action'=>'view',$remission['Remission']['id']))."</td>";
		$pageRow.="<td>".$this->Html->link($remission['Client']['name'], array('controller' => 'clients', 'action' => 'view', $remission['Client']['id']))."</td>";
		$pageRow.="<td>".$dueDateTime->format('d-m-Y')."</td>";
		$pageRow.="<td>".($remission['Remission']['bool_iva']?__('Yes'):__('No'))."</td>";
		if ($currencyId==CURRENCY_USD){
			if ($remission['Currency']['id']==CURRENCY_USD){
				$pageRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".h($remission['Remission']['price_subtotal'])."</span></td>";
				$pageRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".h($remission['Remission']['price_iva'])."</span></td>";
				$pageRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".h($remission['Remission']['price_total'])."</span></td>";
			
				$subtotalUSD+=$remission['Remission']['price_subtotal'];
				$ivaUSD+=$remission['Remission']['price_iva'];
				$totalUSD+=$remission['Remission']['price_total'];
			}
			else {
				$pageRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".round($remission['Remission']['price_subtotal']/$remission['Remission']['exchange_rate'],2)."</span></td>";
				$pageRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".round($remission['Remission']['price_iva']/$remission['Remission']['exchange_rate'],2)."</span></td>";
				$pageRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".round($remission['Remission']['price_total']/$remission['Remission']['exchange_rate'],2)."</span></td>";
			
				$subtotalUSD+=round($remission['Remission']['price_subtotal']/$remission['Remission']['exchange_rate'],2);
				$ivaUSD+=round($remission['Remission']['price_iva']/$remission['Remission']['exchange_rate'],2);
				$totalUSD+=round($remission['Remission']['price_total']/$remission['Remission']['exchange_rate'],2);
			}
		}
		else {
			if ($remission['Currency']['id']==CURRENCY_USD){
				$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".round($remission['Remission']['price_subtotal']*$remission['Remission']['exchange_rate'],2)."</span></td>";
				$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".round($remission['Remission']['price_iva']*$remission['Remission']['exchange_rate'],2)."</span></td>";
				$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".round($remission['Remission']['price_total']*$remission['Remission']['exchange_rate'],2)."</span></td>";
			
				$subtotalUSD+=round($remission['Remission']['price_subtotal']*$remission['Remission']['exchange_rate'],2);
				$ivaUSD+=round($remission['Remission']['price_iva']*$remission['Remission']['exchange_rate'],2);
				$totalUSD+=round($remission['Remission']['price_total']*$remission['Remission']['exchange_rate'],2);
			}
			else {
				$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".h($remission['Remission']['price_subtotal'])."</span></td>";
				$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".h($remission['Remission']['price_iva'])."</span></td>";
				$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".h($remission['Remission']['price_total'])."</span></td>";
			
				$subtotalCS+=$remission['Remission']['price_subtotal'];
				$ivaCS+=$remission['Remission']['price_iva'];
				$totalCS+=$remission['Remission']['price_total'];
			}
		}
			
		$pageRow.="<td>".h($remission['Remission']['bool_paid'])."</td>";
		
		
		$excelBody.="<tr>".$pageRow."</tr>";

		$pageRow.="<td class='actions'>";
			$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $remission['Remission']['id']));
			if ($bool_edit_permission){
				$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $remission['Remission']['id']));
			}
			if ($bool_delete_permission){
				$pageRow.=$this->Form->postLink(__('Delete'), array('action' => 'delete', $remission['Remission']['id']), array(), __('Está seguro que quiere eliminar remisión # %s?', $remission['Remission']['remission_code']));
			}
		$pageRow.="</td>";

		if ($remission['Remission']['bool_annulled']){
			$pageBody.="<tr class='italic'>".$pageRow."</tr>";
		}
		else {
			$pageBody.="<tr>".$pageRow."</tr>";
		}
	}
	
	$pageTotalRow="";
	if ($currencyId==CURRENCY_CS){
		$pageTotalRow.="<tr class='totalrow'>";
			$pageTotalRow.="<td>Total C$</td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$subtotalCS."</span></td>";
			$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$ivaCS."</span></td>";
			$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$totalCS."</span></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
		$pageTotalRow.="</tr>";
	}
	if ($currencyId==CURRENCY_USD){
		$pageTotalRow.="<tr class='totalrow'>";
			$pageTotalRow.="<td>Total US$</td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$subtotalUSD."</span></td>";
			$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$ivaUSD."</span></td>";
			$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$totalUSD."</span></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
		$pageTotalRow.="</tr>";
	}

	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	$table_id="";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo $pageOutput;
	$excelOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	$_SESSION['resumen'] = $excelOutput;
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