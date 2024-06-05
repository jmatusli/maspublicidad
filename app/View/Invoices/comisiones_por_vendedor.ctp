<script>
	$('body').on('change','.powerselector',function(e){
		if ($(this).is(':checked')){
			$(this).closest('fieldset').find('td.selector input').prop('checked',true);
			$(this).closest('fieldset').find('input.powerselector').prop('checked',true);
		}
		else {
			$(this).closest('fieldset').find('td.selector input').prop('checked',false);
			$(this).closest('fieldset').find('input.powerselector').prop('checked',false);
		}
	});

	$('body').on('click','#apply_commission',function(e){	
		var commissionValue=$(this).closest('table').find('.appliedpercentage').val();
		$('td.selector input:checked').each(function(){
			$(this).closest('tr').find('input.commissionpercentage').val(commissionValue);
			updateCommissionForInvoice($(this).closest('tr').find('input.commissionpercentage').attr('id'));
		});
	});
	
	$('body').on('change','.commissionpercentage',function(e){	
		updateCommissionForInvoice($(this).attr('id'));
	});
	
	function updateCommissionForInvoice(commissionPercentageValueId){
		var subtotal=parseFloat($('#'+commissionPercentageValueId).closest('tr').find('td.subtotal.hidden').text());
		if ($('#ReportCurrencyId').val()==<?php echo CURRENCY_USD; ?>){
			var exchangerate=parseFloat($('#'+commissionPercentageValueId).closest('tr').find('input.exchangerate').val());
			subtotal=subtotal*exchangerate;
		}
		var commissionpercentage=parseFloat($('#'+commissionPercentageValueId).val());
		var commissionvalue=roundToTwo(commissionpercentage*subtotal/100);
		$('#'+commissionPercentageValueId).closest('tr').find('input.commissionvalue').val(commissionvalue);
		
		calculateTotalRows();
	}
	
	function calculateTotalRows(){
		$('table.invoice.period').each(function(){
			var totalcommission=0;
			var commissionforrow=0;
			$(this).find('tr:not(.totalrow) td.commissionamount input').each(function(){
				var commissionvalue=$(this).val();
				var boolnan = isNaN($(this).val());
				if ($(this).val()&&!isNaN($(this).val())){
					commissionforrow=parseFloat($(this).val());
					totalcommission+=roundToTwo(commissionforrow);
				}
			});
			$(this).find('tr:not(.totalrow) td.commissionamount span.amountright').each(function(){
				var commissionvalue=$(this).textContent();
				var boolnan = isNaN($(this).textContent());
				if ($(this).textContent()&&!isNaN($(this).textContent())){
					commissionforrow=parseFloat($(this).textContent());
					totalcommission+=roundToTwo(commissionforrow);
				}
			});
			$(this).find('tr.totalrow td.commissionamount span.amountright').text(roundToTwo(totalcommission));
		});
		
		$('table.invoice.previous').each(function(){
			var totalcommission=0;
			var commissionforrow=0;
			$(this).find('tr:not(.totalrow) td.commissionamount span.amountright').each(function(){
				if (!$(this).text()||isNaN($(this).text())){
					commissionforrow=parseFloat($(this).text());
					totalcommission+=roundToTwo(commissionforrow);
				}
			});
			$(this).find('tr.totalrow td.commissionamount span.amountright').text(roundToTwo(totalcommission));
		});	
	}
	
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
		$("td.percentage span.amount div input").each(function(){
			if (Math.abs(parseFloat($(this).val()))<0.001){
				$(this).val("0");
			}
			$(this).number(true,2,'.',',');
			//$(this).closest('div').append(" %");
		});
	}
	
	function formatCSCurrencies(){
		$("td.CScurrency").each(function(){
			if (parseFloat($(this).find('.amountright').text())<0){
				$(this).find('.amountright').prepend("-");
			}
			if ($(this).find('.amountright').text()){
				$(this).find('.amountright').number(true,2);
			}
			$(this).find('.currency').text("C$");
		});
	}
	
	function formatUSDCurrencies(){
		$("td.USDcurrency").each(function(){
			if (parseFloat($(this).find('.amountright').text())<0){
				$(this).find('.amountright').prepend("-");
			}
			if ($(this).find('.amountright').text()){
				$(this).find('.amountright').number(true,2);
			}
			$(this).find('.currency').text("US$");
		});
	}
	
	$(document).ready(function(){
		formatNumbers();
		formatPercentages();
		formatCSCurrencies();
		formatUSDCurrencies();
		$('td.selector').each(function(){
			updateCommissionForInvoice($(this).closest('tr').find('input.commissionpercentage').attr('id'));
		});
		
		calculateTotalRows();
	});
</script>

