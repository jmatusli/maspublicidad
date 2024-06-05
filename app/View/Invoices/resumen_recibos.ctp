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
	
	function formatPercentages(){
		$("td.percentage span.amountright").each(function(){
			if (Math.abs(parseFloat($(this).text()))<0.001){
				$(this).text("0");
			}
			else {
				var percentageValue=parseFloat($(this).text());
				$(this).text(100*percentageValue);
			}
			$(this).number(true,2,'.',',');
			$(this).append(" %");
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
		formatPercentages();
	});
</script>
<div class="invoices resumen_recibos fullwidth">
<?php 
	echo "<h2>".__('Resumen Recibos')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015,'maxYear'=>date('Y')));
			echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015,'maxYear'=>date('Y')));
			echo "<br/>";
			if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
				echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Usuario'),'options'=>$users,'default'=>$userId,'empty'=>array('0'=>__('Todos Usuarios'))));
			}
			else {
				echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Usuario'),'options'=>$users,'default'=>$userId));
			}
			echo $this->Form->input('Report.currency_id',array('label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId));
		echo "</fieldset>";
		echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
		echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
		echo "<br/>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumenRecibos'), array( 'class' => 'btn btn-primary'));
	
	$startDateTime=new DateTime($startDate);
	$endDateTime=new DateTime($endDate);
?> 
</div>
<div>
<?php
	$excelOutput="";
	
	$pageHeader="";
	$excelHeader="";
	$pageHeader.="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('last_payment_date','Fecha de Pago')."</th>";
			$pageHeader.="<th># Recibo</th>";
			$pageHeader.="<th>".$this->Paginator->sort('invoice_date','Fecha de Factura')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('invoice_code','# Factura')."</th>";
			//$pageHeader.="<th># Días</th>";
			$pageHeader.="<th>".$this->Paginator->sort('client_id')."</th>";
			$pageHeader.="<th class='centered'>".$this->Paginator->sort('price_subtotal','Venta Bruta')."</th>";
			$pageHeader.="<th class='centered'>".$this->Paginator->sort('price_total')."</th>";
			$pageHeader.="<th class='centered'>Vendedor</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader.="<thead>";
		$excelHeader.="<tr><th colspan='13' align='center'>".COMPANY_NAME."</th></tr>";	
		$excelHeader.="<tr><th colspan='13' align='center'>".__('Cuentas por cobrar')."</th></tr>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>Fecha de Pago</th>";
			$excelHeader.="<th># Recibo</th>";
			$excelHeader.="<th>Fecha de Facctura</th>";
			$excelHeader.="<th>Factura</th>";
			//$excelHeader.="<th># Días</th>";
			$excelHeader.="<th>Cliente</th>";
			$excelHeader.="<th class='centered'>Subtotal</th>";
			$excelHeader.="<th>Total</th>";
			$excelHeader.="<th class='centered'>Vendedor</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";
	
	$subtotalCS=0;
	$totalCS=0;
	$subtotalUSD=0;
	$totalUSD=0;
	
	$inv=0;
	foreach ($receipts as $receipt){ 
		$receiptDateTime=new DateTime($receipt['Invoice']['last_payment_date']);
		$invoiceDateTime=new DateTime($receipt['Invoice']['invoice_date']);
		
		$daysLate=$receiptDateTime->diff($invoiceDateTime);
		$numberLateDays=$daysLate->days;
		
		if ($receipt['Currency']['id']==CURRENCY_CS){
			$currencyClass=" class='CScurrency'";
			$subtotalCS+=$receipt['Invoice']['price_subtotal'];
			$totalCS+=$receipt['Invoice']['price_total'];
			
			//added calculation of totals in US$
			$subtotalUSD+=round($receipt['Invoice']['price_subtotal']/$receipt['Invoice']['exchange_rate'],2);
			$totalUSD+=round($receipt['Invoice']['price_total']/$receipt['Invoice']['exchange_rate'],2);
		}
		elseif ($receipt['Currency']['id']==CURRENCY_USD){
			$currencyClass=" class='USDcurrency'";
			$subtotalUSD+=$receipt['Invoice']['price_subtotal'];
			$totalUSD+=$receipt['Invoice']['price_total'];
			
			//added calculation of totals in CS$
			$subtotalCS+=round($receipt['Invoice']['price_subtotal']*$receipt['Invoice']['exchange_rate'],2);
			$totalCS+=round(($receipt['Invoice']['price_total'])*$receipt['Invoice']['exchange_rate'],2);
		}
		
		$pageRow="";
			$pageRow.="<td>".$receiptDateTime->format('d-m-Y')."</td>";
			$pageRow.="<td>".$receipt['Invoice']['cash_receipt_code']."</td>";
			$pageRow.="<td>".$invoiceDateTime->format('d-m-Y')."</td>";
			$pageRow.="<td>".$this->Html->link($receipt['Invoice']['invoice_code'].($receipt['Invoice']['bool_annulled']?" (Anulada)":""),array('action'=>'detalle',$receipt['Invoice']['id']))."</td>";
			//$pageRow.="<td>".$numberLateDays."</td>";
			$pageRow.="<td>".$this->Html->link($receipt['Client']['name'], array('controller' => 'clients', 'action' => 'view', $receipt['Client']['id']))."</td>";
			
			$pageRow.="<td".$currencyClass."><span class='currency'></span><span class='amountright'>".h($receipt['Invoice']['price_subtotal'])."</span></td>";
			$pageRow.="<td".$currencyClass."><span class='currency'></span><span class='amountright'>".h($receipt['Invoice']['price_total'])."</span></td>";
			$pageRow.="<td>".$this->Html->Link($receipt['InvoiceSalesOrder'][0]['User']['username'],array('controller'=>'users','action'=>'view',$receipt['InvoiceSalesOrder'][0]['User']['id']))."</td>";

		$excelBody.="<tr>".$pageRow."</tr>";
		$pageBody.="<tr>".$pageRow."</tr>";
		
		$inv++;
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
			$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$subtotalUSD."</td>";
			$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$totalUSD."</td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			
		$pageTotalRow.="</tr>";
	}
	$excelTotalRow="";
	if ($subtotalCS>0){
		$excelTotalRow.="<tr class='totalrow'>";
			$excelTotalRow.="<td>Total C$</td>";
			$excelTotalRow.="<td></td>";
			$excelTotalRow.="<td></td>";
			$excelTotalRow.="<td></td>";
			$excelTotalRow.="<td></td>";
			$excelTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$subtotalCS."</span></td>";
			$excelTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$totalCS."</span></td>";
			$excelTotalRow.="<td></td>";
		$excelTotalRow.="</tr>";
	}
	if ($subtotalUSD>0){
		$excelTotalRow.="<tr class='totalrow'>";
			$excelTotalRow.="<td>Total US$</td>";
			$excelTotalRow.="<td></td>";
			$excelTotalRow.="<td></td>";
			$excelTotalRow.="<td></td>";
			$excelTotalRow.="<td></td>";
			$excelTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$subtotalUSD."</td>";
			$excelTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$totalUSD."</td>";
			$excelTotalRow.="<td></td>";
			
		$excelTotalRow.="</tr>";
	}
	
	
	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	$excelBody="<tbody>".$excelTotalRow.$excelBody.$excelTotalRow."</tbody>";
	$table_id="resumen recibos";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo "<h3>Resumen Recibos de Caja</h3>";
	echo $pageOutput;
	$excelOutput.="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	
	$_SESSION['resumenRecibos'] = $excelOutput;
?>
</div>
