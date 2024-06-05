<script>
	$('body').on('change','.powerselectorcontado',function(e){
		if ($(this).is(':checked')){
			$(this).closest('fieldset').find('td.selectorcontado input').prop('checked',true);
			$(this).closest('fieldset').find('input.powerselectorcontado').prop('checked',true);
		}
		else {
			$(this).closest('fieldset').find('td.selectorcontado input').prop('checked',false);
			$(this).closest('fieldset').find('input.powerselectorcontado').prop('checked',false);
		}
	});
	$('body').on('change','.selectorcontado input',function(e){
		var currentrow=$(this).closest('tr');
		var selecteduserid=$(this).closest('fieldset').find('.selecteduserid').attr('id');
		if ($(this).is(':checked')){
			//var pendingamount=parseFloat(currentrow.find('.pendingamount span.amountright').text());
			var pendingamount=parseFloat(currentrow.find('.pendingamount input').val());
			currentrow.find('.commissionpaid').val(roundToTwo(pendingamount));
		}
		else {
			currentrow.find('.commissionpaid').val(0);
		}
		calculateTotal(selecteduserid);
	});
	$('body').on('change','.powerselectorrecuperada',function(e){
		if ($(this).is(':checked')){
			$(this).closest('fieldset').find('td.selectorrecuperada input').prop('checked',true);
			$(this).closest('fieldset').find('input.powerselectorrecuperada').prop('checked',true);
		}
		else {
			$(this).closest('fieldset').find('td.selectorrecuperada input').prop('checked',false);
			$(this).closest('fieldset').find('input.powerselectorrecuperada').prop('checked',false);
		}
	});
	$('body').on('change','.selectorrecuperada input',function(e){
		var currentrow=$(this).closest('tr');
		var selecteduserid=$(this).closest('fieldset').find('.selecteduserid').attr('id');
		if ($(this).is(':checked')){
			//var pendingamount=parseFloat(currentrow.find('.pendingamount span.amountright').text());
			var pendingamount=parseFloat(currentrow.find('.pendingamount input').val());
			currentrow.find('.commissionpaid').val(roundToTwo(pendingamount));
		}
		else {
			currentrow.find('.commissionpaid').val(0);
		}
		calculateTotal(selecteduserid);
	});
	$('body').on('change','.commissionpaid',function(){
		var selecteduserid=$(this).closest('fieldset').find('.selecteduserid').attr('id');
		calculateTotal(selecteduserid);
	});
	function calculateTotal(selecteduserid){
		var totalpaymentcontado=0;
		var cashinvoicetable=$('#'+selecteduserid).closest('fieldset').find('table.contado'); 
		cashinvoicetable.find('.commissionpaid').each(function(){
			var paymentamount=parseFloat($(this).val());
			totalpaymentcontado+=paymentamount;
		});
		cashinvoicetable.find('tr.totalrow td.payment span.amountright').text(roundToTwo(totalpaymentcontado));
		
		var totalpaymentrecuperada=0;
		var recupinvoicetable=$('#'+selecteduserid).closest('fieldset').find('table.recuperada'); 
		recupinvoicetable.find('.commissionpaid').each(function(){
			var paymentamount=parseFloat($(this).val());
			totalpaymentrecuperada+=paymentamount;
		});
		recupinvoicetable.find('tr.totalrow td.payment span.amountright').text(roundToTwo(totalpaymentrecuperada));
		
		var overviewtable=$('#'+selecteduserid).closest('fieldset').find('table.resumen'); 
		overviewtable.find('tr.contado td.amount span.amountright').text(roundToTwo(totalpaymentcontado));
		overviewtable.find('tr.recuperada td.amount span.amountright').text(roundToTwo(totalpaymentrecuperada));
		overviewtable.find('tr.total td.amount span.amountright').text(roundToTwo(totalpaymentcontado)+roundToTwo(totalpaymentrecuperada));
	};
	
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
		formatCSCurrencies();
		formatUSDCurrencies();
		
		$('fieldset').each(function(){
			var selecteduserid=$(this).find('.selecteduserid').attr('id');
			calculateTotal(selecteduserid);
		});
	});
