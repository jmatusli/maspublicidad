<div class="sales index fullwidth">
<?php	
	echo "<h2>".__('Reporte de Facturas por Ejecutivo de Venta')."</h2>";
	
	echo $this->Form->create('Report'); 
		echo "<fieldset>";
			echo $this->Form->input('Report.startdate',['type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015,'maxYear'=>date('Y')]);
			echo $this->Form->input('Report.enddate',['type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015,'maxYear'=>date('Y')]);
			echo "<br/>";			
			if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) { 
        echo $this->Form->input('Report.user_id',['label'=>'Mostrar Usuario','options'=>$users,'default'=>$userId,'empty'=>['0'=>'Todos Usuarios']]);
      }
      else {
        echo $this->Form->input('Report.user_id',['label'=>'Mostrar Usuario','options'=>$users,'default'=>$userId,'type'=>'hidden']);
      }
      echo $this->Form->input('Report.currency_id',['label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId]);
		echo "</fieldset>";
		echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
		echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
	echo "<br/>";
	echo $this->Form->end(__('Refresh')); 
	
	echo $this->Html->link(__('Guardar como Excel'), ['action' => 'guardarReporteFacturasPorEjecutivo'], ['class' => 'btn btn-primary']); 
	
	$startDateTime=new DateTime($startDate);
	$endDateTime=new DateTime($endDate);
	
	$output="";
	$excel="";
	
	foreach ($selectedUsers as $user){
		if (!empty($user['Invoices'])){
			echo "<h3>Facturas para Ejecutivo de Venta ".$this->Html->Link($user['User']['username'],array('controller'=>'users','action'=>'view',$user['User']['id']))."</h3>";
			$outputhead="<thead>";
				$outputhead.="<tr>";
					$outputhead.="<th>".__('Date')."</th>";
					$outputhead.="<th>".__('Invoice Code')."</th>";
					$outputhead.="<th>".__('Client')."</th>";
					//$outputhead.="<th>".__('Contact')."</th>";
					//$outputhead.="<th class='right'>".__('Subtotal')."</th>";
					//$outputhead.="<th class='right'>".__('IVA')."</th>";
					$outputhead.="<th class='right'>".__('Total')."</th>";
					//$outputhead.="<th>".__('Caído')."</th>";
					//$outputhead.="<th>".__('Vendido')."</th>";
				$outputhead.="</tr>";
			$outputhead.="</thead>";
		
			$excelhead="<thead>";
				$excelhead.="<tr><th colspan='4' align='center'>".COMPANY_NAME."</th></tr>";	
				$excelhead.="<tr><th colspan='4' align='center'>".__('Reporte Facturas por Ejecutivo')." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</th></tr>";
				$excelhead.="<tr>";
					$excelhead.="<th>".__('Date')."</th>";
					$excelhead.="<th>".__('Invoice Code')."</th>";
					$excelhead.="<th>".__('Client')."</th>";
					//$excelhead.="<th>".__('Contact')."</th>";
					//$excelhead.="<th class='centered'>".__('Subtotal')."</th>";
					//$excelhead.="<th class='centered'>".__('IVA')."</th>";
					$excelhead.="<th class='centered'>".__('Total')."</th>";
					//$excelhead.="<th>".__('Caído')."</th>";
					//$excelhead.="<th>".__('Vendido')."</th>";
				$excelhead.="</tr>";
			$excelhead.="</thead>";
		
			$subtotalCS=0;
			$ivaCS=0;
			$totalCS=0;
			
			$subtotalUSD=0;
			$ivaUSD=0;
			$totalUSD=0;
			
			$totalDropped=0;
			$totalSold=0;
		
			$bodyRows="";
			foreach ($user['Invoices'] as $invoice){
				//pr($invoice);
				$invoiceDate=new DateTime($invoice['Invoice']['invoice_date']);
				
				$currencyClass="";
				
				if ($invoice['Currency']['id']==CURRENCY_CS){
					$currencyClass=" class='CScurrency'";
					$subtotalCS+=$invoice['Invoice']['price_subtotal'];
					//$ivaCS+=$invoice['Invoice']['price_iva'];
					//$totalCS+=$invoice['Invoice']['price_total'];
					
					//added calculation of totals in US$
					$subtotalUSD+=round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2);
					//$ivaUSD+=round($invoice['Invoice']['price_iva']/$invoice['Invoice']['exchange_rate'],2);
					//$totalUSD+=round($invoice['Invoice']['price_total']/$invoice['Invoice']['exchange_rate'],2);
				}
				elseif ($invoice['Currency']['id']==CURRENCY_USD){
					$currencyClass=" class='USDcurrency'";
					$subtotalUSD+=$invoice['Invoice']['price_subtotal'];
					//$ivaUSD+=$invoice['Invoice']['price_iva'];
					//$totalUSD+=$invoice['Invoice']['price_total'];
					
					//added calculation of totals in CS$
					$subtotalCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2);
					//$ivaCS+=round($invoice['Invoice']['price_iva']*$invoice['Invoice']['exchange_rate'],2);
					//$totalCS+=round($invoice['Invoice']['price_total']*$invoice['Invoice']['exchange_rate'],2);
				}
				//$totalDropped+=$invoice['Invoice']['dropped'];
				//$totalSold+=$invoice['Invoice']['sold'];
				
					$bodyRows.="<tr>";
					$bodyRows.="<td>".$invoiceDate->format('d-m-Y')."</td>";	
					$bodyRows.="<td>".$this->Html->Link($invoice['Invoice']['invoice_code'],['controller'=>'invoices','action'=>'detalle',$invoice['Invoice']['id']],['target'=>'_blank'])."</td>";	
					$bodyRows.="<td>".($invoice['Client']['bool_generic']?($invoice['Invoice']['client_name']." (".$invoice['Client']['name'].")"):$this->Html->Link($invoice['Client']['name'],['controller'=>'clients','action'=>'view',$invoice['Client']['id']],['target'=>'_blank']))."</td>";
					//$bodyRows.="<td>".$this->Html->Link($invoice['Contact']['fullname'],array('controller'=>'contacts','action'=>'view',$invoice['Contact']['id']),array('target'=>'_blank'))."</td>";
					$bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$invoice['Invoice']['price_subtotal']."</span></td>";
					//$bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$invoice['Invoice']['price_iva']."</span></td>";
					//$bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$invoice['Invoice']['price_total']."</span></td>";
					//$bodyRows.="<td class='percentage'><span class='amountright'>".$invoice['Invoice']['dropped']."</span></td>";
					//$bodyRows.="<td class='percentage'><span class='amountright'>".$invoice['Invoice']['sold']."</span></td>";
					
				$bodyRows.="</tr>";
			}
			$totalRows="";
			if ($currencyId==CURRENCY_CS){
				$totalRows.="<tr class='totalrow'>";
					$totalRows.="<td>Total C$</td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td class='CScurrency'><span class='amountright'>".$subtotalCS."</span></td>";
					//$totalRows.="<td class='CScurrency'><span class='amountright'>".$ivaCS."</span></td>";
					//$totalRows.="<td class='CScurrency'><span class='amountright'>".$totalCS."</span></td>";
					//$totalRows.="<td class='percentage'><span class='amountright'>".($totalDropped/count($user['Quotations']))."</span></td>";
					//$totalRows.="<td class='percentage'><span class='amountright'>".($totalSold/count($user['Quotations']))."</span></td>";
				$totalRows.="</tr>";
			}
			if ($currencyId==CURRENCY_USD){
				$totalRows.="<tr class='totalrow'>";
					$totalRows.="<td>Total US$</td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td class='USDcurrency'><span class='amountright'>".$subtotalUSD."</span></td>";
					//$totalRows.="<td class='USDcurrency'><span class='amountright'>".$ivaUSD."</span></td>";
					//$totalRows.="<td class='USDcurrency'><span class='amountright'>".$totalUSD."</span></td>";
					//$totalRows.="<td class='percentage'><span class='amountright'>".($totalDropped/count($user['Quotations']))."</span></td>";
					//$totalRows.="<td class='percentage'><span class='amountright'>".($totalSold/count($user['Quotations']))."</span></td>";
				$totalRows.="</tr>";
			}
			$body="<tbody>".$totalRows.$bodyRows.$totalRows."</tbody>";

			$table_id=substr("vendedor_".trim($user['User']['username']),0,30);
			echo "<table id='".$table_id."'>".$outputhead.$body."</table>";
			$excel.="<table id='".$table_id."'>".$excelhead.$body."</table>";
		}
	}
	$_SESSION['reporteCotizacionesPorEjecutivo'] = $excel;
?>
</div>
<script>
	function formatNumbers(){
		$("td.number").each(function(){
			$(this).number(true,0);
		});
	}
	function formatPercentages(){
		$("td.percentage span.amountright").each(function(){
			$(this).number(true,0);
			$(this).append(" %");
		});
	}
	
	function formatCSCurrencies(){
		$("td.CScurrency span.amountright").each(function(){
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,2);
			$(this).parent().prepend("C$ ");
		});
	}
	
	function formatUSDCurrencies(){
		$("td.USDcurrency span.amountright").each(function(){
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,2);
			$(this).parent().prepend("US$ ");
		});
	}
	
	$(document).ready(function(){
		formatNumbers();
		formatPercentages();
		formatCSCurrencies();
		formatUSDCurrencies();
	});
	
</script>
