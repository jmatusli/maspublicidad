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

<div class="sales index fullwidth">
<?php	
	echo "<h2>".__('Reporte de Transcurso de Facturas')."</h2>";
	
	echo $this->Form->create('Report'); 
		echo "<fieldset>";
			echo $this->Form->input('Report.startdate',['type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015,'maxYear'=>date('Y')]);
			echo $this->Form->input('Report.enddate',['type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015,'maxYear'=>date('Y')]);
			echo "<br/>";			
			echo $this->Form->input('Report.date_selection_option_id',['label'=>__('Filtrar por ...'),'default'=>$dateSelectionOptionId]);
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
	
	echo $this->Html->link(__('Guardar como Excel'), ['action' => 'guardarReporteTranscursoFacturas'], [ 'class' => 'btn btn-primary']); 
	
	$startDateTime=new DateTime($startDate);
	$endDateTime=new DateTime($endDate);
	
	$output="";
	$excel="";
	
	foreach ($selectedUsers as $user){
		if (!empty($user['Invoices'])){
			echo "<h3>Facturas para Ejecutivo de Venta ".$this->Html->Link($user['User']['username'],['controller'=>'users','action'=>'view',$user['User']['id']])."</h3>";
			
			$tableHeadRow='';	
      $tableHeadRow.="<tr>";
        $tableHeadRow.="<th style='width:8%;'>".__('Date')."</th>";
        $tableHeadRow.="<th style='width:8%;'>".__('Invoice Code')."</th>";
        $tableHeadRow.="<th>".__('Client')."</th>";
        $tableHeadRow.="<th style='width:12%;' class='centered'>".__('SubTotal')."</th>";
        $tableHeadRow.="<th style='width:2%;' class='separator'> </th>";
        $tableHeadRow.="<th style='width:8%;' class='centered'>Días desde Orden</th>";
        $tableHeadRow.="<th style='width:8%;' class='centered'>Fecha Orden</th>";
        $tableHeadRow.="<th style='width:8%;' class='centered'>Orden de Venta</th>";
        $tableHeadRow.="<th style='width:2%;' class='separator'> </th>";
        $tableHeadRow.="<th style='width:8%;' class='centered'>Días desde Cotización</th>";
        $tableHeadRow.="<th style='width:8%;' class='centered'>Fecha Cotización</th>";
        $tableHeadRow.="<th style='width:8%;' class='centered'>Cotización</th>";
      $tableHeadRow.="</tr>";
			$tableHead='<thead>'.$tableHeadRow.'</thead>';
		
			$excelHeadRows='';
      $excelHeadRows.="<tr><th colspan='10' align='center'>".COMPANY_NAME."</th></tr>";	
      $excelHeadRows.="<tr><th colspan='10' align='center'>".__('Reporte Transcurso Facturas')." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</th></tr>";
      $excelHeadRows.=$tableHeadRow;
			$excelHead='<thead>'.$excelHeadRows.'</thead>';
		
			$subtotal=0;
			$bodyRows="";
			foreach ($user['Invoices'] as $invoice){
				//pr($invoice);
				$invoiceDateTime=new DateTime($invoice['Invoice']['invoice_date']);
        $subtotal+=$invoice['Invoice']['subtotal_selected_currency'];
        if ($invoice['Currency']['id']==CURRENCY_CS){
          $currencyClass=" class='CScurrency'";
        }
        elseif ($invoice['Currency']['id']==CURRENCY_USD){
          $currencyClass=" class='USDcurrency'";            
        }
        foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
          $salesOrder=$invoiceSalesOrder['SalesOrder'];
          
          $salesOrderDateTime=new DateTime($salesOrder['sales_order_date']);
          $daysBetweenInvoiceAndSalesOrder=$invoiceDateTime->diff($salesOrderDateTime)->days;
			    
          $quotationDateTime=new DateTime($salesOrder['Quotation']['quotation_date']);
          $daysBetweenInvoiceAndQuotation=$invoiceDateTime->diff($quotationDateTime)->days;				
					
          $bodyRows.="<tr>";
            $bodyRows.="<td>".$invoiceDateTime->format('d-m-Y')."</td>";	
            $bodyRows.="<td>".$this->Html->Link($invoice['Invoice']['invoice_code'],['action'=>'view',$invoice['Invoice']['id']],['target'=>'_blank'])."</td>";	
            $bodyRows.="<td>".$this->Html->Link($invoice['Client']['name'],['controller'=>'clients','action'=>'view',$invoice['Client']['id']],['target'=>'_blank'])."</td>";
            $bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$invoice['Invoice']['price_subtotal']."</span></td>";
            $bodyRows.="<td class='separator'>-</td>";	
            $bodyRows.="<td class='centered'>".$daysBetweenInvoiceAndSalesOrder."</td>";	
            $bodyRows.="<td class='centered'>".$salesOrderDateTime->format('d-m-Y')."</td>";
            $bodyRows.="<td class='centered'>".$this->Html->link($salesOrder['sales_order_code'],['controller'=>'salesOrders','action'=>'view',$salesOrder['id']],['target'=>'_blank'])."</td>";            
            $bodyRows.="<td class='separator'></td>";	
            $bodyRows.="<td class='centered'>".$daysBetweenInvoiceAndQuotation."</td>";	
            $bodyRows.="<td class='centered'>".$quotationDateTime->format('d-m-Y')."</td>";	
            $bodyRows.="<td class='centered'>".$this->Html->link($salesOrder['Quotation']['quotation_code'],['controller'=>'quotations','action'=>'view',$salesOrder['Quotation']['id']],['target'=>'_blank'])."</td>";            
            
          $bodyRows.="</tr>";
        }
			}
      //echo "currency id is ".$currencyId."<br/>";
			$totalRow="";
			$totalRow.="<tr class='totalrow'>";
        $totalRow.="<td>Total ".($currencyId === CURRENCY_CS?"C$":"US$")."</td>";
        $totalRow.="<td></td>";
        $totalRow.="<td></td>";
        $totalRow.="<td class='".($currencyId === CURRENCY_CS?"CScurrency":"USDcurrency")."'><span class='amountright'>".$subtotal."</span></td>";
        $totalRow.="<td class='separator'></td>";
        $totalRow.="<td></td>";
        $totalRow.="<td></td>";
        $totalRow.="<td></td>";
        $totalRow.="<td class='separator'></td>";
        $totalRow.="<td></td>";
        $totalRow.="<td></td>";
        $totalRow.="<td></td>";
      $totalRow.="</tr>";
      
			$excelBody=$tableBody="<tbody>".$totalRow.$bodyRows.$totalRow."</tbody>";
      $tableId=substr("vendedor_".trim($user['User']['username']),0,30);
			echo "<table id='".$tableId."'>".$tableHead.$tableBody."</table>";
			$excel.="<table id='".$tableId."'>".$excelHead.$excelBody."</table>";
		}
	}
	$_SESSION['reporteTranscursoFacturas'] = $excel;
?>
</div>