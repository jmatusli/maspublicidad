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
		$('select.fixed option:not(:selected)').attr('disabled', true);
	});
	
</script>

<div class="sales index fullwidth">
<?php	
	echo "<h2>".__('Reporte Gestión de Ventas por Ejecutivo de Venta')."</h2>";
	
  echo "<div class='container-fluid'>";
    echo "<div class='row'>";
      echo "<div class='col-md-6'>";  
        echo $this->Form->create('Report'); 
          echo "<fieldset>";
            echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015,'maxYear'=>date('Y')));
            echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015,'maxYear'=>date('Y')));
            echo "<br/>";			
            echo $this->Form->input('Report.currency_id',array('label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId));
            echo "<br/>";			
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
        
        echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarReporteGestionDeVentas'), array( 'class' => 'btn btn-primary')); 
        
        $startDateTime=new DateTime($startDate);
        $endDateTime=new DateTime($endDate);
	
        $output="";
        $excel="";
        $currencyClass="";
        $currencyNameForReport="";
        if ($currencyId==CURRENCY_USD){
          $currencyClass="class='USDcurrency'";				
          $currencyNameForReport="USD";
        }
        else if ($currencyId==CURRENCY_CS){					
          $currencyClass="class='CScurrency'";
          $currencyNameForReport="CS";
        }
        
        
        $userTotalsHead="";
        $userTotalsHead.="<thead>";
          $userTotalsHead.="<tr>";
            $userTotalsHead.="<th>Usuario</th>"; 
            $userTotalsHead.="<th class='centered'>Cotizaciones</th>"; 
            $userTotalsHead.="<th class='centered'>Ordenes de Venta</th>"; 
            $userTotalsHead.="<th class='centered'>Facturas</th>"; 
          $userTotalsHead.="</tr>";
        $userTotalsHead.="</thead>";
        $userTotalsBody="";
        $quotationsTotal=0;
        $salesOrdersTotal=0;
        $invoicesTotal=0;
  
        foreach ($selectedUsers as $user){
          $output.="<div class='container-fluid'>";
            $output.="<div class='row'>";
              $output.="<div class='col-md-6'>";
          
            if (!empty($user['QuotationTotals'])){
              //pr($user['QuotationTotals']);
              $quotationTotals=$user['QuotationTotals'];
              $output.="<h3>Estadísticas de Cotizaciones para Ejecutivo de Venta ".$this->Html->Link($user['User']['first_name'].' '.$user['User']['last_name'],['controller'=>'users','action'=>'view',$user['User']['id']])."</h3>";
              $outputhead="<thead>";
                $outputhead.="<tr>";
                  $outputhead.="<th>".__('Description')."</th>";
                  $outputhead.="<th class='centered'>".__('Valor en ').$currencyNameForReport."</th>";
                  $outputhead.="<th class='centered'>".__('Quantity')."</th>";
                $outputhead.="</tr>";
              $outputhead.="</thead>";
            
              $excelhead="<thead>";
                $excelhead.="<tr><th colspan='3' align='center'>".COMPANY_NAME."</th></tr>";	
                $excelhead.='<tr><th colspan="3" align="center">Reporte Gestión de Ventas - Estadísticas Cotizaciones para ejecutivo '.$user['User']['first_name'].' '.$user['User']['last_name'].' de '.$startDateTime->format('d-m-Y').' hasta '.$endDateTime->format('d-m-Y').'</th></tr>';
                $excelhead.="<tr>";
                  $excelhead.="<th>".__('Description')."</th>";
                  $excelhead.="<th>".__('Valor en ').$currencyNameForReport."</th>";
                  $excelhead.="<th>".__('Quantity')."</th>";
                $excelhead.="</tr>";
              $excelhead.="</thead>";
            
              $bodyRows="";			
              
              $bodyRows.="<tr class='totalrow'>";
                $bodyRows.="<td>Total Cotizaciones de este período</td>";	
                $bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$quotationTotals['value_quotations']."</span></td>";	
                $bodyRows.="<td class='centered'>".$quotationTotals['quantity_quotations']."</td>";	
              $bodyRows.="</tr>";
              $bodyRows.="<tr>";
                $bodyRows.="<td>Total Cotizaciones Caídas de este período</td>";	
                $bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$quotationTotals['value_rejected']."</span></td>";	
                $bodyRows.="<td class='centered'>".$quotationTotals['quantity_rejected']."</td>";	
              $bodyRows.="</tr>";
              $bodyRows.="<tr>";
                $bodyRows.="<td>Total Cotizaciones Pendientes de este período</td>";	
                $bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$quotationTotals['value_pending']."</span></td>";	
                $bodyRows.="<td class='centered'>".$quotationTotals['quantity_pending']."</td>";	
              $bodyRows.="</tr>";
              $bodyRows.="<tr>";
                $bodyRows.="<td>Total Ordenes de Venta para Cotizaciones de este período</td>";	
                $bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$quotationTotals['value_sales_orders']."</span></td>";	
                $bodyRows.="<td class='centered'>".$quotationTotals['quantity_sales_orders']."</td>";	
              $bodyRows.="</tr>";
              $bodyRows.="<tr>";
                $bodyRows.="<td>Total Facturas para Cotizaciones de este período</td>";	
                $bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$quotationTotals['value_invoices']."</span></td>";	
                $bodyRows.="<td class='centered'>".$quotationTotals['quantity_invoices']."</td>";	
              $bodyRows.="</tr>";
              
              $body="<tbody>".$bodyRows."</tbody>";

              $table_id=substr("estad_cotiz_".trim($user['User']['first_name'].' '.$user['User']['last_name']),0,30);
              
              $output.="<table id='".$table_id."'>".$outputhead.$body."</table>";
              $excel.="<table id='".$table_id."'>".$excelhead.$body."</table>";
            }
            
            if (!empty($user['SalesOrderTotals'])){
              //pr($user['SalesOrderTotals']);
              $salesOrderTotals=$user['SalesOrderTotals'];
              $output.='<h3>Estadísticas de Ordenes de Venta para Ejecutivo de Venta '.$this->Html->Link($user['User']['first_name'].' '.$user['User']['last_name'],['controller'=>'users','action'=>'view',$user['User']['id']]).'</h3>';
              $outputhead="<thead>";
                $outputhead.="<tr>";
                  $outputhead.="<th>".__('Description')."</th>";
                  $outputhead.="<th class='centered'>".__('Valor en ').$currencyNameForReport."</th>";
                  $outputhead.="<th class='centered'>".__('Quantity')."</th>";
                $outputhead.="</tr>";
              $outputhead.="</thead>";
            
              $excelhead="<thead>";
                $excelhead.="<tr><th colspan='3' align='center'>".COMPANY_NAME."</th></tr>";	
                $excelhead.='<tr><th colspan="3" align="center">Reporte Gestión de Ventas - Estadísticas Ordenes de Venta para ejecutivo '.$user['User']['first_name'].' '.$user['User']['last_name'].' de '.$startDateTime->format('d-m-Y').' hasta '.$endDateTime->format('d-m-Y').'</th></tr>';
                $excelhead.="<tr>";
                  $excelhead.="<th>".__('Description')."</th>";
                  $excelhead.="<th>".__('Valor en ').$currencyNameForReport."</th>";
                  $excelhead.="<th>".__('Quantity')."</th>";
                $excelhead.="</tr>";
              $excelhead.="</thead>";
            
              $bodyRows="";			
              
              $bodyRows.="<tr class='totalrow'>";
                $bodyRows.="<td>Total Ordenes de Venta de este período</td>";	
                $bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$salesOrderTotals['value_sales_orders']."</span></td>";	
                $bodyRows.="<td class='centered'>".$salesOrderTotals['quantity_sales_orders']."</td>";	
              $bodyRows.="</tr>";
              $bodyRows.="<tr>";
                $bodyRows.="<td>Total Ordenes de Venta anuladas de este período</td>";	
                $bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$salesOrderTotals['value_annulled']."</span></td>";	
                $bodyRows.="<td class='centered'>".$salesOrderTotals['quantity_annulled']."</td>";	
              $bodyRows.="</tr>";
              $bodyRows.="<tr>";
                $bodyRows.="<td>Total Ordenes de Venta Pendientes de este período</td>";	
                $bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$salesOrderTotals['value_pending']."</span></td>";	
                $bodyRows.="<td class='centered'>".$salesOrderTotals['quantity_pending']."</td>";	
              $bodyRows.="</tr>";
              $bodyRows.="<tr>";
                $bodyRows.="<td>Total Facturas para Ordenes de Venta de este período</td>";	
                $bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$salesOrderTotals['value_invoices']."</span></td>";	
                $bodyRows.="<td class='centered'>".$salesOrderTotals['quantity_invoices']."</td>";	
              $bodyRows.="</tr>";
              
              $body="<tbody>".$bodyRows."</tbody>";

              $table_id=substr("estad_orden_venta_".trim($user['User']['first_name'].' '.$user['User']['last_name']),0,30);
              
              $output.="<table id='".$table_id."'>".$outputhead.$body."</table>";
              $excel.="<table id='".$table_id."'>".$excelhead.$body."</table>";
            }
      
            if (!empty($user['InvoiceTotals'])){
              //pr($user['InvoiceTotals']);
              $invoiceTotals=$user['InvoiceTotals'];
              $output.="<h3>Estadísticas de Facturas para Ejecutivo de Venta ".$this->Html->Link($user['User']['first_name'].' '.$user['User']['last_name'],['controller'=>'users','action'=>'view',$user['User']['id']])."</h3>";
              $outputhead="<thead>";
                $outputhead.="<tr>";
                  $outputhead.="<th>".__('Description')."</th>";
                  $outputhead.="<th class='centered'>".__('Valor en ').$currencyNameForReport."</th>";
                  $outputhead.="<th class='centered'>".__('Quantity')."</th>";
                $outputhead.="</tr>";
              $outputhead.="</thead>";
            
              $excelhead="<thead>";
                $excelhead.="<tr><th colspan='3' align='center'>".COMPANY_NAME."</th></tr>";	
                $excelhead.='<tr><th colspan="3" align="center">Reporte Gestión de Ventas - Estadísticas Facturas para ejecutivo '.$user['User']['first_name'].' '.$user['User']['last_name'].' de '.$startDateTime->format('d-m-Y').' hasta '.$endDateTime->format('d-m-Y').'</th></tr>';
                $excelhead.="<tr>";
                  $excelhead.="<th>".__('Description')."</th>";
                  $excelhead.="<th>".__('Valor en ').$currencyNameForReport."</th>";
                  $excelhead.="<th>".__('Quantity')."</th>";
                $excelhead.="</tr>";
              $excelhead.="</thead>";
            
              $bodyRows="";			
              
              $bodyRows.="<tr class='totalrow'>";
                $bodyRows.="<td>Total Facturas de Venta de este período</td>";	
                $bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$invoiceTotals['value_invoices']."</span></td>";	
                $bodyRows.="<td class='centered'>".$invoiceTotals['quantity_invoices']."</td>";	
              $bodyRows.="</tr>";
              $bodyRows.="<tr>";
                $bodyRows.="<td>Total Facturas pagadas de este período</td>";	
                $bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$invoiceTotals['value_paid']."</span></td>";	
                $bodyRows.="<td class='centered'>".$invoiceTotals['quantity_paid']."</td>";	
              $bodyRows.="</tr>";
              $bodyRows.="<tr>";
                $bodyRows.="<td>Total Facturas por Cobrar de este período</td>";	
                $bodyRows.="<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$invoiceTotals['value_payment_pending']."</span></td>";	
                $bodyRows.="<td class='centered'>".$invoiceTotals['quantity_payment_pending']."</td>";	
              $bodyRows.="</tr>";
              
              $body="<tbody>".$bodyRows."</tbody>";

              $table_id=substr('estad_facturas_'.trim($user['User']['first_name'].' '.$user['User']['last_name']),0,30);
              
              $output.="<table id='".$table_id."'>".$outputhead.$body."</table>";
              $excel.="<table id='".$table_id."'>".$excelhead.$body."</table>";
            }
            
            if (!empty($user['QuotationRejections'])){
              //pr($user['QuotationRejections']);
              $quotationRejections=$user['QuotationRejections'];
              $output.="<h3>Estadísticas de Caída de Cotizaciones de ".$this->Html->Link($user['User']['first_name'].' '.$user['User']['last_name'],['controller'=>'users','action'=>'view',$user['User']['id']])."</h3>";
              $outputhead="<thead>";
                $outputhead.="<tr>";
                  $outputhead.="<th>".__('Description')."</th>";
                  $outputhead.="<th class='centered'>".__('Quantity')."</th>";
                $outputhead.="</tr>";
              $outputhead.="</thead>";
            
              $excelhead="<thead>";
                $excelhead.="<tr><th colspan='2' align='center'>".COMPANY_NAME."</th></tr>";	
                $excelhead.='<tr><th colspan="2" align="center">Reporte Gestión de Ventas - Caída de Cotizaciones para ejecutivo '.$user['User']['first_name'].' '.$user['User']['last_name'].' de '.$startDateTime->format('d-m-Y').' hasta '.$endDateTime->format('d-m-Y').'</th></tr>';
                $excelhead.="<tr>";
                  $excelhead.="<th>".__('Description')."</th>";
                  $excelhead.="<th>".__('Quantity')."</th>";
                $excelhead.="</tr>";
              $excelhead.="</thead>";
            
              $bodyRows="";			
              foreach ($rejectedReasons as $rejectedReasonId=>$rejectedReasonName){
                $bodyRows.="<tr>";
                  $bodyRows.="<td>".$rejectedReasonName."</td>";	
                  $bodyRows.="<td class='centered'>".$quotationRejections[$rejectedReasonId]."</td>";	
                $bodyRows.="</tr>";
              }
              $body="<tbody>".$bodyRows."</tbody>";

              $table_id=substr("estad_caidas_".trim($user['User']['first_name'].' '.$user['User']['last_name']),0,30);
              
              $output.="<table id='".$table_id."'>".$outputhead.$body."</table>";
              $excel.="<table id='".$table_id."'>".$excelhead.$body."</table>";
            }
            
              $output.="</div>";
              
              
              $output.="<div class='col-md-6'>";
            if (!empty($user['ClientActions'])){
              //pr($user['ClientActions']);
              $clientActions=$user['ClientActions'];
              $output.="<h3>Estadísticas de Activades por Cliente de ".$this->Html->Link($user['User']['first_name'].' '.$user['User']['last_name'],['controller'=>'users','action'=>'view',$user['User']['id']])."</h3>";
              $outputhead="<thead>";
                $outputhead.="<tr>";
                  $outputhead.="<th>".__('Client')."</th>";
                  $outputhead.="<th class='centered' colspan='".count($actionTypes)."'>Acciones para Cotizaciones</th>";
                  $outputhead.="<th class='centered' colspan='".count($actionTypes)."'>Acciones para Ordenes de Venta</th>";
                $outputhead.="</tr>";
                $outputhead.="<tr>";
                  $outputhead.="<th>".__('Client')."</th>";
                  foreach ($actionTypes as $id=>$name){
                    $outputhead.="<th>".$name."</th>";
                  }
                  foreach ($actionTypes as $id=>$name){
                    $outputhead.="<th>".$name."</th>";
                  }
                $outputhead.="</tr>";
              $outputhead.="</thead>";
            
              $excelhead="<thead>";
                $excelhead.="<tr><th colspan='".(2*count($actionTypes)+1)."' align='center'>".COMPANY_NAME."</th></tr>";	
                $excelhead.='<tr><th colspan="'.(2*count($actionTypes)+1).'" align="center">Reporte Gestión de Ventas - Actividades para ejecutivo '.$user['User']['first_name'].' '.$user['User']['last_name'].' de '.$startDateTime->format('d-m-Y').' hasta '.$endDateTime->format('d-m-Y').'</th></tr>';
                $excelhead.="<tr>";
                  $excelhead.="<th>".__('Client')."</th>";
                  foreach ($actionTypes as $id=>$name){
                    $excelhead.="<th>".$name."</th>";
                  }
                  foreach ($actionTypes as $id=>$name){
                    $excelhead.="<th>".$name."</th>";
                  }
                $excelhead.="</tr>";
              $excelhead.="</thead>";
            
              $bodyRows="";			
              //pr($clientActions);
              foreach ($clientActions as $clientId=>$clientActionValues){
                //pr($clientId);
                //pr($clientActionValues);
                $bodyRows.="<tr>";
                  if (!empty($clientId)){
                    $bodyRows.="<td>".$this->Html->link($clients[$clientId],array('controller'=>'clients','action'=>'view',$clientId))."</td>";	
                    foreach ($actionTypes as $actionTypeId=>$actionTypeName){
                      $bodyRows.="<th>".$clientActionValues['quotationActionsForClients'][$actionTypeId]."</th>";
                    }
                    foreach ($actionTypes as $actionTypeId=>$actionTypeName){
                      $bodyRows.="<th>".$clientActionValues['salesOrderActionsForClients'][$actionTypeId]."</th>";
                    }
                  }
                  else {
                    // 20170605 FOR JENI THERE IS ALWAYS A GHOST ENTRY POPPING UP WITH ZEROS ETC, INVESTIGATE
                    // CAUSE WAS THAT DELETION OF QUOTATIONS DID NOT DELETE REMARKS (OR IMAGES)=>STANDS CORRECTED 20170605
                    //pr($clientId);
                    //pr($clientActionValues);
                  }
                $bodyRows.="</tr>";
              }
              $body="<tbody>".$bodyRows."</tbody>";

              $table_id=substr("estad_activ_".trim($user['User']['first_name'].' '.$user['User']['last_name']),0,30);
              
              $output.="<table id='".$table_id."'>".$outputhead.$body."</table>";
              $excel.="<table id='".$table_id."'>".$excelhead.$body."</table>";
            }

            if (!empty($user['CreatedClients'])){
              //pr($user['CreatedClients']);
              $createdClients=$user['CreatedClients'];
              $output.="<h3>Clientes creados por Ejecutivo de Venta ".$this->Html->Link($user['User']['first_name'].' '.$user['User']['last_name'],['controller'=>'users','action'=>'view',$user['User']['id']])."</h3>";
              $outputhead="<thead>";
                $outputhead.="<tr>";
                  $outputhead.="<th>".__('Name')."</th>";
                  $outputhead.="<th>".__('Phone')."</th>";
                  $outputhead.="<th>".__('Cell')."</th>";
                $outputhead.="</tr>";
              $outputhead.="</thead>";
            
              $excelhead="<thead>";
                $excelhead.="<tr><th colspan='3' align='center'>".COMPANY_NAME."</th></tr>";	
                $excelhead.="<tr><th colspan='3' align='center'>".__('Reporte Gestión de Ventas - Clientes creados')." por ejecutivo ".$user['User']['first_name'].' '.$user['User']['last_name']." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</th></tr>";
                $excelhead.="<tr>";
                  $excelhead.="<th>".__('Name')."</th>";
                  $excelhead.="<th>".__('Phone')."</th>";
                  $excelhead.="<th>".__('Cell')."</th>";
                $excelhead.="</tr>";
              $excelhead.="</thead>";
            
              $bodyRows="";			
              
              foreach ($createdClients as $client){
                $bodyRows.="<tr>";
                  $bodyRows.="<td>".$client['Client']['name']."</td>";	
                  $bodyRows.="<td>".$client['Client']['phone']."</td>";	
                  $bodyRows.="<td>".$client['Client']['cell']."</td>";	
                $bodyRows.="</tr>";
              }
              $body="<tbody>".$bodyRows."</tbody>";

              $table_id=substr("clientes_creados_".trim($user['User']['first_name'].' '.$user['User']['last_name']),0,30);
              
              $output.="<table id='".$table_id."'>".$outputhead.$body."</table>";
              $excel.="<table id='".$table_id."'>".$excelhead.$body."</table>";
            }
                
              $output.="</div>";
            $output.="</div>";
          $output.="</div>";
          
          $output.="<div class='container-fluid'>";
            $output.="<div class='row'>";
              $output.="<div class='col-md-6'>";
            if (!empty($user['Quotations'])){
              $output.="<h3>Cotizaciones para Ejecutivo de Venta ".$this->Html->Link($user['User']['first_name'].' '.$user['User']['last_name'],['controller'=>'users','action'=>'view',$user['User']['id']])."</h3>";
              $outputhead="<thead>";
                $outputhead.="<tr>";
                  $outputhead.="<th>".__('Date')."</th>";
                  $outputhead.="<th>".__('Quotation Code')."</th>";
                  $outputhead.="<th>".__('Orden de Venta')."</th>";
                  $outputhead.="<th>".__('Client')."</th>";
                  $outputhead.="<th>".__('Contact')."</th>";
                  $outputhead.="<th class='right'>".__('Subtotal')."</th>";
                  //$outputhead.="<th class='right'>".__('IVA')."</th>";
                  //$outputhead.="<th class='right'>".__('Total')."</th>";
                  //20170203//$outputhead.="<th>".__('Caído')."</th>";
                  //20170203//$outputhead.="<th>".__('Vendido')."</th>";
                $outputhead.="</tr>";
              $outputhead.="</thead>";
            
              $excelhead="<thead>";
                $excelhead.="<tr><th colspan='6' align='center'>".COMPANY_NAME."</th></tr>";	
                $excelhead.="<tr><th colspan='6' align='center'>".__('Reporte Gestión de Ventas - Cotizaciones por Ejecutivo')." para ejecutivo ".$user['User']['first_name'].' '.$user['User']['last_name']." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</th></tr>";
                $excelhead.="<tr>";
                  $excelhead.="<th>".__('Date')."</th>";
                  $excelhead.="<th>".__('Quotation Code')."</th>";
                  $excelhead.="<th>".__('Orden de Venta')."</th>";
                  $excelhead.="<th>".__('Client')."</th>";
                  $excelhead.="<th>".__('Contact')."</th>";
                  $excelhead.="<th class='centered'>".__('Subtotal')."</th>";
                  //$excelhead.="<th class='centered'>".__('IVA')."</th>";
                  //$excelhead.="<th class='centered'>".__('Total')."</th>";
                  //20170203//$excelhead.="<th>".__('Caído')."</th>";
                  //20170203//$excelhead.="<th>".__('Vendido')."</th>";
                $excelhead.="</tr>";
              $excelhead.="</thead>";
            
              $totalSubTotalCS=0;
              //20170203//$totalIvaCS=0;
              //20170203//$totalTotalCS=0;
              
              $totalSubTotalUSD=0;
              //20170203//$totalIvaUSD=0;
              //20170203//$totalTotalUSD=0;
              
              //20170203//$totalDropped=0;
              //20170203//$totalSold=0;
            
              $bodyRows="";
              foreach ($user['Quotations'] as $quotation){
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
                  
                  //20170203//$totalDropped+=round($quotation['Quotation']['dropped']*$quotation['Quotation']['price_subtotal']/$quotation['Quotation']['exchange_rate'],2);
                  //20170203//$totalSold+=round($quotation['Quotation']['sold']*$quotation['Quotation']['price_subtotal']/$quotation['Quotation']['exchange_rate'],2);
                }
                else if ($quotation['Quotation']['currency_id']==CURRENCY_USD){					
                  $totalSubTotalUSD+=$quotation['Quotation']['price_subtotal'];
                  
                  // dropped and sold calculated in US dollars
                  //20170203//$totalDropped+=round($quotation['Quotation']['dropped']*$quotation['Quotation']['price_subtotal'],2);
                  //20170203//$totalSold+=round($quotation['Quotation']['sold']*$quotation['Quotation']['price_subtotal'],2);
                  
                  //$totalIvaUSD+=$quotation['Quotation']['price_iva'];
                  //$totalTotalUSD+=$quotation['Quotation']['price_total'];
                  //added calculation of totals in C$
                  $totalSubTotalCS+=round($quotation['Quotation']['price_subtotal']*$quotation['Quotation']['exchange_rate'],2);
                  $currencyClass="class='USDcurrency'";
                }
                
                $dueDate= new DateTime($quotation['Quotation']['due_date']);
                $nowDate= new DateTime();
                //20170203//$daysLate=$nowDate->diff($dueDate);
                //$output.="days late for quotation ".$quotation['Quotation']['quotation_code']." is ".((int)$daysLate->format("%r%a"));
                //20170203//$output.="<p class='comment'>Las filas para cotizaciones que pasaron su fecha de vencimiento salen en cursivo.</p>";
                //20170203//if ((int)$daysLate->format("%r%a")<0){
                //20170203//	$bodyRows.="<tr class='italic'>";
                //20170203//}
                //20170203//else {
                  $bodyRows.="<tr>";
                //20170203//}
                  $bodyRows.="<td>".$quotationDate->format('d-m-Y')."</td>";	
                  $bodyRows.="<td>".$this->Html->Link($quotation['Quotation']['quotation_code'],array('controller'=>'quotations','action'=>'view',$quotation['Quotation']['id']),array('target'=>'_blank'))."</td>";	
                  $bodyRows.="<td>".(empty($quotation['SalesOrder'])?"-":$this->Html->link($quotation['SalesOrder'][0]['sales_order_code'],array('controller'=>'sales_orders','action'=>'view',$quotation['SalesOrder'][0]['id']),array('target'=>'_blank')))."</td>";
                  $bodyRows.="<td>".$this->Html->Link($quotation['Client']['name'],array('controller'=>'clients','action'=>'view',$quotation['Client']['id']),array('target'=>'_blank'))."</td>";
                  $bodyRows.="<td>".$this->Html->Link($quotation['Contact']['fullname'],array('controller'=>'contacts','action'=>'view',$quotation['Contact']['id']),array('target'=>'_blank'))."</td>";
                  $bodyRows.="<td ".$currencyClass."><span class='amountright'>".$quotation['Quotation']['price_subtotal']."</span></td>";
                  //$bodyRows.="<td ".$currencyClass."><span class='amountright'>".$quotation['Quotation']['price_iva']."</span></td>";
                  //$bodyRows.="<td ".$currencyClass."><span class='amountright'>".$quotation['Quotation']['price_total']."</span></td>";
                  //20170203//$bodyRows.="<td class='percentage'><span class='amountright'>".$quotation['Quotation']['dropped']."</span></td>";
                  //20170203//$bodyRows.="<td class='percentage'><span class='amountright'>".$quotation['Quotation']['sold']."</span></td>";
                  
                $bodyRows.="</tr>";
              }
              $totalRows="";
              
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
                  //20170203//$totalRows.="<td class='percentage'><span class='amountright'>".round(100*$totalDropped/($totalDropped+$totalSold),2)."</span></td>";
                  //20170203//$totalRows.="<td class='percentage'><span class='amountright'>".round(100*$totalSold/($totalDropped+$totalSold),2)."</span></td>";
                $totalRows.="</tr>";
              }
              
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
                  //20170203//if (($totalDropped+$totalSold)>0){
                  //20170203//	$totalRows.="<td class='percentage'><span class='amountright'>".round(100*$totalDropped/($totalDropped+$totalSold),2)."</span></td>";
                  //20170203//	$totalRows.="<td class='percentage'><span class='amountright'>".round(100*$totalSold/($totalDropped+$totalSold),2)."</span></td>";
                  //20170203//}
                  //20170203//else {
                  //20170203//	$totalRows.="<td class='percentage'><span class='amountright'>0</span></td>";
                  //20170203//	$totalRows.="<td class='percentage'><span class='amountright'>0</span></td>";
                  //20170203//}
                $totalRows.="</tr>";
              }
              $body="<tbody>".$totalRows.$bodyRows.$totalRows."</tbody>";

              $table_id=substr("cotizaciones_".trim($user['User']['first_name'].' '.$user['User']['last_name']),0,30);
              $output.="<table id='".$table_id."'>".$outputhead.$body."</table>";
              $excel.="<table id='".$table_id."'>".$excelhead.$body."</table>";
            }
            
            if (!empty($user['SalesOrders'])){
              $output.="<h3>Ordenes de Venta para Ejecutivo de Venta ".$this->Html->Link($user['User']['first_name'].' '.$user['User']['last_name'],['controller'=>'users','action'=>'view',$user['User']['id']])."</h3>";
              $outputhead="<thead>";
                $outputhead.="<tr>";
                  $outputhead.="<th>".__('Date')."</th>";
                  $outputhead.="<th>".__('Sales Order Code')."</th>";
                  $outputhead.="<th>".__('Invoice')."</th>";
                  $outputhead.="<th>".__('Client')."</th>";
                  $outputhead.="<th>".__('Contact')."</th>";
                  $outputhead.="<th class='right'>".__('Subtotal')."</th>";
                $outputhead.="</tr>";
              $outputhead.="</thead>";
            
              $excelhead="<thead>";
                $excelhead.="<tr><th colspan='10' align='center'>".COMPANY_NAME."</th></tr>";	
                $excelhead.="<tr><th colspan='10' align='center'>".__('Reporte Gestión de Ventas - Ordenes de Venta por Ejecutivo')." para ejecutivo ".$user['User']['first_name'].' '.$user['User']['last_name']." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</th></tr>";
                $excelhead.="<tr>";
                  $excelhead.="<th>".__('Date')."</th>";
                  $excelhead.="<th>".__('Sales Order Code')."</th>";
                  $excelhead.="<th>".__('Factura')."</th>";
                  $excelhead.="<th>".__('Client')."</th>";
                  $excelhead.="<th>".__('Contact')."</th>";
                  $excelhead.="<th class='centered'>".__('Subtotal')."</th>";
                $excelhead.="</tr>";
              $excelhead.="</thead>";
            
              $totalSubTotalCS=0;
              $totalSubTotalUSD=0;
            
              $bodyRows="";
              foreach ($user['SalesOrders'] as $salesOrder){
                //pr($salesOrder);
                $salesOrderDate=new DateTime($salesOrder['SalesOrder']['sales_order_date']);
                $currencyClass="";
                if ($salesOrder['SalesOrder']['currency_id']==CURRENCY_CS){
                  $totalSubTotalCS+=$salesOrder['SalesOrder']['price_subtotal'];
                  $totalSubTotalUSD+=round($salesOrder['SalesOrder']['price_subtotal']/$salesOrder['SalesOrder']['exchange_rate'],2);
                  $currencyClass="class='CScurrency'";
                  
                }
                else if ($salesOrder['SalesOrder']['currency_id']==CURRENCY_USD){					
                  $totalSubTotalUSD+=$salesOrder['SalesOrder']['price_subtotal'];
                  $totalSubTotalCS+=round($salesOrder['SalesOrder']['price_subtotal']*$salesOrder['SalesOrder']['exchange_rate'],2);
                  $currencyClass="class='USDcurrency'";
                }
                
                $nowDate= new DateTime();
                  $bodyRows.="<tr>";
                  $bodyRows.="<td>".$salesOrderDate->format('d-m-Y')."</td>";	
                  $bodyRows.="<td>".$this->Html->Link($salesOrder['SalesOrder']['sales_order_code'],array('controller'=>'sales_orders','action'=>'view',$salesOrder['SalesOrder']['id']),array('target'=>'_blank'))."</td>";	
                  $bodyRows.="<td>".(empty($salesOrder['InvoiceSalesOrder'])?"-":$this->Html->link($salesOrder['InvoiceSalesOrder'][0]['Invoice']['invoice_code'],array('controller'=>'invoices','action'=>'view',$salesOrder['InvoiceSalesOrder'][0]['Invoice']['id']),array('target'=>'_blank')))."</td>";
                  $bodyRows.="<td>".$this->Html->Link($salesOrder['Quotation']['Client']['name'],array('controller'=>'clients','action'=>'view',$salesOrder['Quotation']['Client']['id']),array('target'=>'_blank'))."</td>";
                  if (!empty($salesOrder['Quotation']['Contact'])){
                    $bodyRows.="<td>".$this->Html->Link($salesOrder['Quotation']['Contact']['fullname'],array('controller'=>'contacts','action'=>'view',$salesOrder['Quotation']['Contact']['id']),array('target'=>'_blank'))."</td>";
                  }
                  else {
                    $bodyRows.="<td>-</td>";
                  }
                  $bodyRows.="<td ".$currencyClass."><span class='amountright'>".$salesOrder['Quotation']['price_subtotal']."</span></td>";
                  
                $bodyRows.="</tr>";
              }
              $totalRows="";
              
              if ($currencyId==CURRENCY_CS){
                $totalRows.="<tr class='totalrow'>";
                  $totalRows.="<td>Total C$</td>";
                  $totalRows.="<td></td>";
                  $totalRows.="<td></td>";
                  $totalRows.="<td></td>";
                  $totalRows.="<td></td>";
                  $totalRows.="<td class='CScurrency'><span class='amountright'>".$totalSubTotalCS."</span></td>";
                $totalRows.="</tr>";
              }
              
              if ($currencyId==CURRENCY_USD){
                $totalRows.="<tr class='totalrow'>";
                  $totalRows.="<td>Total US$</td>";
                  $totalRows.="<td></td>";
                  $totalRows.="<td></td>";
                  $totalRows.="<td></td>";
                  $totalRows.="<td></td>";
                  $totalRows.="<td class='USDcurrency'><span class='amountright'>".$totalSubTotalUSD."</span></td>";
                $totalRows.="</tr>";
              }
              $body="<tbody>".$totalRows.$bodyRows.$totalRows."</tbody>";

              $table_id=substr("ordenes_venta_".trim($user['User']['first_name'].' '.$user['User']['last_name']),0,30);
              $output.="<table id='".$table_id."'>".$outputhead.$body."</table>";
              $excel.="<table id='".$table_id."'>".$excelhead.$body."</table>";
            }
            
            if (!empty($user['Invoices'])){
              $output.="<h3>Facturas para Ejecutivo de Venta ".$this->Html->Link($user['User']['first_name'].' '.$user['User']['last_name'],['controller'=>'users','action'=>'view',$user['User']['id']])."</h3>";
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
                $excelhead.="<tr><th colspan='4' align='center'>Reporte Gestión de Ventas -  Facturas por Ejecutivo para ejecutivo ".$user['User']['first_name'].' '.$user['User']['last_name']." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</th></tr>";
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
                  $bodyRows.="<td>".$this->Html->Link($invoice['Invoice']['invoice_code'],array('controller'=>'invoices','action'=>'view',$invoice['Invoice']['id']),array('target'=>'_blank'))."</td>";	
                  $bodyRows.="<td>".$this->Html->Link($invoice['Client']['name'],array('controller'=>'clients','action'=>'view',$invoice['Client']['id']),array('target'=>'_blank'))."</td>";
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

              $table_id=substr("vendedor_".trim($user['User']['first_name'].' '.$user['User']['last_name']),0,30);
              $output.="<table id='".$table_id."'>".$outputhead.$body."</table>";
              $excel.="<table id='".$table_id."'>".$excelhead.$body."</table>";
            }
              $output.="</div>";
            $output.="</div>";
          $output.="</div>";
          
          $userTotalsBody.="<tr>";
            $userTotalsBody.="<td>".$user['User']['first_name'].' '.$user['User']['last_name']."</td>";
            $userTotalsBody.=(empty($quotationTotals)?"<td>0</td>":"<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$quotationTotals['value_quotations']."</span></td>");
            $userTotalsBody.=(empty($salesOrderTotals)?"<td>0</td>":"<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$salesOrderTotals['value_sales_orders']."</span></td>");	
            $userTotalsBody.=(empty($invoiceTotals)?"<td>0</td>":"<td ".$currencyClass."><span class='currency'></span><span class='amountright'>".$invoiceTotals['value_invoices']."</span></td>");	
          $userTotalsBody.="</tr>";
          $quotationsTotal+=$quotationTotals['value_quotations'];
          $salesOrdersTotal+=$salesOrderTotals['value_sales_orders'];
          $invoicesTotal+=$invoiceTotals['value_invoices'];
        }
        $totalRows="";
        if ($currencyId==CURRENCY_CS){
          $totalRows.="<tr class='totalrow'>";
            $totalRows.="<td>Total C$</td>";
            $totalRows.="<td class='CScurrency'><span class='amountright'>".$quotationsTotal."</span></td>";
            $totalRows.="<td class='CScurrency'><span class='amountright'>".$salesOrdersTotal."</span></td>";
            $totalRows.="<td class='CScurrency'><span class='amountright'>".$invoicesTotal."</span></td>";
          $totalRows.="</tr>";
        }
        if ($currencyId==CURRENCY_USD){
          $totalRows.="<tr class='totalrow'>";
            $totalRows.="<td>Total US$</td>";
            $totalRows.="<td class='USDcurrency'><span class='amountright'>".$quotationsTotal."</span></td>";
            $totalRows.="<td class='USDcurrency'><span class='amountright'>".$salesOrdersTotal."</span></td>";
            $totalRows.="<td class='USDcurrency'><span class='amountright'>".$invoicesTotal."</span></td>";
          $totalRows.="</tr>";
        }
        $userTotals="<table id='resumen'>".$userTotalsHead.$totalRows.$userTotalsBody.$totalRows."</table>";
        
        $excel.=$userTotals.$excel;
      echo "</div>";  
      echo "<div class='col-md-6'>";  
        echo $userTotals;
      echo "</div>";
    echo "</div>";
    echo $output;
  echo "</div>";
  
	$_SESSION['reporteGestionDeVentas'] = $excel;
?>
</div>

