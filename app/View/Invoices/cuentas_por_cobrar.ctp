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
<div class="invoices cuentas_por_cobrar fullwidth">
<?php 
	echo "<h2>".__('Cuentas por Cobrar')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			//echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015,'maxYear'=>date('Y')));
			//echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015,'maxYear'=>date('Y')));
			//echo "<br/>";
			//if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
			//	echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Usuario'),'options'=>$users,'default'=>$user_id,'empty'=>array('0'=>__('Todos Usuarios'))));
			//}
			//else {
			//	echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Usuario'),'options'=>$users,'default'=>$user_id));
			//}
			echo $this->Form->input('Report.currency_id',array('label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId));
		echo "</fieldset>";
		//echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
		//echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarCuentasPorCobrar'), array( 'class' => 'btn btn-primary'));
	
	//$startDateTime=new DateTime($startDate);
	//$endDateTime=new DateTime($endDate);
?> 
</div>
<div>
<?php
	echo $this->Form->create('Applicant'); 
	echo "<fieldset>";
		
		$excelOutput="";
		
		$pageHeader="";
		$excelHeader="";
		$pageHeader.="<thead>";
			$pageHeader.="<tr>";
				$pageHeader.="<th style='width:6%;'>".$this->Paginator->sort('invoice_date','Fecha')."</th>";
				$pageHeader.="<th style='width:5%;'># Días</th>";
				$pageHeader.="<th style='width:6%;'>".$this->Paginator->sort('client_id')."</th>";
				$pageHeader.="<th style='width:6%;'>".$this->Paginator->sort('invoice_code','# Factura')."</th>";
				//$pageHeader.="<th style='width:6%;'>".$this->Paginator->sort('sales_order_id','#OD Venta')."</th>";
				//$pageHeader.="<th class='centered' style='width:8%'>".$this->Paginator->sort('price_subtotal','Venta Bruta')."</th>";
				//$pageHeader.="<th class='centered' style='width:7%'>".$this->Paginator->sort('price_iva','Impuestos')."</th>";
				//$pageHeader.="<th class='centered'>".$this->Paginator->sort('amount_paid','Adelantos')."</th>";
				$pageHeader.="<th class='centered' style='width:8%'>".$this->Paginator->sort('price_total')."</th>";
				$pageHeader.="<th style='width:6%;'>Contact</th>";
				$pageHeader.="<th style='width:6%;'>Celular</th>";
				if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
					$pageHeader.="<th style='width:6%;'>".$this->Paginator->sort('user_id','Vendedor')."</th>";					
					$pageHeader.="<th class='hidden'>Invoice Id</th>";
					$pageHeader.="<th style='width:10%;'>RC Pago</th>";
					$pageHeader.="<th style='width:12%'>Fecha Último pago</th>";
					$pageHeader.="<th style='width:5%'></th>";
				}
			$pageHeader.="</tr>";
		$pageHeader.="</thead>";
		$excelHeader.="<thead>";
			$excelHeader.="<tr><th colspan='13' align='center'>".COMPANY_NAME."</th></tr>";	
			$excelHeader.="<tr><th colspan='13' align='center'>".__('Cuentas por cobrar')."</th></tr>";
			$excelHeader.="<tr>";
				$excelHeader.="<th>Fecha</th>";
				$excelHeader.="<th># Días</th>";
				$excelHeader.="<th>Cliente</th>";
				$excelHeader.="<th>Factura</th>";
				//$excelHeader.="<th>#OD Venta</th>";
				//$excelHeader.="<th class='centered'>Venta Bruta')</th>";
				//$excelHeader.="<th class='centered'>Impuestos')</th>";
				//$excelHeader.="<th class='centered'>Adelantos')</th>";
				$excelHeader.="<th class='centered'>Total</th>";
				$excelHeader.="<th>Contact</th>";
				$excelHeader.="<th>Celular</th>";
				if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
					$excelHeader.="<th>Vendedor</th>";
				}
			$excelHeader.="</tr>";
		$excelHeader.="</thead>";

		$pageBody="";
		$excelBody="";
		
		$subtotalCS=0;
		$ivaCS=0;
		//$paidCS=0;
		$totalCS=0;
		$subtotalUSD=0;
		$ivaUSD=0;
		//$paidUSD=0;
		$totalUSD=0;
		
		$inv=0;
		foreach ($invoices as $invoice){ 
			$invoiceDateTime=new DateTime($invoice['Invoice']['invoice_date']);
			$nowDateTime=new DateTime();
			$daysLate=$nowDateTime->diff($invoiceDateTime);
			//pr($daysLate);
			$numberLateDays=$daysLate->days;
			
			if ($invoice['Currency']['id']==CURRENCY_CS){
				$currencyClass=" class='CScurrency'";
				$subtotalCS+=$invoice['Invoice']['price_subtotal'];
				$ivaCS+=$invoice['Invoice']['price_iva'];
				//$paidCS+=$invoice['Invoice']['amount_paid'];
				//$totalCS+=($invoice['Invoice']['price_total']-$invoice['Invoice']['amount_paid']);
				$totalCS+=$invoice['Invoice']['price_total'];
				
				//added calculation of totals in US$
				$subtotalUSD+=round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2);
				$ivaUSD+=round($invoice['Invoice']['price_iva']/$invoice['Invoice']['exchange_rate'],2);
				//$paidUSD+=round($invoice['Invoice']['amount_paid']/$invoice['Invoice']['exchange_rate'],2);
				//$totalUSD+=round(($invoice['Invoice']['price_total']-$invoice['Invoice']['amount_paid'])/$invoice['Invoice']['exchange_rate'],2);
				$totalUSD+=round($invoice['Invoice']['price_total']/$invoice['Invoice']['exchange_rate'],2);
			}
			elseif ($invoice['Currency']['id']==CURRENCY_USD){
				$currencyClass=" class='USDcurrency'";
				$subtotalUSD+=$invoice['Invoice']['price_subtotal'];
				$ivaUSD+=$invoice['Invoice']['price_iva'];
				//$paidUSD+=$invoice['Invoice']['amount_paid'];
				//$totalUSD+=($invoice['Invoice']['price_total']-$invoice['Invoice']['amount_paid']);
				$totalUSD+=$invoice['Invoice']['price_total'];
				
				//added calculation of totals in CS$
				$subtotalCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2);
				$ivaCS+=round($invoice['Invoice']['price_iva']*$invoice['Invoice']['exchange_rate'],2);
				//$paidCS+=round($invoice['Invoice']['amount_paid']*$invoice['Invoice']['exchange_rate'],2);
				//$totalCS+=round(($invoice['Invoice']['price_total']-$invoice['Invoice']['amount_paid'])*$invoice['Invoice']['exchange_rate'],2);
				$totalCS+=round(($invoice['Invoice']['price_total'])*$invoice['Invoice']['exchange_rate'],2);
			}
			
			$pageRow="";
				$pageRow.="<td>".$invoiceDateTime->format('d-m-Y')."</td>";
				$pageRow.="<td>".$numberLateDays."</td>";
				$pageRow.="<td>".$this->Html->link($invoice['Client']['name'], array('controller' => 'clients', 'action' => 'view', $invoice['Client']['id']))."</td>";
				$pageRow.="<td>".$this->Html->link($invoice['Invoice']['invoice_code'].($invoice['Invoice']['bool_annulled']?" (Anulada)":""),array('action'=>'detalle',$invoice['Invoice']['id']))."</td>";
				//if(!empty($invoice['InvoiceSalesOrder'])){
				//	$pageRow.="<td>";
				//	foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
				//		$pageRow.=$this->Html->link($invoiceSalesOrder['SalesOrder']['sales_order_code'], array('controller' => 'sales_orders', 'action' => 'view', $invoiceSalesOrder['SalesOrder']['id']))."<br/>";
				//	}
				//	$pageRow.="</td>";
				//}
				//else {
				//	$pageRow.="<td>-</td>";
				//	$pageRow.="<td>-</td>";
				//}
				//$pageRow.="<td".$currencyClass."><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_subtotal'])."</span></td>";
				//$pageRow.="<td".$currencyClass."><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_iva'])."</span></td>";
				//$pageRow.="<td".$currencyClass."><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['amount_paid'])."</span></td>";
				//$pageRow.="<td".$currencyClass."><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_total']-$invoice['Invoice']['amount_paid'])."</span></td>";
				$pageRow.="<td".$currencyClass."><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_total'])."</span></td>";
				if (!empty($invoice['InvoiceSalesOrder'][0]['SalesOrder']['Quotation']['Contact'])){
					$pageRow.="<td>".$this->Html->link($invoice['InvoiceSalesOrder'][0]['SalesOrder']['Quotation']['Contact']['first_name']." ".$invoice['InvoiceSalesOrder'][0]['SalesOrder']['Quotation']['Contact']['last_name'], array('controller' => 'contact', 'action' => 'view', $invoice['InvoiceSalesOrder'][0]['SalesOrder']['Quotation']['Contact']['id']))."</td>";
					if (!empty($invoice['InvoiceSalesOrder'][0]['SalesOrder']['Quotation']['Contact']['cell'])){
						$pageRow.="<td>".$invoice['InvoiceSalesOrder'][0]['SalesOrder']['Quotation']['Contact']['cell']."</td>";
					}
					else {
						$pageRow.="<td>-</td>";
					}
				}
				else {
					$pageRow.="<td>-</td>";
					$pageRow.="<td>-</td>";
				}
				if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
					if (!empty($invoice['InvoiceSalesOrder'])){
						$pageRow.="<td>";
						//20170214 AS THE SALES ORDERS ARE NO LONGER SHOWN, THE USER ONLY NEEDS TO BE SHOWN ONCE
						//foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
						//	$pageRow.=$this->Html->link($invoiceSalesOrder['User']['username'], array('controller' => 'users', 'action' => 'view', $invoiceSalesOrder['User']['id']));
						//	$pageRow.="<br/>";
						//}
						$pageRow.=$this->Html->link($invoice['InvoiceSalesOrder'][0]['User']['username'], array('controller' => 'users', 'action' => 'view', $invoice['InvoiceSalesOrder'][0]['User']['id']));
						$pageRow.="</td>";
					}
					else {
						$pageRow.="<td>-</td>";
					}
				}
			$excelBody.="<tr>".$pageRow."</tr>";
			if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
				$pageRow.="<td class='hidden'>".$this->Form->input('Invoice.'.$inv.".id",array('value'=>$invoice['Invoice']['id'],'label'=>false,'type'=>'hidden'))."</td>";
				$pageRow.="<td>".$this->Form->input('Invoice.'.$inv.'.cash_receipt_code',array('label'=>false,'value'=>''))."</td>";
				$pageRow.="<td>".$this->Form->input('Invoice.'.$inv.'.last_payment_date',array('label'=>false,'dateFormat'=>'DMY'))."</td>";
				$pageRow.="<td>".$this->Form->submit('Cancelar factura',array('id'=>'Invoice.'.$inv.'.submit','name'=>'Invoice.'.$inv.'.submit'))."</td>";					
			}
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
				//$pageTotalRow.="<td></td>";
				//$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$subtotalCS."</span></td>";
				//$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$ivaCS."</span></td>";
				//$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$paidCS."</span></td>";
				$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$totalCS."</span></td>";
				$pageTotalRow.="<td></td>";
				$pageTotalRow.="<td></td>";
				if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
					$pageTotalRow.="<td></td>";
					$pageTotalRow.="<td class='hidden'></td>";
					$pageTotalRow.="<td></td>";
					$pageTotalRow.="<td></td>";
					$pageTotalRow.="<td></td>";
				}
			$pageTotalRow.="</tr>";
		}
		if ($currencyId==CURRENCY_USD){
			$pageTotalRow.="<tr class='totalrow'>";
				$pageTotalRow.="<td>Total US$</td>";
				$pageTotalRow.="<td></td>";
				$pageTotalRow.="<td></td>";
				$pageTotalRow.="<td></td>";
				//$pageTotalRow.="<td></td>";
				//$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$subtotalUSD."</td>";
				//$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$ivaUSD."</td>";
				//$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$paidUSD."</td>";
				$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$totalUSD."</td>";
				$pageTotalRow.="<td></td>";
				$pageTotalRow.="<td></td>";
				if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
					$pageTotalRow.="<td></td>";
					$pageTotalRow.="<td class='hidden'></td>";
					$pageTotalRow.="<td></td>";
					$pageTotalRow.="<td></td>";
					$pageTotalRow.="<td></td>";
				}
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
				$excelTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$ivaCS."</span></td>";
				//$excelTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$paidCS."</td>";
				$excelTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$totalCS."</span></td>";
				$excelTotalRow.="<td></td>";
				if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
					$excelTotalRow.="<td></td>";
					$excelTotalRow.="<td class='hidden'></td>";
					$excelTotalRow.="<td></td>";
					$excelTotalRow.="<td></td>";
					$excelTotalRow.="<td></td>";
				}
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
				$excelTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$ivaUSD."</td>";
				//$excelTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$paidUSD."</td>";
				$excelTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$totalUSD."</td>";
				$excelTotalRow.="<td></td>";
				if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
					$excelTotalRow.="<td></td>";
					$excelTotalRow.="<td class='hidden'></td>";
					$excelTotalRow.="<td></td>";
					$excelTotalRow.="<td></td>";
					$excelTotalRow.="<td></td>";
				}
			$excelTotalRow.="</tr>";
		}
		
		
		$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
		$excelBody="<tbody>".$excelTotalRow.$excelBody.$excelTotalRow."</tbody>";
		$table_id="cuentas por cobrar";
		$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
		echo "<h3>Facturas por cobrar</h3>";
		echo $pageOutput;
	echo "</fieldset>";
	echo $this->Form->end(); 	
	$excelOutput.="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
			
	$pageHeader="";
	$excelHeader="";
	$pageHeader.="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>Cliente</th>";
			$pageHeader.="<th class='centered'>Saldo Total</th>";
			$pageHeader.="<th class='centered'>%</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader.="<thead>";
		$excelHeader.="<tr><th colspan='3' align='center'>".COMPANY_NAME."</th></tr>";	
		$excelHeader.="<tr><th colspan='3' align='center'>".__('Cuentas por cobrar - Clientes')."</th></tr>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>Cliente</th>";
			$excelHeader.="<th>Saldo Total</th>";
			$excelHeader.="<th>%</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	foreach ($clientesPorCobrar as $cliente){ 
		if ($currencyId==CURRENCY_USD){
			$currencyClass=" class='USDcurrency'";
		}
		else {
			$currencyClass=" class='CScurrency'";
		}
		$pageRow="";
			$pageRow.="<td>".$this->Html->link($cliente['name'], array('controller' => 'clients', 'action' => 'view', $cliente['id']))."</td>";
			$pageRow.="<td".$currencyClass."><span class='currency'></span><span class='amountright'>".$cliente['saldo']."</span></td>";
			if ($currencyId==CURRENCY_USD){
				$pageRow.="<td class='percentage'><span class='amountright'>".($cliente['saldo']/$totalUSD)."</span></td>";
			}
			else {
				$pageRow.="<td class='percentage'><span class='amountright'>".($cliente['saldo']/$totalCS)."</span></td>";
			}
		$pageBody.="<tr>".$pageRow."</tr>";
	}

	$pageTotalRow="";
	
	if ($currencyId==CURRENCY_USD){
		$pageTotalRow.="<tr class='totalrow'>";
			$pageTotalRow.="<td>Total US$</td>";
			$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$totalUSD."</span></td>";
			$pageTotalRow.="<td></td>";
		$pageTotalRow.="</tr>";
	}
	else {
		$pageTotalRow.="<tr class='totalrow'>";
			$pageTotalRow.="<td>Total C$</td>";
			$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$subtotalCS."</td>";
			$pageTotalRow.="<td></td>";
		$pageTotalRow.="</tr>";
	}	
	
	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	$excelBody="<tbody>".$excelBody."</tbody>";
	$table_id="clientes por cobrar";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	
	echo "<div class='container-fluid'>";
			echo "<div class='row'>";
				echo "<div class='col-md-6'>";
					echo "<h3>Clientes por cobrar</h3>";
					echo $pageOutput;
				echo "</div>";
	$excelOutput.="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	
	if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
		$pageHeader="";
		$excelHeader="";
		$pageHeader.="<thead>";
			$pageHeader.="<tr>";
				$pageHeader.="<th>Cliente</th>";
				$pageHeader.="<th class='centered'>Saldo Total</th>";
				$pageHeader.="<th class='centered'>%</th>";
			$pageHeader.="</tr>";
		$pageHeader.="</thead>";
		$excelHeader.="<thead>";
			$excelHeader.="<tr><th colspan='3' align='center'>".COMPANY_NAME."</th></tr>";	
			$excelHeader.="<tr><th colspan='3' align='center'>".__('Cuentas por cobrar - Vendedores')."</th></tr>";
			$excelHeader.="<tr>";
				$excelHeader.="<th>Vendedor</th>";
				$excelHeader.="<th>Saldo Total</th>";
				$excelHeader.="<th>%</th>";
			$excelHeader.="</tr>";
		$excelHeader.="</thead>";

		$pageBody="";
		$excelBody="";

		foreach ($vendedoresPorCobrar as $vendedor){ 
			if ($currencyId==CURRENCY_USD){
				$currencyClass=" class='USDcurrency'";
			}
			else {
				$currencyClass=" class='CScurrency'";
			}
			$pageRow="";
				$pageRow.="<td>".$this->Html->link($vendedor['name'], array('controller' => 'users', 'action' => 'view', $vendedor['id']))."</td>";
				$pageRow.="<td".$currencyClass."><span class='currency'></span><span class='amountright'>".$vendedor['saldo']."</span></td>";
				if ($currencyId==CURRENCY_USD){
					$pageRow.="<td class='percentage'><span class='amountright'>".($vendedor['saldo']/$totalUSD)."</span></td>";
				}
				else {
					$pageRow.="<td class='percentage'><span class='amountright'>".($vendedor['saldo']/$totalCS)."</span></td>";
				}
			$pageBody.="<tr>".$pageRow."</tr>";
		}

		$pageTotalRow="";
		
		if ($currencyId==CURRENCY_USD){
			$pageTotalRow.="<tr class='totalrow'>";
				$pageTotalRow.="<td>Total US$</td>";
				$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$totalUSD."</span></td>";
				$pageTotalRow.="<td></td>";
			$pageTotalRow.="</tr>";
		}
		else {
			$pageTotalRow.="<tr class='totalrow'>";
				$pageTotalRow.="<td>Total C$</td>";
				$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$subtotalCS."</td>";
				$pageTotalRow.="<td></td>";
			$pageTotalRow.="</tr>";
		}	
		
		$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
		$excelBody="<tbody>".$excelBody."</tbody>";
		$table_id="vendedores por cobrar";
		$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
		
					echo "<div class='col-md-6'>";
						echo "<h3>Vendedores por cobrar</h3>";
						echo $pageOutput;
					echo "</div>";
				echo "</div>";
			echo "</div>";
		$excelOutput.="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	}
	
	$_SESSION['cuentasPorCobrar'] = $excelOutput;
?>
</div>
