<script>
	$('body').on('change','.powerselector',function(e){
		if ($(this).is(':checked')){
			$(this).closest('div.salesOrderTypeContainer').find('td.selector div input[type="checkbox"]').prop('checked',true);
			$(this).closest('div.salesOrderTypeContainer').find('input.powerselector').prop('checked',true);
		}
		else {
			$(this).closest('div.salesOrderTypeContainer').find('td.selector div input[type="checkbox"]').prop('checked',false);
			$(this).closest('div.salesOrderTypeContainer').find('input.powerselector').prop('checked',false);
		}
	});

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
	
	function formatPercentages(){
		$("td.percentage").each(function(){
			//if (parseFloat($(this).find('.amountright').text())<0){
			//	$(this).find('.amountright').prepend("-");
			//}
			$(this).find('.amountright').number(true,2);
			$(this).find('.amountright').append(" %");
		});
	}
	
	$(document).ready(function(){
		formatNumbers();
		formatCSCurrencies();
		formatUSDCurrencies();
		formatPercentages();
	});
</script>

<div class="salesOrders index">
<?php 
	echo "<h1>".__('Sales Orders')."</h1>";
  echo "<div class='container-fluid'>";
		echo "<div class='row'>";
			echo "<div class='col-md-6'>";		
        echo $this->Form->create('Report');
          echo "<fieldset>";
            echo $this->Form->input('Report.startdate',['type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015,'maxYear'=>date('Y')]);
            echo $this->Form->input('Report.enddate',['type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015,'maxYear'=>date('Y')]);
            echo "<br/>";
            if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) { 
              echo $this->Form->input('Report.user_id',['label'=>'Mostrar Usuario','options'=>$users,'default'=>$user_id,'empty'=>['0'=>'-- Todos Usuarios --']]);
            }
            else {
              echo $this->Form->input('Report.user_id',['label'=>__('Mostrar Usuario'),'options'=>$users,'default'=>$user_id]);
            }
            echo $this->Form->input('Report.currency_id',['label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId]);			
            echo $this->Form->input('Report.invoice_display',['label'=>__('Mostrar Facturas'),'options'=>$invoiceOptions,'default'=>$invoiceDisplay]);
            echo $this->Form->input('Report.authorized_display',['label'=>__('Mostrar Autorizados'),'options'=>$authorizedOptions,'default'=>$authorizedDisplay]);
          echo "</fieldset>";
          echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
          echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
        echo "<br/>";	
        echo $this->Form->submit(__('Refresh'),['name'=>'refresh', 'id'=>'refresh','div'=>['class'=>'submit']]); 
        echo $this->Form->submit(__('Autorizar todas Ordenes de Venta seleccionados'),['name'=>'authorize_all', 'id'=>'authorize_all','style'=>'width:30em;','div'=>['class'=>'submit']]); 
        echo "<br/>";	
        echo $this->Html->link(__('Guardar como Excel'), ['action' => 'guardarResumenOrdenesDeVenta'], ['class' => 'btn btn-primary']);
      echo "</div>";
      echo "<div class='col-md-6'>";		
        if ($userRoleId == ROLE_ADMIN || $canSeeExecutiveTables) { 
          echo "<h3>Totales por Ejecutivo</h3>";
          $thead="";  
          $thead.="<thead>";
            $thead.="<tr>";
              $thead.="<th>Ejecutivo</th>";
              $thead.="<th>Total Anulada</th>";
              $thead.="<th>Total Pendiente</th>";
              $thead.="<th>Total Autorizada</th>";
              $thead.="<th>Total Período</th>";
              $thead.="<th>% Autorizada</th>";
              
            $thead.="</tr>";
          $thead.="</thead>";
          
          $totalCS=[
            'annulled'=>0,
            'pending'=>0,
            'authorized'=>0,
            'total'=>0,
          ]; 
          $totalUSD=[
            'annulled'=>0,
            'pending'=>0,
            'authorized'=>0,
            'total'=>0,
          ];
                
          $tbody="";         
          $tbody.="<tbody>";							
          foreach ($users as $key=>$value){
            if (!empty($userPeriodCS[$key])||!empty($userPeriodUSD[$key])){
              if ($currencyId==CURRENCY_CS){
                $totalCS['annulled']+=$userPeriodCS[$key]['annulled'];
                $totalCS['pending']+=$userPeriodCS[$key]['pending'];
                $totalCS['authorized']+=$userPeriodCS[$key]['authorized'];
                
                $totalCS['total']+=($userPeriodCS[$key]['annulled'] + $userPeriodCS[$key]['pending'] + $userPeriodCS[$key]['authorized']);
                
                $tbody.="<tr>";
                  $tbody.="<td>".$value."</td>";
                  $tbody.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$userPeriodCS[$key]['annulled']."</span></td>";
                  $tbody.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$userPeriodCS[$key]['pending']."</span></td>";
                  $tbody.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$userPeriodCS[$key]['authorized']."</span></td>";
                  $tbody.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".($userPeriodCS[$key]['annulled'] + $userPeriodCS[$key]['pending'] + $userPeriodCS[$key]['authorized'])."</span></td>";
                  $tbody.="<td class='percentage'><span class='amountright'>".($userPeriodCS[$key]['annulled'] + $userPeriodCS[$key]['pending'] + $userPeriodCS[$key]['authorized'] > 0 ? ($userPeriodCS[$key]['authorized']/($userPeriodCS[$key]['annulled'] + $userPeriodCS[$key]['pending'] + $userPeriodCS[$key]['authorized'])):0 )."</span></td>";
                $tbody.="</tr>";
              }
              elseif ($currencyId==CURRENCY_USD){
                $totalUSD['annulled']+=$userPeriodUSD[$key]['annulled'];
                $totalUSD['pending']+=$userPeriodUSD[$key]['pending'];
                $totalUSD['authorized']+=$userPeriodUSD[$key]['authorized'];
                
                $totalUSD['total']+=($userPeriodUSD[$key]['annulled'] + $userPeriodUSD[$key]['pending'] + $userPeriodUSD[$key]['authorized']);
                
                $tbody.="<tr>";
                  $tbody.="<td>".$value."</td>";
                  $tbody.="<td class='USDcurrency'><span class='currency'>US$ </span><span class='amountright'>".$userPeriodUSD[$key]['annulled']."</span></td>";
                  $tbody.="<td class='USDcurrency'><span class='currency'>US$ </span><span class='amountright'>".$userPeriodUSD[$key]['pending']."</span></td>";
                  $tbody.="<td class='USDcurrency'><span class='currency'>US$ </span><span class='amountright'>".$userPeriodUSD[$key]['authorized']."</span></td>";
                  $tbody.="<td class='USDcurrency'><span class='currency'>US$ </span><span class='amountright'>".($userPeriodUSD[$key]['annulled'] + $userPeriodUSD[$key]['pending'] + $userPeriodUSD[$key]['authorized'])."</span></td>";
                  $tbody.="<td class='percentage'><span class='amountright'>".($userPeriodUSD[$key]['annulled'] + $userPeriodUSD[$key]['pending'] + $userPeriodUSD[$key]['authorized'] > 0 ? ($userPeriodUSD[$key]['authorized']/($userPeriodUSD[$key]['annulled'] + $userPeriodUSD[$key]['pending'] + $userPeriodUSD[$key]['authorized'])):0 )."</span></td>";
                $tbody.="</tr>";
              }
            }
          }
          $tbody.="</tbody>";
          $totalRow="";
          if ($currencyId==CURRENCY_CS){
            $totalRow.="<tr class='totalrow'>";
              $totalRow.="<td>Total C$</td>";
              $totalRow.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$totalCS['annulled']."</span></td>";
              $totalRow.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$totalCS['pending']."</span></td>";
              $totalRow.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".$totalCS['authorized']."</span></td>";
              $totalRow.="<td class='CScurrency'><span class='currency'>C$ </span><span class='amountright'>".($totalCS['annulled'] + $totalCS['pending'] + $totalCS['authorized'])."</span></td>";
              $totalRow.="<td class='percentage'><span class='amountright'>".($totalCS['annulled'] + $totalCS['pending'] + $totalCS['authorized'] > 0 ? ($totalCS['authorized']/($totalCS['annulled'] + $totalCS['pending'] + $totalCS['authorized'])):0 )."</span></td>";
              
            $totalRow.="</tr>";
          }
          elseif ($currencyId==CURRENCY_USD){
            $totalRow.="<tr class='totalrow'>";
              $totalRow.="<td>Total US$</td>";
              $totalRow.="<td class='USDcurrency'><span class='currency'>C$ </span><span class='amountright'>".$totalUSD['annulled']."</span></td>";
              $totalRow.="<td class='USDcurrency'><span class='currency'>C$ </span><span class='amountright'>".$totalUSD['pending']."</span></td>";
              $totalRow.="<td class='USDcurrency'><span class='currency'>C$ </span><span class='amountright'>".$totalUSD['authorized']."</span></td>";
              $totalRow.="<td class='USDcurrency'><span class='currency'>C$ </span><span class='amountright'>".($totalUSD['annulled'] + $totalUSD['pending'] + $totalUSD['authorized'])."</span></td>";
              $totalRow.="<td class='percentage'><span class='amountright'>".($totalUSD['annulled'] + $totalUSD['pending'] + $totalUSD['authorized'] > 0 ? ($totalUSD['authorized']/($totalUSD['annulled'] + $totalUSD['pending'] + $totalUSD['authorized'])):0 )."</span></td>";
            $totalRow.="</tr>";
          }
          echo "<table cellpadding='0' cellspacing='0'>".$thead.$totalRow.$tbody.$totalRow."</table>";
        }
      echo "</div>";
    echo "</div>";
  echo "</div>";  
	$startDateTime=new DateTime($startDate);
	$endDateTime=new DateTime($endDate);
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_add_permission){
			echo "<li>".$this->Html->link(__('New Sales Order'), ['action' => 'add'])."</li>";
		}
		echo "<br/>";
		if ($bool_quotation_index_permission){
			echo "<li>".$this->Html->link(__('List Quotations'), ['controller' => 'quotations', 'action' => 'index'])."</li>";
		}
		if ($bool_quotation_add_permission){
			echo "<li>".$this->Html->link(__('New Quotation'), ['controller' => 'quotations', 'action' => 'add'])."</li>";
		}
	echo "</ul>";
?>
</div>
<div style='clear:left;'>
<?php
	$excelOutput='';
  //pr($salesOrders);
  foreach ($salesOrders as $salesOrderType=>$salesOrdersData){
    $pageHeader="";
    $excelHeader="";
    $pageHeader.="<thead>";
      $pageHeader.="<tr>";
        if (in_array($salesOrderType,['authorization_pending_period','authorization_pending_before_period','authorization_pending_after_period']) && $bool_autorizar_permission){
          $pageHeader.="<th>Seleccione</th>";
        }
        if ($user_id==0){
          $pageHeader.="<th>".$this->Paginator->sort('user_id','Vendedor')."</th>";
        }
        $pageHeader.="<th>".$this->Paginator->sort('sales_order_date')."</th>";
        $pageHeader.="<th>".$this->Paginator->sort('sales_order_code')."</th>";
        $pageHeader.="<th>Facturas relacionadas</th>";
        $pageHeader.="<th>".$this->Paginator->sort('bool_completely_delivered')."</th>";
        $pageHeader.="<th>".$this->Paginator->sort('Quotation.quotation_code','Cotización')."</th>";
        //$pageHeader.="<th>".$this->Paginator->sort('client_id')."</th>";
        $pageHeader.="<th>Cliente</th>";
        $pageHeader.="<th class='centered'>".$this->Paginator->sort('price_subtotal')."</th>";
        $pageHeader.="<th style='width:10%;' class='centered'>Estado</th>";
        
        $pageHeader.="<th class='actions'>".__('Actions')."</th>";
      $pageHeader.="</tr>";
    $pageHeader.="</thead>";
    $excelHeader.="<thead>";		
      if ($user_id==0){
        $excelHeader.="<tr><th colspan='6' align='center'>".COMPANY_NAME."</th></tr>";	
        $excelHeader.="<tr><th colspan='6' align='center'>".__('Resumen de Ordenes de Venta')." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</th></tr>";
        $excelHeader.="<tr>";
          $excelHeader.="<th>".$this->Paginator->sort('user_id','Vendedor')."</th>";
      }
      else {
        $excelHeader.="<tr><th colspan='6' align='center'>".COMPANY_NAME."</th></tr>";	
        $excelHeader.="<tr><th colspan='6' align='center'>".__('Resumen de Ordenes de Venta')." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</th></tr>";
        $excelHeader.="<tr>";
      }
        $excelHeader.="<th>".$this->Paginator->sort('sales_order_date')."</th>";
        $excelHeader.="<th>".$this->Paginator->sort('sales_order_code')."</th>";
        $excelHeader.="<th>Facturas relacionadas</th>";
        $excelHeader.="<th>".$this->Paginator->sort('bool_completely_delivered')."</th>";
        $excelHeader.="<th>".$this->Paginator->sort('quotation_id')."</th>";
        $excelHeader.="<th>".$this->Paginator->sort('client_id')."</th>";
        $excelHeader.="<th>".$this->Paginator->sort('price_subtotal')."</th>";
        $excelHeader.="<th>Estado</th>";
      $excelHeader.="</tr>";
    $excelHeader.="</thead>";

    $pageBody="";
    $excelBody="";

    $subtotalCS=0;
    $subtotalUSD=0;
    $statustotalCS=0;
    $statustotalUSD=0;
    
    foreach ($salesOrdersData as $salesOrder){ 
      if (
        (
          $invoiceDisplay == 0 
          || ($invoiceDisplay == 1 && $salesOrder['SalesOrder']['bool_completely_delivered']) 
          || ($invoiceDisplay == 2 && !$salesOrder['SalesOrder']['bool_completely_delivered'])
        ) 
        && (
          $authorizedDisplay == 0 
          || ($authorizedDisplay == 1 && $salesOrder['SalesOrder']['bool_authorized'])
          || ($authorizedDisplay == 2 && !$salesOrder['SalesOrder']['bool_authorized'])
        )
      ){
        $salesOrderDate=new DateTime($salesOrder['SalesOrder']['sales_order_date']);
        
        if ($salesOrder['Currency']['id']==CURRENCY_CS){
          $currencyClass=" class='CScurrency'";
          $subtotalCS+=$salesOrder['SalesOrder']['price_subtotal'];
          $statustotalCS+=round($salesOrder['SalesOrder']['price_subtotal']*$salesOrder['SalesOrder']['status'],2);
          
          //added calculation of totals in US$
          $subtotalUSD+=round($salesOrder['SalesOrder']['price_subtotal']/$salesOrder['SalesOrder']['exchange_rate'],2);
          $statustotalUSD+=round($salesOrder['SalesOrder']['price_subtotal']*$salesOrder['SalesOrder']['status']/$salesOrder['SalesOrder']['exchange_rate'],2);
        }
        elseif ($salesOrder['Currency']['id']==CURRENCY_USD){
          $currencyClass=" class='USDcurrency'";
          $subtotalUSD+=$salesOrder['SalesOrder']['price_subtotal'];
          $statustotalUSD+=round($salesOrder['SalesOrder']['price_subtotal']*$salesOrder['SalesOrder']['status'],2);
          
          //added calculation of totals in CS$
          $subtotalCS+=round($salesOrder['SalesOrder']['price_subtotal']*$salesOrder['SalesOrder']['exchange_rate'],2);
          $statustotalCS+=round($salesOrder['SalesOrder']['price_subtotal']*$salesOrder['SalesOrder']['status']*$salesOrder['SalesOrder']['exchange_rate'],2);
        }
        //echo "salesorder currency is ".$salesOrder['Currency']['id']."<br/>";
        //echo "subtotal for salesorder is ".$salesOrder['SalesOrder']['price_subtotal']."<br/>";
        //echo "status for salesorder is ".$salesOrder['SalesOrder']['status']."<br/>";
        //echo "running subtotal C$ is ".$subtotalCS."<br/>";
        //echo "running status subtotal C$ is ".$subtotalCS."<br/>";
        //echo "running subtotal US$ is ".$subtotalUSD."<br/>";
        //echo "running status subtotal US$ is ".$subtotalUSD."<br/>";
        
        $pageRow="";
          if (in_array($salesOrderType,['authorization_pending_period','authorization_pending_before_period','authorization_pending_after_period']) && $bool_autorizar_permission){
            $pageRow.="<td class='selector'>".$this->Form->input('Report.selector.'.$salesOrder['SalesOrder']['id'],array('checked'=>true,'label'=>false))."</td>";
          }
          if ($user_id==0){
            $pageRow.="<td>".$this->Html->link($salesOrder['Quotation']['User']['first_name']." ".$salesOrder['Quotation']['User']['last_name'], array('controller' => 'users', 'action' => 'view', $salesOrder['Quotation']['User']['id']))."</td>";
          }
          $pageRow.="<td>".$salesOrderDate->format('d-m-Y')."</td>";
          $pageRow.="<td>".$this->Html->Link($salesOrder['SalesOrder']['sales_order_code'].($salesOrder['SalesOrder']['bool_annulled']?" (Anulada)":""),array('action'=>'view',$salesOrder['SalesOrder']['id']))."</td>";
          if (empty($salesOrder['InvoiceSalesOrder'])){
            $pageRow.="<td>-</td>";
          }
          else {
            $pageRow.="<td>";
            for ($iso=0;$iso<count($salesOrder['InvoiceSalesOrder']);$iso++){
              $pageRow.=$this->Html->Link($salesOrder['InvoiceSalesOrder'][$iso]['Invoice']['invoice_code'],array('controller'=>'invoices','action'=>'view',$salesOrder['InvoiceSalesOrder'][$iso]['Invoice']['id']))."";
              if ($iso<count($salesOrder['InvoiceSalesOrder'])-1){
                $pageRow.="<br/>";
              }
            }
            $pageRow.="</td>";
          }
          $pageRow.="<td>".($salesOrder['SalesOrder']['bool_completely_delivered']?__('Yes'):('No'))."</td>";
          
          
          $pageRow.="<td>".$this->Html->link($salesOrder['Quotation']['quotation_code'], array('controller' => 'quotations', 'action' => 'view', $salesOrder['Quotation']['id']))."</td>";
          $pageRow.="<td>".$this->Html->link($salesOrder['Quotation']['Client']['name'], array('controller' => 'clients', 'action' => 'view', $salesOrder['Quotation']['Client']['id']))."</td>";
          $pageRow.="<td".$currencyClass."><span class='currency'>".$salesOrder['Currency']['abbreviation']."</span><span class='amountright'>".h($salesOrder['SalesOrder']['price_subtotal'])."</span></td>";
          $pageRow.="<td class='percentage'><span class='amountright'>".h($salesOrder['SalesOrder']['status'])."</span></td>";
          if ($salesOrder['SalesOrder']['bool_annulled']){
            $excelBody.="<tr class='italic'>".$pageRow."</tr>";
          }
          else {
            $excelBody.="<tr>".$pageRow."</tr>";
          }

          $pageRow.="<td class='actions'>";
            $filename="Orden de Venta_".$salesOrder['SalesOrder']['sales_order_code'];
            if ($salesOrderType == 'authorization_pending' && $bool_autorizar_permission){
              $pageRow.=$this->Html->link(__('Autorizar'), array('action' => 'autorizar', $salesOrder['SalesOrder']['id']), array('confirm'=>__('Está seguro que quiere autorizar Orden de Venta # %s?', $salesOrder['SalesOrder']['sales_order_code'])));
            }
            //if ($bool_cambiarestado_permission){
            //	$pageRow.=$this->Html->link(__('Cambiar Estado'), array('action' => 'cambiarEstado', $salesOrder['SalesOrder']['id']));
            //}
            //$pageRow.=$this->Form->postLink(__('Anular'), array('action' => 'annul', $salesOrder['SalesOrder']['id']), array(), __('Está seguro que quiere anular Orden de Venta # %s?', $salesOrder['SalesOrder']['sales_order_code']));
            $pageRow.=$this->Html->link(__('Pdf'), array('action' => 'viewPdf','ext'=>'pdf', $salesOrder['SalesOrder']['id'],$filename),array('target'=>'_blank'));
            if ($salesOrderType == 'authorized'){
              if (empty($salesOrder['ProductionOrder'])){
                if ($bool_production_order_crear_permission){
                  $pageRow.=$this->Html->link('Orden de Producción', ['controller'=>'productionOrders','action' => 'crear',$salesOrder['SalesOrder']['id']],['target'=>'_blank']);
                }
                else {
                    $pageRow.=' ';
                }
              }
              else {
                //pr($salesOrder['ProductionOrder']);
                if ($bool_production_order_resumen_permission){
                  $pageRow.=$this->Html->link($salesOrder['ProductionOrder'][0]['production_order_code'], ['controller'=>'productionOrders','action' => 'detalle',$salesOrder['ProductionOrder'][0]['id']]);
                }
                else {
                  $pageRow.=$salesOrder['ProductionOrder'][0]['production_order_code'];
                }
              }
            }
          $pageRow.="</td>";
        
        if ($salesOrder['SalesOrder']['bool_annulled']){
          $pageBody.="<tr class='italic'>".$pageRow."</tr>";
        }
        else {
          $pageBody.="<tr>".$pageRow."</tr>";
        }
      }
    }
    $pageTotalRow="";
    if ($currencyId==CURRENCY_CS){
      $pageTotalRow.="<tr class='totalrow'>";
        if (in_array($salesOrderType,['authorization_pending_period','authorization_pending_before_period','authorization_pending_after_period']) && $bool_autorizar_permission){
          $pageTotalRow.="<td></td>";
        }
        $pageTotalRow.="<td>Total C$</td>";
        if ($user_id==0){
          $pageTotalRow.="<td></td>";
        }
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$subtotalCS."</span></td>";
        $status=0;
        if ($subtotalCS>0){
          $status=$statustotalCS/$subtotalCS;
        }
        $pageTotalRow.="<td class='percentage'><span class='amountright'>".$status."</span></td>";
        $pageTotalRow.="<td></td>";
      $pageTotalRow.="</tr>";
    }
    if ($currencyId==CURRENCY_USD){
      $pageTotalRow.="<tr class='totalrow'>";
        if (in_array($salesOrderType,['authorization_pending_period','authorization_pending_before_period','authorization_pending_after_period']) && $bool_autorizar_permission){
          $pageTotalRow.="<td></td>";
        }
        $pageTotalRow.="<td>Total US$</td>";
        if ($user_id==0){
          $pageTotalRow.="<td></td>";
        }
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$subtotalUSD."</span></td>";
        $status=0;
        if ($subtotalUSD>0){
          $status=$statustotalUSD/$subtotalUSD;
        }
        $pageTotalRow.="<td class='percentage'><span class='amountright'>".$status."</span></td>";
        $pageTotalRow.="<td></td>";
      $pageTotalRow.="</tr>";
    }
    $pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
    
    echo '<div class="salesOrderTypeContainer">';
    if ($salesOrderType == 'authorization_pending_period'){
      echo '<h2>Ordenes de Venta del período esperando autorización</h2>';
      echo $this->Form->input('powerselector1'.$salesOrderType,['class'=>'powerselector','checked'=>true,'style'=>'width:5em;','label'=>['text'=>'Seleccionar/Deseleccionar Ordenes de Venta para autorizar','style'=>'padding-left:5em;'],'format' => ['before', 'input', 'between', 'label', 'after', 'error']]);
      $tableId="Ordenes_de_Venta_No_Autorizadas_Periodo";
    }
    if ($salesOrderType == 'authorization_pending_before_period'){
      echo '<h2>Ordenes de Venta antes del período esperando autorización</h2>';
      echo $this->Form->input('powerselector1'.$salesOrderType,[
        'class'=>'powerselector',
        'checked'=>true,
        'style'=>'width:5em;',
        'label'=>['text'=>'Seleccionar/Deseleccionar Ordenes de Venta para autorizar','style'=>'padding-left:5em;'],
        'format' => ['before', 'input', 'between', 'label', 'after', 'error'],
      ]);
      $tableId="Ordenes_de_Venta_No_Autorizadas_Antes";
    }
    if ($salesOrderType == 'authorization_pending_after_period'){
      echo '<h2>Ordenes de Venta después del período esperando autorización</h2>';
      echo $this->Form->input('powerselector1'.$salesOrderType,['class'=>'powerselector','checked'=>true,'style'=>'width:5em;','label'=>['text'=>'Seleccionar/Deseleccionar Ordenes de Venta para autorizar','style'=>'padding-left:5em;'],'format' => ['before', 'input', 'between', 'label', 'after', 'error']]);
      $tableId="Ordenes_de_Venta_No_Autorizadas_Despues";
    }
    elseif ($salesOrderType == 'authorized') {
      echo '<h2>Todas Ordenes de Venta autorizadas no entregadas completamente</h2>';
      $tableId="Ordenes_de_Venta_Autorizadas";
    }
    elseif ($salesOrderType == 'annulled') {
      echo '<h2>Ordenes de Venta anuladas para el período seleccionado</h2>';
      $tableId="Ordenes_de_Venta_Anuladas";
    }
    $pageOutput="<table cellpadding='0' cellspacing='0' id='".$tableId."'>".$pageHeader.$pageBody."</table>";
    echo $pageOutput;
    if ($salesOrderType == 'authorization_pending_period' && $bool_autorizar_permission){
      echo $this->Form->input('powerselector2'.$salesOrderType,['class'=>'powerselector','checked'=>true,'style'=>'width:5em;','label'=>['text'=>'Seleccionar/Deseleccionar Ordenes de Venta para autorizar','style'=>'padding-left:5em;'],'format' => ['before', 'input', 'between', 'label', 'after', 'error' ]]);
    }
    elseif ($salesOrderType == 'authorization_pending_before_period' && $bool_autorizar_permission){
      echo $this->Form->input('powerselector2'.$salesOrderType,['class'=>'powerselector','checked'=>true,'style'=>'width:5em;','label'=>['text'=>'Seleccionar/Deseleccionar Ordenes de Venta para autorizar','style'=>'padding-left:5em;'],'format' => ['before', 'input', 'between', 'label', 'after', 'error' ]]);
    }
    elseif ($salesOrderType == 'authorization_pending_after_period' && $bool_autorizar_permission){
      echo $this->Form->input('powerselector2'.$salesOrderType,['class'=>'powerselector','checked'=>true,'style'=>'width:5em;','label'=>['text'=>'Seleccionar/Deseleccionar Ordenes de Venta para autorizar','style'=>'padding-left:5em;'],'format' => ['before', 'input', 'between', 'label', 'after', 'error' ]]);
    }
    echo '</div>';
    $excelOutput.='<table id="'.$tableId.'">'.$excelHeader.$excelBody.'</table>';
    
  }
	echo $this->Form->end(); 
	
	
	
	$_SESSION['resumenOrdenesDeVenta'] = $excelOutput;
?>
</div>