<div class="invoices comisiones_por_vendedor fullwidth">
<?php 
	$excelOutput="";
	
	echo "<h2>".__('Comisiones por Vendedor')."</h2>";
	echo "<div class='container-fluid'>";
		echo "<div class='rows'>";
			echo "<div class='col-md-4'>";
				echo $this->Form->create('Report');
					echo "<fieldset>";
						echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015));
						echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015));
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT) { 
							echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Usuario'),'options'=>$users,'default'=>$userId,'empty'=>array('0'=>__('Todos Usuarios'))));
						}
						else {
							echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Usuario'),'options'=>$users,'default'=>$userId));
						}
						echo $this->Form->input('Report.currency_id',array('label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId));
						echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
						echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";								
						echo "<br/>";
						echo $this->Form->submit(__('Refresh'),array('id'=>'refresh','name'=>'refresh'));
					echo "</fieldset>";
				echo $this->Form->end();
				echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarComisionesPorVendedor'), array( 'class' => 'btn btn-primary'));
				echo $this->Html->link(__('Guardar como pdf'), array('action' => 'verPdfComisionesPorVendedor','ext'=>'pdf', $userId,$currencyId,$startDate,$endDate,$filename),array('class' => 'btn btn-primary','target'=>'_blank'));
			echo "</div>";
			echo "<div class='col-md-8'>";
			if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
				echo "<h3>Resumen Totales</h3>";
				$overviewTable="";
				$overviewTable.="<table id='resumen_comisiones_vendedor'>";
					$overviewTable.="<thead>";
						$overviewTable.="<tr>";
							$overviewTable.="<th>Vendedor</th>";
							$overviewTable.="<th>Subtotal Efectivo</th>";
							$overviewTable.="<th>Comisión Efectivo</th>";
							$overviewTable.="<th>Subtotal Crédito</th>";
							$overviewTable.="<th>Comisión Crédito</th>";
							$overviewTable.="<th>Subtotal Recuperado</th>";
							$overviewTable.="<th>Comisión Recuperado</th>";
							$overviewTable.="<th>Subtotal Pendiente</th>";
							$overviewTable.="<th>Comisión Pendiente</th>";
						$overviewTable.="</tr>";
					$overviewTable.="</thead>";
					$overviewTable.="<tbody>";
					foreach ($selectedUsers as $user){
						$overviewTable.="<tr>";
							$overviewTable.="<td>".$user['User']['username']."</td>";
							switch ($currencyId){
								case CURRENCY_CS:
									$overviewTable.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$user['cash_subtotal_CS']."</span></td>";
									$overviewTable.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$user['cash_commission_CS']."</span></td>";
									$overviewTable.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$user['credit_subtotal_CS']."</span></td>";
									$overviewTable.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$user['credit_commission_CS']."</span></td>";
									$overviewTable.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$user['recovered_subtotal_CS']."</span></td>";
									$overviewTable.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$user['recovered_commission_CS']."</span></td>";
									$overviewTable.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$user['pending_subtotal_CS']."</span></td>";
									$overviewTable.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$user['pending_commission_CS']."</span></td>";
									break;
								case CURRENCY_USD:
									$overviewTable.="<td class='USDcurrency'><span class='currency'>US$ </span><span class='amountright'>".$user['cash_subtotal_USD']."</span></td>";
									$overviewTable.="<td class='USDcurrency'><span class='currency'>US$ </span><span class='amountright'>".$user['cash_commission_USD']."</span></td>";
									$overviewTable.="<td class='USDcurrency'><span class='currency'>US$ </span><span class='amountright'>".$user['credit_subtotal_USD']."</span></td>";
									$overviewTable.="<td class='USDcurrency'><span class='currency'>US$ </span><span class='amountright'>".$user['credit_commission_USD']."</span></td>";
									$overviewTable.="<td class='USDcurrency'><span class='currency'>US$ </span><span class='amountright'>".$user['recovered_subtotal_USD']."</span></td>";
									$overviewTable.="<td class='USDcurrency'><span class='currency'>US$ </span><span class='amountright'>".$user['recovered_commission_USD']."</span></td>";
									$overviewTable.="<td class='USDcurrency'><span class='currency'>US$ </span><span class='amountright'>".$user['pending_subtotal_USD']."</span></td>";
									$overviewTable.="<td class='USDcurrency'><span class='currency'>US$ </span><span class='amountright'>".$user['pending_commission_USD']."</span></td>";
									break;
							}
						$overviewTable.="</tr>";
					}
					$overviewTable.="</tbody>";
				$overviewTable.="</table>";
				echo $overviewTable;
				$excelOutput.=$overviewTable;
			}
			echo "</div>";
		echo "</div>";
	echo "</div>";	
	
	$startDateTime=new DateTime($startDate);
	$endDateTime=new DateTime($endDate);
?> 
</div>

<div>
<?php
	
	if ($currencyId==CURRENCY_USD){
		$currencyClass="USDcurrency";
	}
	else {
		$currencyClass="CScurrency";
	}
	foreach ($selectedUsers as $selectedUser){
		//pr($selectedUser);
		$showUser=true;
		if (!empty($userId)){
			if ($selectedUser['User']['id']!=$userId){
				$showUser=false;
			}
		}
		if ($showUser){
			$selectedUserId=$selectedUser['User']['id'];
			
			$subTotalCashCS=0;
			$subTotalCashUSD=0;
			$subTotalCreditCS=0;
			$subTotalCreditUSD=0;
			$subTotalAllCS=0;
			$subTotalAllUSD=0;
			
			echo $this->Form->create('User'.$selectedUserId); 
			
			echo "<fieldset>";
			
			echo $this->Form->input('User.'.$selectedUser['User']['id'].'.powerselector1',array('class'=>'powerselector','checked'=>true,'style'=>'width:5em;','label'=>array('text'=>'Seleccionar/Deseleccionar facturas para usuario','style'=>'padding-left:5em;'),'format' => array('before', 'input', 'between', 'label', 'after', 'error' )));
			// FIRST THE CASH INVOICES
			if (!empty($selectedUser['cashInvoices'])){
				$pageHeader="";
				$excelHeader="";
				$pageHeader.="<thead>";
					$pageHeader.="<tr>";
						$pageHeader.="<th>Seleccione</th>";
						$pageHeader.="<th>".$this->Paginator->sort('invoice_date')."</th>";
						$pageHeader.="<th>".$this->Paginator->sort('invoice_code')."</th>";
						$pageHeader.="<th>".$this->Paginator->sort('SalesOrder.sales_order_code','Orden de Venta')."</th>";
						$pageHeader.="<th>".$this->Paginator->sort('Client.name')."</th>";
						$pageHeader.="<th class='centered'>".$this->Paginator->sort('price_subtotal')."</th>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$pageHeader.="<th class='centered'>Comisión %</th>";
							$pageHeader.="<th class='centered'>Comisión Total</th>";
						}
					$pageHeader.="</tr>";
				$pageHeader.="</thead>";
				$colSpanNumber=5;
				if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
					$colSpanNumber=7;
				}
				$excelHeader.="<thead>";				
					$excelHeader.="<tr><th colspan='".$colSpanNumber."' align='center'>".COMPANY_NAME."</th></tr>";	
					$excelHeader.="<tr><th colspan='".$colSpanNumber."' align='center'>".__('Facturas de Contado ')." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')." para ".$selectedUser['User']['username']."</th></tr>";
					$excelHeader.="<tr>";
						$excelHeader.="<th>Fecha</th>";
						$excelHeader.="<th>Factura</th>";
						$excelHeader.="<th>Orden de Venta</th>";
						$excelHeader.="<th>Cliente</th>";
						$excelHeader.="<th class='centered'>".$this->Paginator->sort('price_subtotal')."</th>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$excelHeader.="<th class='centered'>Comisión %</th>";
							$excelHeader.="<th class='centered'>Comisión Total</th>";
						}
					$excelHeader.="</tr>";
				$excelHeader.="</thead>";

				$pageBody="";
				$excelBody="";
				
				$subTotalCS=0;
				$commissionCS=0;
				$subTotalUSD=0;
				$commissionUSD=0;

				foreach ($selectedUser['cashInvoices'] as $invoice){ 
					$invoiceDateTime=new DateTime($invoice['Invoice']['invoice_date']);
					if ($invoice['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$invoice['Invoice']['price_subtotal'];
						$commissionCS+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						//added calculation of totals in US$
						$subTotalUSD+=round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']/$invoice['Invoice']['exchange_rate'],2);
					}
					elseif ($invoice['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$invoice['Invoice']['price_subtotal'];
						$commissionUSD+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						//added calculation of totals in CS$
						$subTotalCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2);
						$commissionCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']*$invoice['Invoice']['exchange_rate'],2);
					}
					
					$pageRow="";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$pageRow.="<td class='selector'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.selector',array('checked'=>true,'label'=>false))."</td>";
						}
						
						$pageRow.="<td>";
							$pageRow.=$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.invoiceid',array('value'=>$invoice['Invoice']['id'],'class'=>'invoiceid','type'=>'hidden'));
							$pageRow.=$invoiceDateTime->format('d-m-Y');
						$pageRow.="</td>";
						$pageRow.="<td>".$this->Html->link($invoice['Invoice']['invoice_code'],array('action'=>'view',$invoice['Invoice']['id']))."</td>";
						if (!empty($invoice['InvoiceSalesOrder'])){
							$pageRow.="<td>";
							foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
								$pageRow.=$this->Html->link($invoiceSalesOrder['SalesOrder']['sales_order_code'], array('controller' => 'sales_orders', 'action' => 'view', $invoiceSalesOrder['SalesOrder']['id']));
								$pageRow.="<br/>";
							}
							$pageRow.="</td>";
						}
						else {
							$pageRow.="<td>-</td>";
						}
						$pageRow.="<td>".$this->Html->link($invoice['Client']['name'], array('controller' => 'clients', 'action' => 'view', $invoice['Client']['id']))."</td>";
						if ($currencyId==CURRENCY_USD){
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_subtotal'])."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".h($invoice['Invoice']['price_subtotal'])."</td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2)."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2)."</td>";
							}
						}
						else {
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2)."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2)."</td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_subtotal'])."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".h($invoice['Invoice']['price_subtotal'])."</td>";
							}
						}
						$pageRow.="<td class='hidden'><span class='currency'></span><span class='amountright'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.exchange_rate',array('value'=>$invoice['Invoice']['exchange_rate'],'class'=>'exchangerate','readonly'=>'readonly'))."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$pageRow.="<td class='percentage centered'><span class='amountright'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.percentage_commission',array('value'=>round($invoice['Invoice']['percentage_commission']>0?$invoice['Invoice']['percentage_commission']:(100*$selectedUser['historical_performance']),2),'label'=>false,'class'=>'commissionpercentage','style'=>'text-align:center;'))."</span></td>";
							$pageRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.amount_commission',array('default'=>$selectedUser['historical_performance']*$invoice['Invoice']['price_subtotal'],'value'=>$invoice['Invoice']['amount_commission'],'label'=>false,'readonly'=>'readonly','class'=>'commissionvalue','style'=>'text-align:right;'))."</span></td>";
						}
					$excelBody.="<tr>".$pageRow."</tr>";
					$pageBody.="<tr>".$pageRow."</tr>";
				}

				$pageTotalRow="";
				if ($currencyId==CURRENCY_CS){
					$currencyClass="CScurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total C$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$currencyClass."'><span class='currency'></span><span class='amountright'>".$subTotalCS."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$commissionCS."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				if ($currencyId==CURRENCY_USD){
					$currencyClass="USDcurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total US$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$currencyClass."'><span class='currency'></span><span class='amountright'>".$subTotalUSD."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$commissionCS."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				
				$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
				$excelBody="<tbody>".$excelBody."</tbody>";
				
				$subTotalCashCS=$subTotalCS;
				$subTotalCashUSD=$subTotalUSD;
				
				$table_id=substr("facturas_contado_".$selectedUser['User']['username'],0,30);
				$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."' class='invoice period'>".$pageHeader.$pageBody."</table>";
				echo "<h3>Facturas de contado para vendedor ".$selectedUser['User']['username']."</h3>";
				echo $pageOutput;
				
				$excelOutput.="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
			}
			else {
				echo "<h3>No hay facturas de contado para vendedor ".$selectedUser['User']['username']."</h3>";
			}
			
			// SECOND THE CREDIT INVOICES
			if (!empty($selectedUser['creditInvoices'])){
				$pageHeader="";
				$excelHeader="";
				$pageHeader.="<thead>";
					$pageHeader.="<tr>";
						$pageHeader.="<th>Seleccione</th>";
						$pageHeader.="<th>".$this->Paginator->sort('invoice_date')."</th>";
						$pageHeader.="<th>".$this->Paginator->sort('invoice_code')."</th>";
						$pageHeader.="<th>".$this->Paginator->sort('SalesOrder.sales_order_code','Orden de Venta')."</th>";
						$pageHeader.="<th>".$this->Paginator->sort('Client.name')."</th>";
						$pageHeader.="<th class='centered'>".$this->Paginator->sort('price_subtotal')."</th>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$pageHeader.="<th class='centered'>Comisión %</th>";
							$pageHeader.="<th class='centered'>Comisión Total</th>";
						}
					$pageHeader.="</tr>";
				$pageHeader.="</thead>";
				$colSpanNumber=5;
				if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
					$colSpanNumber=7;
				}
				$excelHeader.="<thead>";				
					$excelHeader.="<tr><th colspan='".$colSpanNumber."' align='center'>".COMPANY_NAME."</th></tr>";	
					$excelHeader.="<tr><th colspan='".$colSpanNumber."' align='center'>".__('Facturas de Contado ')." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')." para ".$selectedUser['User']['username']."</th></tr>";
					$excelHeader.="<tr>";
						$excelHeader.="<th>Fecha</th>";
						$excelHeader.="<th>Factura</th>";
						$excelHeader.="<th>Orden de Venta</th>";
						$excelHeader.="<th>Cliente</th>";
						$excelHeader.="<th class='centered'>".$this->Paginator->sort('price_subtotal')."</th>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$excelHeader.="<th class='centered'>Comisión %</th>";
							$excelHeader.="<th class='centered'>Comisión Total</th>";
						}
					$excelHeader.="</tr>";
				$excelHeader.="</thead>";

				$pageBody="";
				$excelBody="";
				
				$subTotalCS=0;
				$commissionCS=0;
				$subTotalUSD=0;
				$commissionUSD=0;

				foreach ($selectedUser['creditInvoices'] as $invoice){ 
					$invoiceDateTime=new DateTime($invoice['Invoice']['invoice_date']);
					if ($invoice['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$invoice['Invoice']['price_subtotal'];
						$commissionCS+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						//added calculation of totals in US$
						$subTotalUSD+=round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']/$invoice['Invoice']['exchange_rate'],2);
					}
					elseif ($invoice['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$invoice['Invoice']['price_subtotal'];
						$commissionUSD+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						//added calculation of totals in CS$
						$subTotalCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2);
						$commissionCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']*$invoice['Invoice']['exchange_rate'],2);
					}
					
					$pageRow="";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							if ($invoice['Invoice']['id']){
								$pageRow.="<td class='selector'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.selector',array('checked'=>true,'label'=>false))."</td>";
							}
						}
						$pageRow.="<td>";
							$pageRow.=$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.invoiceid',array('value'=>$invoice['Invoice']['id'],'class'=>'invoiceid','type'=>'hidden'));
							$pageRow.=$invoiceDateTime->format('d-m-Y');
						$pageRow.="</td>";
						$pageRow.="<td>".$this->Html->link($invoice['Invoice']['invoice_code'],array('action'=>'view',$invoice['Invoice']['id']))."</td>";
						if (!empty($invoice['InvoiceSalesOrder'])){
							$pageRow.="<td>";
							foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
								$pageRow.=$this->Html->link($invoiceSalesOrder['SalesOrder']['sales_order_code'], array('controller' => 'sales_orders', 'action' => 'view', $invoiceSalesOrder['SalesOrder']['id']));
								$pageRow.="<br/>";
							}
							$pageRow.="</td>";
						}
						else {
							$pageRow.="<td>-</td>";
						}
						$pageRow.="<td>".$this->Html->link($invoice['Client']['name'], array('controller' => 'clients', 'action' => 'view', $invoice['Client']['id']))."</td>";
						if ($currencyId==CURRENCY_USD){
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_subtotal'])."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".h($invoice['Invoice']['price_subtotal'])."</td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2)."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2)."</td>";
							}
						}
						else {
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2)."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2)."</td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_subtotal'])."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".h($invoice['Invoice']['price_subtotal'])."</td>";
							}
						}
						$pageRow.="<td class='hidden'><span class='currency'></span><span class='amountright'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.exchange_rate',array('value'=>$invoice['Invoice']['exchange_rate'],'class'=>'exchangerate','readonly'=>'readonly'))."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageRow.="<td class='percentage centered'><span class='amount'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.percentage_commission',array('value'=>round($invoice['Invoice']['percentage_commission']>0?$invoice['Invoice']['percentage_commission']:(100*$selectedUser['historical_performance']),2),'label'=>false,'class'=>'commissionpercentage','style'=>'text-align:center;'))."</span></td>";
							$pageRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.amount_commission',array('default'=>$selectedUser['historical_performance']*$invoice['Invoice']['price_subtotal'],'value'=>$invoice['Invoice']['amount_commission'],'label'=>false,'readonly'=>'readonly','class'=>'commissionvalue','style'=>'text-align:right;'))."</span></td>";
						}
					$excelBody.="<tr>".$pageRow."</tr>";
					$pageBody.="<tr>".$pageRow."</tr>";
				}

				$pageTotalRow="";
				if ($currencyId==CURRENCY_CS){
					$currencyClass="CScurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total C$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$currencyClass."'><span class='currency'></span><span class='amountright'>".$subTotalCS."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$commissionCS."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				if ($currencyId==CURRENCY_USD){
					$currencyClass="USDcurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total US$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$currencyClass."'><span class='currency'></span><span class='amountright'>".$subTotalUSD."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$commissionCS."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				
				$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
				$excelBody="<tbody>".$excelBody."</tbody>";
				
				$subTotalCreditCS=$subTotalCS;
				$subTotalCreditUSD=$subTotalUSD;
				$table_id=substr("facturas_credito_".$selectedUser['User']['username'],0,30);
				$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."' class='invoice period'>".$pageHeader.$pageBody."</table>";
				echo "<h3>Facturas de crédito para vendedor ".$selectedUser['User']['username']."</h3>";
				echo $pageOutput;
				
				$excelOutput.="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
			}
			else {
				echo "<h3>No hay facturas de crédito para vendedor ".$selectedUser['User']['username']."</h3>";
			}
			
			echo $this->Form->input('User.'.$selectedUser['User']['id'].'.powerselector2',array('class'=>'powerselector','checked'=>true,'style'=>'width:5em;','label'=>array('text'=>'Seleccionar/Deseleccionar facturas para usuario','style'=>'padding-left:5em;'),'format' => array('before', 'input', 'between', 'label', 'after', 'error' )));
			
			// THEN SHOW THE OVERVIEW FIRST
			$subTotalCS=$subTotalCashCS + $subTotalCreditCS;
			$subTotalUSD=$subTotalCashUSD + $subTotalCreditUSD;
			if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
				echo "<div class='container-fluid'>";
					echo "<div class='row'>";
						echo "<div class='col-md-4'>";
							echo "<table>";
								echo "<tbody>";
								if ($currencyId==CURRENCY_USD){
									$classCurrency="USDcurrency";
									echo "<tr>";
										echo "<td>Facturas de Contado</td>";
										echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$subTotalCashUSD."</span></td>";
									echo "</tr>";
									echo "<tr>";
										echo "<td>Facturas de Crédito</td>";
										echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$subTotalCreditUSD."</span></td>";
									echo "</tr>";
									echo "<tr>";
										echo "<td>Total Ventas</td>";
										echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$subTotalUSD."</span></td>";
									echo "</tr>";
								}
								else {
									$classCurrency="CScurrency";
									echo "<tr>";
										echo "<td>Facturas de Contado</td>";
										echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$subTotalCashCS."</span></td>";
									echo "</tr>";
									echo "<tr>";
										echo "<td>Facturas de Crédito</td>";
										echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$subTotalCreditCS."</span></td>";
									echo "</tr>";
									echo "<tr>";
										echo "<td>Total Ventas</td>";
										echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$subTotalCS."</span></td>";
									echo "</tr>";
								}
								echo "</tbody>";
							echo "</table>";
						echo "</div>";	
						echo "<div class='col-md-4'>";
							echo "<table>";
								echo "<tbody>";
								if ($currencyId==CURRENCY_USD){
									$classCurrency="USDcurrency";
									echo "<tr>";
										echo "<td>Venta Mínima</td>";
										if (!empty($selectedUser['SalesObjective'])){
											echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".round($selectedUser['SalesObjective']['minimum_objective']/$exchangeRateStartDate['ExchangeRate']['rate'],2)."</span></td>";
										}
										else {
											echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".round(1000000000/$exchangeRateStartDate['ExchangeRate']['rate'],2)."</span></td>";
										}
									echo "</tr>";
									echo "<tr>";
										echo "<td>Meta</td>";
										if (!empty($selectedUser['SalesObjective'])){
											echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".round($selectedUser['SalesObjective']['maximum_objective']/$exchangeRateStartDate['ExchangeRate']['rate'],2)."</span></td>";
										}
										else {
											echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".round(1000000000/$exchangeRateStartDate['ExchangeRate']['rate'],2)."</span></td>";
										}
									echo "</tr>";
									echo "<tr>";
										echo "<td>Cumplimiento</td>";
										//if ($subTotalUSD>$selectedUser['SalesObjective']['minimum_objective']/$exchangeRateStartDate['ExchangeRate']['rate']){
										if (!empty($selectedUser['SalesObjective'])){
											echo "<td class='percentage centered'><span class='amount'>".round((100*$subTotalUSD*$exchangeRateStartDate['ExchangeRate']['rate']/$selectedUser['SalesObjective']['maximum_objective']),2)."</span></td>";
										}
										else {
											echo "<td class='percentage centered'><span class='amount'>0</span></td>";
										}
									
										//}
										//else {
										//	echo "<td class='percentage centered'><span class='amount'>0</span></td>";
										//}
									echo "</tr>";
								}
								else {
									$classCurrency="CScurrency";
									echo "<tr>";
										echo "<td>Venta Mínima</td>";
										if (!empty($selectedUser['SalesObjective'])){
											echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$selectedUser['SalesObjective']['minimum_objective']."</span></td>";
										}
										else {
											echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>1000000000</span></td>";
										}
									echo "</tr>";
									echo "<tr>";
										echo "<td>Meta</td>";
										if (!empty($selectedUser['SalesObjective'])){
											echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$selectedUser['SalesObjective']['maximum_objective']."</span></td>";
										}
										else {
											echo "<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>1000000000</span></td>";
										}
									echo "</tr>";
									echo "<tr>";
										echo "<td>Cumplimiento</td>";
										//if ($subTotalCS>$selectedUser['SalesObjective']['minimum_objective']){
										if (!empty($selectedUser['SalesObjective'])){
											echo "<td class='percentage centered'><span class='amount'>".round((100*$subTotalCS/$selectedUser['SalesObjective']['maximum_objective']),2)."</span></td>";
										}
										else {
											echo "<td class='percentage centered'><span class='amount'>0</span></td>";
										}
										//}
										//else {
										//	echo "<td class='percentage centered'><span class='amount'>0</span></td>";
										//}
									echo "</tr>";
								}
								echo "</tbody>";
							echo "</table>";
						echo "</div>";	
						echo "<div class='col-md-4'>";
							if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
								echo "<table style='width:80%'>";
									echo "<tr>";
										echo "<td>Performancia Histórica</td>";
										echo "<td><span class='amountright'>".round(100*$selectedUser['historical_performance'],2)."</span></td>";
									echo "</tr>";
									echo "<tr>";
										echo "<td>Comisión a aplicar</td>";
										$defaultCommissionValue=0;
										if (!empty($selectedUser['SalesObjective'])){
											if ($subTotalCS>$selectedUser['SalesObjective']['minimum_objective']){
												$defaultCommissionValue=round((5*$subTotalCS/$selectedUser['SalesObjective']['maximum_objective']),2);
											}					
										}
										echo "<td><span class='amountright'>".$this->Form->input('Page.applied_commission.'.$selectedUserId,array('label'=>false,'value'=>$defaultCommissionValue,'class'=>'appliedpercentage'))."</span></td>";
									echo "</tr>";
									echo "<tr>";
										echo "<td><button id='apply_commission' type='button'>Aplicar comisión a facturas seleccionadas</button></td>";
									echo "</tr>";
								echo "</table>";
							}
						echo "</div>";	
					echo "</div>";	
				echo "</div>";	
			}
			echo $this->Form->submit('Guardar comisiones para vendedor '.$selectedUser['User']['username'],array('id'=>'User.'.$selectedUserId.'.save','name'=>'User.'.$selectedUserId.'.save','style'=>'width:400px;'))."</td>";
			
			//THIRD SHOW THE RECOVERED INVOICES
			if (!empty($selectedUser['recoveredInvoices'])){
				$pageHeader="";
				$excelHeader="";
				$pageHeader.="<thead>";
					$pageHeader.="<tr>";
						
						$pageHeader.="<th>".$this->Paginator->sort('invoice_date')."</th>";
						$pageHeader.="<th>".$this->Paginator->sort('invoice_code')."</th>";
						$pageHeader.="<th>".$this->Paginator->sort('SalesOrder.sales_order_code','Orden de Venta')."</th>";
						$pageHeader.="<th>".$this->Paginator->sort('Client.name')."</th>";
						$pageHeader.="<th class='centered'>".$this->Paginator->sort('price_subtotal')."</th>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$pageHeader.="<th class='centered'>Comisión %</th>";
							$pageHeader.="<th class='centered'>Comisión Total</th>";
						}
					$pageHeader.="</tr>";
				$pageHeader.="</thead>";
				$colSpanNumber=5;
				if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
					$colSpanNumber=7;
				}
				$excelHeader.="<thead>";				
					$excelHeader.="<tr><th colspan='".$colSpanNumber."' align='center'>".COMPANY_NAME."</th></tr>";	
					$excelHeader.="<tr><th colspan='".$colSpanNumber."' align='center'>".__('Facturas de Contado ')." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')." para ".$selectedUser['User']['username']."</th></tr>";
					$excelHeader.="<tr>";
						$excelHeader.="<th>Fecha</th>";
						$excelHeader.="<th>Factura</th>";
						$excelHeader.="<th>Orden de Venta</th>";
						$excelHeader.="<th>Cliente</th>";
						$excelHeader.="<th class='centered'>".$this->Paginator->sort('price_subtotal')."</th>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$excelHeader.="<th class='centered'>Comisión %</th>";
							$excelHeader.="<th class='centered'>Comisión Total</th>";
						}
					$excelHeader.="</tr>";
				$excelHeader.="</thead>";

				$pageBody="";
				$excelBody="";
				
				$subTotalCS=0;
				$commissionCS=0;
				$subTotalUSD=0;
				$commissionUSD=0;

				foreach ($selectedUser['recoveredInvoices'] as $invoice){ 
					$invoiceDateTime=new DateTime($invoice['Invoice']['invoice_date']);
					if ($invoice['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$invoice['Invoice']['price_subtotal'];
						$commissionCS+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						//added calculation of totals in US$
						$subTotalUSD+=round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']/$invoice['Invoice']['exchange_rate'],2);
					}
					elseif ($invoice['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$invoice['Invoice']['price_subtotal'];
						$commissionUSD+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						//added calculation of totals in CS$
						$subTotalCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2);
						$commissionCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']*$invoice['Invoice']['exchange_rate'],2);
					}
					
					$pageRow="";
						//if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							//$pageRow.="<td class='selector'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.PendingInvoice.'.$invoice['Invoice']['id'].'.selector',array('checked'=>false,'label'=>false))."</td>";
						//}
						$pageRow.="<td>";
							$pageRow.=$this->Form->input('User.'.$selectedUser['User']['id'].'.PendingInvoice.'.$invoice['Invoice']['id'].'.invoiceid',array('value'=>$invoice['Invoice']['id'],'class'=>'invoiceid','type'=>'hidden'));
							$pageRow.=$invoiceDateTime->format('d-m-Y');
						$pageRow.="</td>";
						$pageRow.="<td>".$this->Html->link($invoice['Invoice']['invoice_code'],array('action'=>'view',$invoice['Invoice']['id']))."</td>";
						if (!empty($invoice['InvoiceSalesOrder'])){
							$pageRow.="<td>";
							foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
								$pageRow.=$this->Html->link($invoiceSalesOrder['SalesOrder']['sales_order_code'], array('controller' => 'sales_orders', 'action' => 'view', $invoiceSalesOrder['SalesOrder']['id']));
								$pageRow.="<br/>";
							}
							$pageRow.="</td>";
						}
						else {
							$pageRow.="<td>-</td>";
						}
						$pageRow.="<td>".$this->Html->link($invoice['Client']['name'], array('controller' => 'clients', 'action' => 'view', $invoice['Client']['id']))."</td>";
						if ($currencyId==CURRENCY_USD){
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_subtotal'])."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".h($invoice['Invoice']['price_subtotal'])."</td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2)."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2)."</td>";
							}
						}
						else {
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2)."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2)."</td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_subtotal'])."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".h($invoice['Invoice']['price_subtotal'])."</td>";
							}
						}
						$pageRow.="<td class='hidden'><span class='currency'></span><span class='amountright'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.PendingInvoice.'.$invoice['Invoice']['id'].'.exchange_rate',array('value'=>$invoice['Invoice']['exchange_rate'],'class'=>'exchangerate','readonly'=>'readonly'))."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							//$pageRow.="<td class='percentage centered'><span class='amount'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.PendingInvoice.'.$invoice['Invoice']['id'].'.percentage_commission',array('value'=>round($invoice['Invoice']['percentage_commission']>0?$invoice['Invoice']['percentage_commission']:(100*$selectedUser['historical_performance']),2),'label'=>false,'class'=>'commissionpercentage','type'=>'decimal','style'=>'text-align:center;'))."</span></td>";
							//pr($invoice);
							$pageRow.="<td class='percentage centered'><span class='amount'>".$invoice['Invoice']['percentage_commission']." %</span></td>";
							//$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.PendingInvoice.'.$invoice['Invoice']['id'].'.amount_commission',array('default'=>$selectedUser['historical_performance']*$invoice['Invoice']['price_subtotal'],'value'=>$invoice['Invoice']['amount_commission'],'label'=>false,'readonly'=>'readonly','class'=>'commissionvalue','type'=>'decimal'))."</span></td>";
							$pageRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$invoice['Invoice']['amount_commission']."</span></td>";
						}
					$excelBody.="<tr>".$pageRow."</tr>";
					$pageBody.="<tr>".$pageRow."</tr>";
				}

				$pageTotalRow="";
				if ($currencyId==CURRENCY_CS){
					$classCurrency="CScurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total C$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$subTotalCS."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$commissionCS."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				if ($currencyId==CURRENCY_USD){
					$classCurrency="USDcurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total US$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$subTotalUSD."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$commissionCS."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
				$excelBody="<tbody>".$excelBody."</tbody>";
				
				$table_id=substr("recuperadas_vendedor_".$selectedUser['User']['username'],0,30);
				$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."' class='invoice previous'>".$pageHeader.$pageBody."</table>";
				echo "<h3>Facturas recuperadas para vendedor ".$selectedUser['User']['username']."</h3>";
				echo $pageOutput;
				
				$excelOutput.="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
			}
			else {
				echo "<h3>No hay facturas recuperadas  vendedor ".$selectedUser['User']['username']."</h3>";
			}
			
			//FOURTH SHOW THE PENDING INVOICES
			if (!empty($selectedUser['pendingInvoices'])){
				$pageHeader="";
				$excelHeader="";
				$pageHeader.="<thead>";
					$pageHeader.="<tr>";
						
						$pageHeader.="<th>".$this->Paginator->sort('invoice_date')."</th>";
						$pageHeader.="<th>".$this->Paginator->sort('invoice_code')."</th>";
						$pageHeader.="<th>".$this->Paginator->sort('SalesOrder.sales_order_code','Orden de Venta')."</th>";
						$pageHeader.="<th>".$this->Paginator->sort('Client.name')."</th>";
						$pageHeader.="<th class='centered'>".$this->Paginator->sort('price_subtotal')."</th>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$pageHeader.="<th class='centered'>Comisión %</th>";
							$pageHeader.="<th class='centered'>Comisión Total</th>";
						}
					$pageHeader.="</tr>";
				$pageHeader.="</thead>";
				$colSpanNumber=5;
				if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
					$colSpanNumber=7;
				}
				$excelHeader.="<thead>";				
					$excelHeader.="<tr><th colspan='".$colSpanNumber."' align='center'>".COMPANY_NAME."</th></tr>";	
					$excelHeader.="<tr><th colspan='".$colSpanNumber."' align='center'>".__('Facturas de Contado ')." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')." para ".$selectedUser['User']['username']."</th></tr>";
					$excelHeader.="<tr>";
						$excelHeader.="<th>Fecha</th>";
						$excelHeader.="<th>Factura</th>";
						$excelHeader.="<th>Orden de Venta</th>";
						$excelHeader.="<th>Cliente</th>";
						$excelHeader.="<th class='centered'>".$this->Paginator->sort('price_subtotal')."</th>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$excelHeader.="<th class='centered'>Comisión %</th>";
							$excelHeader.="<th class='centered'>Comisión Total</th>";
						}
					$excelHeader.="</tr>";
				$excelHeader.="</thead>";

				$pageBody="";
				$excelBody="";
				
				$subTotalCS=0;
				$commissionCS=0;
				$subTotalUSD=0;
				$commissionUSD=0;

				foreach ($selectedUser['pendingInvoices'] as $invoice){ 
					$invoiceDateTime=new DateTime($invoice['Invoice']['invoice_date']);
					if ($invoice['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$invoice['Invoice']['price_subtotal'];
						$commissionCS+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						//added calculation of totals in US$
						$subTotalUSD+=round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']/$invoice['Invoice']['exchange_rate'],2);
					}
					elseif ($invoice['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$invoice['Invoice']['price_subtotal'];
						$commissionUSD+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						//added calculation of totals in CS$
						$subTotalCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2);
						$commissionCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']*$invoice['Invoice']['exchange_rate'],2);
					}
					
					$pageRow="";
						//if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							//$pageRow.="<td class='selector'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.PendingInvoice.'.$invoice['Invoice']['id'].'.selector',array('checked'=>false,'label'=>false))."</td>";
						//}
						$pageRow.="<td>";
							$pageRow.=$this->Form->input('User.'.$selectedUser['User']['id'].'.PendingInvoice.'.$invoice['Invoice']['id'].'.invoiceid',array('value'=>$invoice['Invoice']['id'],'class'=>'invoiceid','type'=>'hidden'));
							$pageRow.=$invoiceDateTime->format('d-m-Y');
						$pageRow.="</td>";
						$pageRow.="<td>".$this->Html->link($invoice['Invoice']['invoice_code'],array('action'=>'view',$invoice['Invoice']['id']))."</td>";
						if(!empty($invoice['InvoiceSalesOrder'])){
							$pageRow.="<td>";
							foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
								$pageRow.=$this->Html->link($invoiceSalesOrder['SalesOrder']['sales_order_code'], array('controller' => 'sales_orders', 'action' => 'view', $invoiceSalesOrder['SalesOrder']['id']));
							}
							$pageRow.="</td>";
						}
						else {
							$pageRow.="<td>-</td>";
						}
						
						$pageRow.="<td>".$this->Html->link($invoice['Client']['name'], array('controller' => 'clients', 'action' => 'view', $invoice['Client']['id']))."</td>";
						if ($currencyId==CURRENCY_USD){
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_subtotal'])."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".h($invoice['Invoice']['price_subtotal'])."</td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2)."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2)."</td>";
							}
						}
						else {
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2)."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2)."</td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_subtotal'])."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".h($invoice['Invoice']['price_subtotal'])."</td>";
							}
						}
						$pageRow.="<td class='hidden'><span class='currency'></span><span class='amountright'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.PendingInvoice.'.$invoice['Invoice']['id'].'.exchange_rate',array('value'=>$invoice['Invoice']['exchange_rate'],'class'=>'exchangerate','readonly'=>'readonly'))."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							//$pageRow.="<td class='percentage centered'><span class='amount'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.PendingInvoice.'.$invoice['Invoice']['id'].'.percentage_commission',array('value'=>round($invoice['Invoice']['percentage_commission']>0?$invoice['Invoice']['percentage_commission']:(100*$selectedUser['historical_performance']),2),'label'=>false,'class'=>'commissionpercentage','type'=>'decimal','style'=>'text-align:center;'))."</span></td>";
							$pageRow.="<td class='percentage centered'><span class='amount'>".$invoice['Invoice']['percentage_commission']." %</span></td>";
							//$pageRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.PendingInvoice.'.$invoice['Invoice']['id'].'.amount_commission',array('default'=>$selectedUser['historical_performance']*$invoice['Invoice']['price_subtotal'],'value'=>$invoice['Invoice']['amount_commission'],'label'=>false,'readonly'=>'readonly','class'=>'commissionvalue','type'=>'decimal','style'=>'text-align:right;'))."</span></td>";
							$pageRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$invoice['Invoice']['amount_commission']."</span></td>";
						}
					$excelBody.="<tr>".$pageRow."</tr>";
					$pageBody.="<tr>".$pageRow."</tr>";
				}

				$pageTotalRow="";
				if ($currencyId==CURRENCY_CS){
					$classCurrency="CScurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total C$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$subTotalCS."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$commissionCS."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				if ($currencyId==CURRENCY_USD){
					$classCurrency="USDcurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total US$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$subTotalUSD."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$commissionCS."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				
				$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
				$excelBody="<tbody>".$excelBody."</tbody>";
				
				$table_id=substr("pendientes_vendedor_".$selectedUser['User']['username'],0,30);
				$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."' class='invoice previous'>".$pageHeader.$pageBody."</table>";
				echo "<h3>Facturas pendientes de recuperar para vendedor ".$selectedUser['User']['username']."</h3>";
				echo $pageOutput;
				
				$excelOutput.="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
			}
			else {
				echo "<h3>No hay facturas pendientes de recuperar para vendedor ".$selectedUser['User']['username']."</h3>";
			}
				
			echo "</fieldset>";
			echo $this->Form->end();
		}
	}
	$_SESSION['comisionesPorVendedor'] = $excelOutput;
?>
</div>