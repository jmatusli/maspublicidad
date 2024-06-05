<div class="sales index" style='width:100%;'>
<?php	
	$excel="";
	
	echo "<h2>".__('Reporte de Cotizaciones por Cliente')."</h2>";
	echo "<div class='container-fluid'>";
		echo "<div class='rows'>";
			echo "<div class='col-md-6'>";
				echo $this->Form->create('Report'); 
					echo "<fieldset>";
						echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015,'maxYear'=>date('Y')));
						echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015,'maxYear'=>date('Y')));
						echo "<br/>";			
						echo $this->Form->input('Report.currency_id',array('label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId));
						echo "<br/>";			
						echo $this->Form->input('Report.client_id',['label'=>__('Cliente'),'default'=>$clientId,'empty'=>['0'=>'Seleccione Cliente']]);
            if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) { 
              echo $this->Form->input('Report.user_id',['label'=>'Mostrar Usuario','options'=>$users,'default'=>$userId,'empty'=>['0'=>'Todos Usuarios']]);
            }
            else {
              echo $this->Form->input('Report.user_id',['label'=>'Mostrar Usuario','options'=>$users,'default'=>$userId,'type'=>'hidden']);
            }
					echo "</fieldset>";
					echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
					echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";	
					echo "<br/>";
				echo $this->Form->end(__('Refresh'));
				echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarReporteCotizacionesPorCliente'), array( 'class' => 'btn btn-primary')); 
			echo "</div>";
			echo "<div class='col-md-6'>";
				echo "<h3>Resumen Totales por Cliente</h3>";
				$overviewTable="";
				
				$overviewTable.="<table id='resumen_cotizaciones_cliente'>";
					$overviewTable.="<thead>";
						$overviewTable.="<tr>";
							$overviewTable.="<th>Cliente</th>";
							$overviewTable.="<th>Precio Subtotal</th>";
						$overviewTable.="</tr>";
					$overviewTable.="</thead>";
					$overviewTable.="<tbody>";
					$overviewTableBody="";
					$totalCS=0;
					$totalUSD=0;						
					foreach ($selectedClients as $client){
						if ($client['subtotal_price_USD']>0){
							$overviewTableBody.="<tr>";
								$overviewTableBody.="<td>".$client['Client']['name']."</td>";								
									switch ($currencyId){
										case CURRENCY_CS:
											$overviewTableBody.="<td class='CScurrency'><span class='amountright'>".$client['subtotal_price_CS']."</span></td>";
											$totalCS+=$client['subtotal_price_CS'];
											break;
										case CURRENCY_USD:
											
											$overviewTableBody.="<td class='USDcurrency'><span class='amountright'>".$client['subtotal_price_USD']."</span></td>";
											$totalUSD+=$client['subtotal_price_USD'];
											break;
									}
							$overviewTableBody.="</tr>";
						}
					}
						$totalRow="";
						$totalRow.="<tr class='totalrow'>";
							$totalRow.="<td>Total</td>";
							switch ($currencyId){
								case CURRENCY_CS:
									$totalRow.="<td class='CScurrency'><span class='amountright'>".$totalCS."</span></td>";
									break;
								case CURRENCY_USD:
									$totalRow.="<td class='USDcurrency'><span class='amountright'>".$totalUSD."</span></td>";
									break;
							}
						$totalRow.="</tr>";
						$overviewTable.=$totalRow.$overviewTableBody.$totalRow;
					$overviewTable.="</tbody>";
				$overviewTable.="</table>";
				
				echo $overviewTable;
				$excel.=$overviewTable;
			echo "</div>";
		echo "</div>";
	echo "</div>";
	$startDateTime=new DateTime($startDate);
	$endDateTime=new DateTime($endDate);
	
	$output="";
	foreach ($selectedClients as $client){
		$showClient=true;
		if (!empty($client_id)){
			if ($client['Client']['id']!=$client_id){
				$showClient=false;
			}
		}
		if (!empty($client['Quotations'])&&$showClient){
			echo "<h3>Cotizaciones para Cliente ".$this->Html->Link($client['Client']['name'],array('controller'=>'clients','action'=>'view',$client['Client']['id']))."</h3>";
			$outputhead="<thead>";
				$outputhead.="<tr>";
					$outputhead.="<th>".__('Date')."</th>";
					$outputhead.="<th>".__('Quotation Code')."</th>";
					$outputhead.="<th>".__('Orden de Venta')."</th>";
					$outputhead.="<th>".__('Vendedor')."</th>";
					$outputhead.="<th>".__('Contact')."</th>";
					$outputhead.="<th class='right'>".__('Subtotal')."</th>";
					//$outputhead.="<th class='right'>".__('IVA')."</th>";
					//$outputhead.="<th class='right'>".__('Total')."</th>";
					$outputhead.="<th>".__('Caído')."</th>";
					$outputhead.="<th>".__('Vendido')."</th>";
				$outputhead.="</tr>";
			$outputhead.="</thead>";
		
			$excelhead="<thead>";
				$excelhead.="<tr><th colspan='10' align='center'>".COMPANY_NAME."</th></tr>";	
				$excelhead.="<tr><th colspan='10' align='center'>".__('Reporte Cotizaciones por Ejecutivo')." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</th></tr>";
				$excelhead.="<tr>";
					$excelhead.="<th>".__('Date')."</th>";
					$excelhead.="<th>".__('Quotation Code')."</th>";
					$excelhead.="<th>".__('Orden de Venta')."</th>";
					$excelhead.="<th>".__('Vendedor')."</th>";
					$excelhead.="<th>".__('Contact')."</th>";
					$excelhead.="<th class='centered'>".__('Subtotal')."</th>";
					//$excelhead.="<th class='centered'>".__('IVA')."</th>";
					//$excelhead.="<th class='centered'>".__('Total')."</th>";
					$excelhead.="<th>".__('Caído')."</th>";
					$excelhead.="<th>".__('Vendido')."</th>";
				$excelhead.="</tr>";
			$excelhead.="</thead>";
		
			$totalSubTotalCS=0;
			$totalIvaCS=0;
			$totalTotalCS=0;
			
			$totalSubTotalUSD=0;
			//$totalIvaUSD=0;
			//$totalTotalUSD=0;
			
			$totalDropped=0;
			$totalSold=0;
		
			$bodyRows="";
			foreach ($client['Quotations'] as $quotation){
				//pr($quotation);
				$quotationDate=new DateTime($quotation['Quotation']['quotation_date']);
				$currencyClass="";
				if ($quotation['Quotation']['currency_id']==CURRENCY_CS){
					$totalSubTotalCS+=$quotation['Quotation']['price_subtotal'];
					//$totalIvaCS+=$quotation['Quotation']['price_iva'];
					//$totalTotalCS+=$quotation['Quotation']['price_total'];
					//added calculation of totals in US$
					$totalSubTotalUSD+=round($quotation['Quotation']['price_subtotal']/$quotation['Quotation']['exchange_rate'],2);
					$currencyClass="class='CScurrency'";
					
					// dropped and sold calculated in US dollars
					$totalDropped+=round($quotation['Quotation']['dropped']*$quotation['Quotation']['price_subtotal']/$quotation['Quotation']['exchange_rate'],2);
					$totalSold+=round($quotation['Quotation']['sold']*$quotation['Quotation']['price_subtotal']/$quotation['Quotation']['exchange_rate'],2);
				}
				else if ($quotation['Quotation']['currency_id']==CURRENCY_USD){
					$totalSubTotalUSD+=$quotation['Quotation']['price_subtotal'];
					
					// dropped and sold calculated in US dollars
					$totalDropped+=round($quotation['Quotation']['dropped']*$quotation['Quotation']['price_subtotal'],2);
					$totalSold+=round($quotation['Quotation']['sold']*$quotation['Quotation']['price_subtotal'],2);
					
					//$totalIvaUSD+=$quotation['Quotation']['price_iva'];
					//$totalTotalUSD+=$quotation['Quotation']['price_total'];
					//added calculation of totals in CS$
					$totalSubTotalCS+=round($quotation['Quotation']['price_subtotal']*$quotation['Quotation']['exchange_rate'],2);
					$currencyClass="class='USDcurrency'";
				}
				$totalDropped+=$quotation['Quotation']['dropped'];
				$totalSold+=$quotation['Quotation']['sold'];
				
				$dueDate= new DateTime($quotation['Quotation']['due_date']);
				$nowDate= new DateTime();
				$daysLate=$nowDate->diff($dueDate);
				//echo "days late for quotation ".$quotation['Quotation']['quotation_code']." is ".((int)$daysLate->format("%r%a"));
				if ((int)$daysLate->format("%r%a")>0){
					$bodyRows.="<tr>";
				}
				else {
					$bodyRows.="<tr class='italic'>";
				}
					$bodyRows.="<td>".$quotationDate->format('d-m-Y')."</td>";	
					$bodyRows.="<td>".$this->Html->Link($quotation['Quotation']['quotation_code'],array('controller'=>'quotations','action'=>'view',$quotation['Quotation']['id']),array('target'=>'_blank'))."</td>";	
					$bodyRows.="<td>".($quotation['Quotation']['bool_sales_order_present']?__("Yes"):__("No"))."</td>";
					$bodyRows.="<td>".$this->Html->Link($quotation['User']['first_name'].' '.$quotation['User']['last_name'],['controller'=>'users','action'=>'view',$quotation['User']['id']],['target'=>'_blank'])."</td>";
					$bodyRows.="<td>".$this->Html->Link($quotation['Contact']['fullname'],array('controller'=>'contacts','action'=>'view',$quotation['Contact']['id']),array('target'=>'_blank'))."</td>";
					$bodyRows.="<td ".$currencyClass."><span class='amountright'>".$quotation['Quotation']['price_subtotal']."</span></td>";
					//$bodyRows.="<td ".$currencyClass."><span class='amountright'>".$quotation['Quotation']['price_iva']."</span></td>";
					//$bodyRows.="<td ".$currencyClass."><span class='amountright'>".$quotation['Quotation']['price_total']."</span></td>";
					$bodyRows.="<td class='percentage'><span class='amountright'>".$quotation['Quotation']['dropped']."</span></td>";
					$bodyRows.="<td class='percentage'><span class='amountright'>".$quotation['Quotation']['sold']."</span></td>";
					
				$bodyRows.="</tr>";
			}
			$totalRows="";
			//if ($totalTotalCS>0){
			if ($currencyId==CURRENCY_CS){
				$totalRows.="<tr class='totalrow'>";
					$totalRows.="<td>Total C$</td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td class='CScurrency'><span class='amountright'>".$totalSubTotalCS."</span></td>";
					//$totalRows.="<td class='CScurrency'><span class='amountright'>".$totalIvaCS."</span></td>";
					//$totalRows.="<td class='CScurrency'><span class='amountright'>".$totalTotalCS."</span></td>";
					if (($totalDropped+$totalSold)>0){
						$totalRows.="<td class='percentage'><span class='amountright'>".round(100*$totalDropped/($totalDropped+$totalSold),2)."</span></td>";
						$totalRows.="<td class='percentage'><span class='amountright'>".round(100*$totalSold/($totalDropped+$totalSold),2)."</span></td>";
					}
					else {
						$totalRows.="<td class='percentage italic'><span class='amountright'>0</span></td>";
						$totalRows.="<td class='percentage italic'><span class='amountright'>0</span></td>";
					}
				$totalRows.="</tr>";
			}
			//if ($totalTotalUSD>0){
			if ($currencyId==CURRENCY_USD){
				$totalRows.="<tr class='totalrow'>";
					$totalRows.="<td>Total US$</td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td class='USDcurrency'><span class='amountright'>".$totalSubTotalUSD."</span></td>";
					//$totalRows.="<td class='USDcurrency'><span class='amountright'>".$totalIvaUSD."</span></td>";
					//$totalRows.="<td class='USDcurrency'><span class='amountright'>".$totalTotalUSD."</span></td>";
					if (($totalDropped+$totalSold)>0){
						$totalRows.="<td class='percentage'><span class='amountright'>".round(100*$totalDropped/($totalDropped+$totalSold),2)."</span></td>";
						$totalRows.="<td class='percentage'><span class='amountright'>".round(100*$totalSold/($totalDropped+$totalSold),2)."</span></td>";
					}
					else {
						$totalRows.="<td class='percentage italic'><span class='amountright'>0</span></td>";
						$totalRows.="<td class='percentage italic'><span class='amountright'>0</span></td>";
					}
				$totalRows.="</tr>";
			}
			$body="<tbody>".$totalRows.$bodyRows.$totalRows."</tbody>";

			$table_id=substr("vendedor_".trim($client['Client']['name']),0,30);
			echo "<table id='".$table_id."'>".$outputhead.$body."</table>";
			$excel.="<table id='".$table_id."'>".$excelhead.$body."</table>";
		}
	}
	$_SESSION['reporteCotizacionesPorCliente'] = $excel;
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