</script>
<div class="vendorCommissionPayments resumenPagosPendientes fullwidth">
<?php 
	echo "<h2>".__('Comisiones por Pagar')." ".$monthName."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo "<div class='container-fluid'>";
				echo "<div class='row'>";
					echo "<div class='col-md-5'>";
						echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate));
						echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate));
						echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
						echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
					echo "</div>";
					echo "<div class='col-md-4'>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT) { 
							echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Usuario'),'options'=>$users,'default'=>$userId,'empty'=>array('0'=>__('Todos Usuarios'))));
						}
						else {
							echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Usuario'),'options'=>$users,'default'=>$userId));
						}
						echo $this->Form->submit(__('Refresh'),array('id'=>'refresh','name'=>'refresh'));
					echo "</div>";
				echo "</div>";					
			echo "</div>";
		echo "</fieldset>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumenPagosPendientes'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
/*
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('New Vendor Commission Payment'), array('action' => 'add'))."</li>";
		if ($bool_invoice_index_permission){
			echo "<br/>";
			echo "<li>".$this->Html->link(__('List Invoices'), array('controller' => 'invoices', 'action' => 'index'))."</li>";
		}
		if ($bool_invoice_add_permission){
			echo "<li>".$this->Html->link(__('New Invoice'), array('controller' => 'invoices', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
*/
?>
</div>
<div>
<?php
	$excelOutput="";
	$startDateTime=new DateTime($startDate);
	$endDateTime=new DateTime($endDate);
	foreach($selectedUsers as $selectedUser){
		$selectedUserId=$selectedUser['User']['id'];
		if (!empty($selectedUser['cashInvoices'])||!empty($selectedUser['paidInvoicesPreviousPeriod'])){
			echo "<h2>Pagos de comisiones pendientes  para vendedor ".$selectedUser['User']['username']." ".$monthName."</h2>";
			echo $this->Form->create('User'.$selectedUserId); 
			if (!empty($selectedUser['cashInvoices'])){
				echo "<h3>Facturas de Contado de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</h3>";
				echo "<fieldset>";
					echo $this->Form->input('User.'.$selectedUserId.'.selected_user_id',array('type'=>'hidden','value'=>$selectedUserId,'id'=>$selectedUserId,'class'=>'selecteduserid'));
					echo $this->Form->input('User.'.$selectedUserId.'.payment_date',array('type'=>'date','dateFormat'=>'DMY'));
					echo $this->Form->input('User.'.$selectedUserId.'.powerselector1',array('class'=>'powerselectorcontado','checked'=>true,'style'=>'width:5em;','label'=>array('text'=>'Seleccionar/Deseleccionar facturas de contado para usuario','style'=>'padding-left:5em;'),'format' => array('before', 'input', 'between', 'label', 'after', 'error' )));
					$pageHeader="<thead>";
						$pageHeader.="<tr>";
							$pageHeader.="<th>Seleccione</th>";
							$pageHeader.="<th>".$this->Paginator->sort('invoice_code')."</th>";
							$pageHeader.="<th class='centered'>".$this->Paginator->sort('percentage_commission')."</th>";
							$pageHeader.="<th class='centered'>".$this->Paginator->sort('amount_commission')."</th>";
							$pageHeader.="<th class='centered'>".$this->Paginator->sort('already_paid')."</th>";
							$pageHeader.="<th class='centered'>".$this->Paginator->sort('remaining_saldo')."</th>";
							$pageHeader.="<th class='centered'>".$this->Paginator->sort('commission_paid')."</th>";
						$pageHeader.="</tr>";
					$pageHeader.="</thead>";
					$excelHeader="<thead>";
						$excelHeader.="<tr>";
							$excelHeader.="<th>".$this->Paginator->sort('invoice_code')."</th>";
							$excelHeader.="<th>".$this->Paginator->sort('percentage_commission')."</th>";
							$excelHeader.="<th>".$this->Paginator->sort('amount_commission','Monto Comisión')."</th>";
							$excelHeader.="<th>".$this->Paginator->sort('already_paid')."</th>";
							$excelHeader.="<th>".$this->Paginator->sort('remaining_saldo')."</th>";
							$excelHeader.="<th>".$this->Paginator->sort('commission_paid')."</th>";
						$excelHeader.="</tr>";
					$excelHeader.="</thead>";

					$pageBody="";
					$excelBody="";
					
					$totalCommission=0;
					$totalPaidAlready=0;
					$totalRemainingSaldo=0;

					foreach ($selectedUser['cashInvoices'] as $invoice){ 
						$totalCommission+=$invoice['Invoice']['amount_commission'];
						$totalPaidAlready+=$invoice['Invoice']['already_paid'];
						$totalRemainingSaldo+=$invoice['Invoice']['remaining_saldo'];
						//pr($pendingInvoice);
						$pageRow="";
							$pageRow.="<td>";
								$pageRow.=$this->Form->input('User.'.$selectedUserId.'.Invoice.'.$invoice['Invoice']['id'].'.invoiceid',array('value'=>$invoice['Invoice']['id'],'class'=>'invoiceid','type'=>'hidden'));
								$pageRow.=$this->Html->link($invoice['Invoice']['invoice_code'], array('controller' => 'invoices', 'action' => 'view', $invoice['Invoice']['id']));
							$pageRow.="</td>";
							$pageRow.="<td class='percentage centered'><span class='amount'>".h($invoice['Invoice']['percentage_commission'])." %</span></td>";
							$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['amount_commission'])."</span></td>";
								$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['already_paid'])."</span></td>";
							$pageRow.="<td class='pendingamount CScurrency'>";
								$pageRow.="<span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['remaining_saldo'])."</span>";
								$pageRow.=$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.pendingamount',array('type'=>'hidden','value'=>$invoice['Invoice']['remaining_saldo']));
							$pageRow.="</td>";
							if ($invoice['Invoice']['remaining_saldo']>0.001){
								$pageRow.="<td class='payment CScurrency'><span class='currency'></span><span class='amountright'>".$this->Form->input('User.'.$selectedUserId.'.Invoice.'.$invoice['Invoice']['id'].'.commission_paid',array('label'=>false,'value'=>$invoice['Invoice']['remaining_saldo'],'class'=>'commissionpaid','type'=>'decimal'))."</span></td>";
							}
							else {
								$pageRow.="<td><span class='currency'></span><span class='amountright'>".$invoice['Invoice']['already_paid']."</span></td>";
							}
						$excelBody.="<tr>".$pageRow."</tr>";
						if ($invoice['Invoice']['remaining_saldo']>0.001){
							$pageRow="<td class='selectorcontado'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.selector',array('checked'=>true,'label'=>false))."</td>".$pageRow;
						}
						else {
							$pageRow="<td>".((new DateTime($invoice['Invoice']['payment_date']))->format('d-m-Y'))."</td>".$pageRow;
						}						
						$pageBody.="<tr>".$pageRow."</tr>";
					}

					$pageTotalRow="";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$totalCommission."</span></td>";
						$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$totalPaidAlready."</span></td>";
						$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$totalRemainingSaldo."</span></td>";
						$pageTotalRow.="<td class='payment CScurrency'><span class='currency'></span><span class='amountright'>0</span></td>";
					$pageTotalRow.="</tr>";

					$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
					$table_id=substr("facturas_contado_".$selectedUser['User']['username'],0,30);
					$pageOutput="<table cellpadding='0' cellspacing='0' class='contado' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
					
					echo $pageOutput;
					echo $this->Form->input('User.'.$selectedUserId.'.powerselector2',array('class'=>'powerselectorcontado','checked'=>true,'style'=>'width:5em;','label'=>array('text'=>'Seleccionar/Deseleccionar facturas de contado para usuario','style'=>'padding-left:5em;'),'format' => array('before', 'input', 'between', 'label', 'after', 'error' )));
					$excelOutput.="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
				}
				
				if (!empty($selectedUser['paidInvoicesPreviousPeriod'])){
					echo "<h3>Facturas de Crédito recuperadas de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</h3>";
				
					echo $this->Form->input('User.'.$selectedUserId.'.powerselector3',array('class'=>'powerselectorrecuperada','checked'=>true,'style'=>'width:5em;','label'=>array('text'=>'Seleccionar/Deseleccionar facturas recuperadas para usuario','style'=>'padding-left:5em;'),'format' => array('before', 'input', 'between', 'label', 'after', 'error' )));
					$pageHeader="<thead>";
						$pageHeader.="<tr>";
							$pageHeader.="<th>Seleccione</th>";
							$pageHeader.="<th>".$this->Paginator->sort('invoice_code')."</th>";
							$pageHeader.="<th class='centered'>".$this->Paginator->sort('percentage_commission')."</th>";
							$pageHeader.="<th class='centered'>".$this->Paginator->sort('amount_commission')."</th>";
							$pageHeader.="<th class='centered'>".$this->Paginator->sort('already_paid')."</th>";
							$pageHeader.="<th class='centered'>".$this->Paginator->sort('remaining_saldo')."</th>";
							$pageHeader.="<th class='centered'>".$this->Paginator->sort('commission_paid')."</th>";
						$pageHeader.="</tr>";
					$pageHeader.="</thead>";
					$excelHeader="<thead>";
						$excelHeader.="<tr>";
							$excelHeader.="<th>".$this->Paginator->sort('invoice_code')."</th>";
							$excelHeader.="<th>".$this->Paginator->sort('percentage_commission')."</th>";
							$excelHeader.="<th>".$this->Paginator->sort('amount_commission','Monto Comisión')."</th>";
							$excelHeader.="<th>".$this->Paginator->sort('already_paid')."</th>";
							$excelHeader.="<th>".$this->Paginator->sort('remaining_saldo')."</th>";
							$excelHeader.="<th>".$this->Paginator->sort('commission_paid')."</th>";
						$excelHeader.="</tr>";
					$excelHeader.="</thead>";

				$pageBody="";
				$excelBody="";
			
				$totalCommission=0;
				$totalPaidAlready=0;
				$totalRemainingSaldo=0;

				foreach ($selectedUser['paidInvoicesPreviousPeriod'] as $invoice){ 
					$totalCommission+=$invoice['Invoice']['amount_commission'];
					$totalPaidAlready+=$invoice['Invoice']['already_paid'];
					$totalRemainingSaldo+=$invoice['Invoice']['remaining_saldo'];
					//pr($pendingInvoice);
					$pageRow="";
						$pageRow.="<td>";
							$pageRow.=$this->Form->input('User.'.$selectedUserId.'.Invoice.'.$invoice['Invoice']['id'].'.invoiceid',array('value'=>$invoice['Invoice']['id'],'class'=>'invoiceid','type'=>'hidden'));
							$pageRow.=$this->Html->link($invoice['Invoice']['invoice_code'], array('controller' => 'invoices', 'action' => 'view', $invoice['Invoice']['id']));
						$pageRow.="</td>";
						$pageRow.="<td class='percentage centered'><span class='amount'>".h($invoice['Invoice']['percentage_commission'])." %</span></td>";
						$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['amount_commission'])."</span></td>";
						$pageRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['already_paid'])."</span></td>";
						$pageRow.="<td class='pendingamount CScurrency'>";
							$pageRow.="<span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['remaining_saldo'])."</span>";
							$pageRow.=$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.pendingamount',array('type'=>'hidden','value'=>$invoice['Invoice']['remaining_saldo']));
						$pageRow.="</td>";
						if ($invoice['Invoice']['remaining_saldo']>0.001){
							$pageRow.="<td class='payment CScurrency'><span class='currency'></span><span class='amountright'>".$this->Form->input('User.'.$selectedUserId.'.Invoice.'.$invoice['Invoice']['id'].'.commission_paid',array('label'=>false,'value'=>$invoice['Invoice']['remaining_saldo'],'class'=>'commissionpaid','type'=>'decimal'))."</span></td>";
						}
						else {
							$pageRow.="<td><span class='currency'></span><span class='amountright'>".$invoice['Invoice']['already_paid']."</span></td>";
						}
					$excelBody.="<tr>".$pageRow."</tr>";
					if ($invoice['Invoice']['remaining_saldo']>0.001){
						$pageRow="<td class='selectorrecuperada'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.Invoice.'.$invoice['Invoice']['id'].'.selector',array('checked'=>true,'label'=>false))."</td>".$pageRow;
					}
					else {
						$pageRow="<td>".((new DateTime($invoice['Invoice']['payment_date']))->format('d-m-Y'))."</td>".$pageRow;
					}
					$pageBody.="<tr>".$pageRow."</tr>";
				}

				$pageTotalRow="";
				$pageTotalRow.="<tr class='totalrow'>";
					$pageTotalRow.="<td></td>";
					$pageTotalRow.="<td></td>";
					$pageTotalRow.="<td></td>";
					$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$totalCommission."</span></td>";
					$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$totalPaidAlready."</span></td>";
					$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$totalRemainingSaldo."</span></td>";
					$pageTotalRow.="<td class='payment CScurrency'><span class='currency'></span><span class='amountright'>0</span></td>";
				$pageTotalRow.="</tr>";

				$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
				$table_id=substr("facturas_contado_".$selectedUser['User']['username'],0,30);
				$pageOutput="<table cellpadding='0' cellspacing='0' class='recuperada' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
				
				echo $pageOutput;
				echo $this->Form->input('User.'.$selectedUserId.'.powerselector4',array('class'=>'powerselectorrecuperada','checked'=>true,'style'=>'width:5em;','label'=>array('text'=>'Seleccionar/Deseleccionar facturas recuperadas para usuario','style'=>'padding-left:5em;'),'format' => array('before', 'input', 'between', 'label', 'after', 'error' )));
				$excelOutput.="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
			}
			$overviewTable="";
			$overviewTable.="<table class='resumen'>";
				$overviewTable.="<tbody>";
					$overviewTable.="<tr class='contado'>";
						$overviewTable.="<td>FACTURAS DE CONTADO ".$monthName."</td>";
						$overviewTable.="<td class='amount'><span class='currency'></span><span class='amountright'>0</td>";
					$overviewTable.="</tr>";
					$overviewTable.="<tr class='recuperada'>";
						$overviewTable.="<td>FACTURAS RECUPERADAS ".$monthName."</td>";
						$overviewTable.="<td class='amount'><span class='currency'></span><span class='amountright'>0</td>";
					$overviewTable.="</tr>";
					$overviewTable.="<tr class='total'>";
						$overviewTable.="<td>TOTAL A PAGAR</td>";
						$overviewTable.="<td class='amount'><span class='currency'></span><span class='amountright'>0</td>";
					$overviewTable.="</tr>";
				$overviewTable.="</tbody>";
			$overviewTable.="</table>";
			echo $overviewTable;
			
			echo $this->Form->submit('Guardar pagos a vendedor '.$selectedUser['User']['username'],array('id'=>'User.'.$selectedUserId.'.save','name'=>'User.'.$selectedUserId.'.save','style'=>'width:400px;'))."</td>";
			
			//SHOW THE PENDING INVOICES
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
						if ($userRoleId==ROLE_ADMIN){
							$pageHeader.="<th class='centered'>Comisión %</th>";
							$pageHeader.="<th class='centered'>Comisión Total</th>";
						}
					$pageHeader.="</tr>";
				$pageHeader.="</thead>";
				$colSpanNumber=5;
				if ($userRoleId==ROLE_ADMIN){
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
						if ($userRoleId==ROLE_ADMIN){
							$excelHeader.="<th class='centered'>Comisión %</th>";
							$excelHeader.="<th class='centered'>Comisión Total</th>";
						}
					$excelHeader.="</tr>";
				$excelHeader.="</thead>";

				$pageBody="";
				$excelBody="";
				
				$subTotalCS=0;
				$commissionCS=0;
				//$subTotalUSD=0;
				//$commissionUSD=0;

				foreach ($selectedUser['pendingInvoices'] as $invoice){ 
					$invoiceDateTime=new DateTime($invoice['Invoice']['invoice_date']);
					if ($invoice['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$invoice['Invoice']['price_subtotal'];
						$commissionCS+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						//added calculation of totals in US$
						//$subTotalUSD+=round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2);
						//$commissionUSD+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']/$invoice['Invoice']['exchange_rate'],2);
					}
					elseif ($invoice['Currency']['id']==CURRENCY_USD){
						//$subTotalUSD+=$invoice['Invoice']['price_subtotal'];
						//$commissionUSD+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						//added calculation of totals in CS$
						$subTotalCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2);
						$commissionCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']*$invoice['Invoice']['exchange_rate'],2);
					}
				
					$pageRow="";
						//if ($userRoleId==ROLE_ADMIN){
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
						// 20160901 SE ASUME QUE TODOS LOS PAGOS DE COMISIONES DE VENDEDOR SE REALIZAN EN DÓLARES
						//if ($currencyId==CURRENCY_USD){
						//	if ($invoice['Currency']['id']==CURRENCY_USD){
						//		$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_subtotal'])."</span></td>";
						//		$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".h($invoice['Invoice']['price_subtotal'])."</td>";
						//	}
						//	else {
						//		$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2)."</span></td>";
						//		$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2)."</td>";
						//	}
						//}
						//else {
							$currencyClass="CScurrency";
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2)."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2)."</td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_subtotal'])."</span></td>";
								$pageRow.="<td class='subtotal hidden ".$currencyClass."'>".h($invoice['Invoice']['price_subtotal'])."</td>";
							}
						//}
						$pageRow.="<td class='hidden'><span class='currency'></span><span class='amountright'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.PendingInvoice.'.$invoice['Invoice']['id'].'.exchange_rate',array('value'=>$invoice['Invoice']['exchange_rate'],'class'=>'exchangerate','readonly'=>'readonly'))."</span></td>";
						if ($userRoleId==ROLE_ADMIN){
							//$pageRow.="<td class='percentage centered'><span class='amount'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.PendingInvoice.'.$invoice['Invoice']['id'].'.percentage_commission',array('value'=>round($invoice['Invoice']['percentage_commission']>0?$invoice['Invoice']['percentage_commission']:(100*$selectedUser['historical_performance']),2),'label'=>false,'class'=>'commissionpercentage','type'=>'decimal','style'=>'text-align:center;'))."</span></td>";
							$pageRow.="<td class='percentage centered'><span class='amount'>".$invoice['Invoice']['percentage_commission']." %</span></td>";
							//$pageRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$this->Form->input('User.'.$selectedUser['User']['id'].'.PendingInvoice.'.$invoice['Invoice']['id'].'.amount_commission',array('default'=>$selectedUser['historical_performance']*$invoice['Invoice']['price_subtotal'],'value'=>$invoice['Invoice']['amount_commission'],'label'=>false,'readonly'=>'readonly','class'=>'commissionvalue','type'=>'decimal','style'=>'text-align:right;'))."</span></td>";
							$pageRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$invoice['Invoice']['amount_commission']."</span></td>";
						}
					$excelBody.="<tr>".$pageRow."</tr>";
					$pageBody.="<tr>".$pageRow."</tr>";
				}

				$pageTotalRow="";
				//if ($currencyId==CURRENCY_CS){
					$classCurrency="CScurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total C$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$subTotalCS."</span></td>";
						if ($userRoleId==ROLE_ADMIN){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$commissionCS."</span></td>";
						}
					$pageTotalRow.="</tr>";
				//}
				//if ($currencyId==CURRENCY_USD){
				//	$classCurrency="USDcurrency";
				//	$pageTotalRow.="<tr class='totalrow'>";
				//		$pageTotalRow.="<td>Total US$</td>";
				//		$pageTotalRow.="<td></td>";
				//		$pageTotalRow.="<td></td>";
				//		$pageTotalRow.="<td></td>";
				//		$pageTotalRow.="<td class='".$classCurrency."'><span class='currency'></span><span class='amountright'>".$subTotalUSD."</span></td>";
				//		if ($userRoleId==ROLE_ADMIN){						
				//			$pageTotalRow.="<td></td>";
				//			$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'></span><span class='amountright'>".$commissionCS."</span></td>";
				//		}
				//	$pageTotalRow.="</tr>";
				//}
			
				$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
				$excelBody="<tbody>".$excelBody."</tbody>";
			
				$table_id=substr("pendientes_vendedor_".$selectedUser['User']['username'],0,30);
				$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."' class='invoice previous'>".$pageHeader.$pageBody."</table>";
				echo "<h3>Facturas pendientes de recuperar para vendedor ".$selectedUser['User']['username']."</h3>";
				echo $pageOutput;
				
				$excelOutput.="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
			}
			echo "</fieldset>";
			echo $this->Form->end();
		}
		else {
			echo "<h3>No hay pagos de comisiones pendientes para vendedor ".$selectedUser['User']['username']."</h3>";
		}
	}
	$_SESSION['resumenPagosPendientes'] = $excelOutput;
?>
</div>
