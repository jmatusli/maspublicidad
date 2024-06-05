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
			$(this).number(true,0,'.',',');
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


<?php 
	$excelOutput='';
  foreach ($salesOrders as $salesOrderType=>$salesOrdersData){
    if ($salesOrderType == 'authorization_pending'){
      $tableId="Ordenes_de_Venta_No_Autorizadas";
    }
    elseif ($salesOrderType == 'authorized') {
      $tableId="Ordenes_de_Venta_Autorizadas";
    }
    $pageHeader="";
    $excelHeader="";
    $pageHeader.="<thead>";
      $pageHeader.="<tr>";
        if (in_array($salesOrderType,['authorization_pending','authorization_pending','authorization_pending']) && $bool_autorizar_permission){
          $pageHeader.="<th>Seleccione</th>";
        }
        if ($userId==0){
          $pageHeader.="<th>".$this->Paginator->sort('user_id','Vendedor')."</th>";
        }
        $pageHeader.="<th>".$this->Paginator->sort('sales_order_date','Fecha Orden')."</th>";
        $pageHeader.="<th>".$this->Paginator->sort('sales_order_date','Fecha Autorización')."</th>";
        $pageHeader.="<th>".$this->Paginator->sort('sales_order_code','# Orden Venta')."</th>";
        $pageHeader.="<th>Orden de Producción</th>";
        $pageHeader.="<th>Facturas relacionadas</th>";
        $pageHeader.="<th>".$this->Paginator->sort('bool_completely_delivered')."</th>";
        $pageHeader.='<th>Producto</th>';
        $pageHeader.='<th>Cantidad</th>';
        //$pageHeader.="<th>Cliente</th>";
        //$pageHeader.="<th class='centered'>".$this->Paginator->sort('price_subtotal')."</th>";
        $pageHeader.="<th>Departamento</th>";
        $pageHeader.="<th style='width:10%;' class='centered'>Estado</th>";
        //$pageHeader.="<th class='actions'>".__('Actions')."</th>";
      $pageHeader.="</tr>";
    $pageHeader.="</thead>";
    $excelHeader.="<thead>";		
      if ($userId==0){
        $excelHeader.="<tr><th colspan='6' align='center'>".COMPANY_NAME."</th></tr>";	
        $excelHeader.="<tr><th colspan='6' align='center'>Reporte de Producción pendiente</th></tr>";
        $excelHeader.="<tr>";
          $excelHeader.="<th>".$this->Paginator->sort('user_id','Vendedor')."</th>";
      }
      else {
        $excelHeader.="<tr><th colspan='6' align='center'>".COMPANY_NAME."</th></tr>";	
        $excelHeader.="<tr><th colspan='6' align='center'>Reporte de Producción pendiente</th></tr>";
        $excelHeader.="<tr>";
      }
        $excelHeader.='<th>Fecha Orden</th>';
        $excelHeader.='<th>Fecha Autorización</th>';
        $excelHeader.='<th># Orden</th>';
        $excelHeader.='<th>Orden de Producción</th>';
        $excelHeader.="<th>Facturas relacionadas</th>";
        $excelHeader.="<th>".$this->Paginator->sort('bool_completely_delivered')."</th>";
        $excelHeader.='<th>Producto</th>';
        $excelHeader.='<th>Cant.</th>';
        //$excelHeader.="<th>".$this->Paginator->sort('client_id')."</th>";
        //$excelHeader.="<th>".$this->Paginator->sort('price_subtotal')."</th>";
        $excelHeader.="<th>Departamento</th>";
        $excelHeader.="<th>Estado</th>";
      $excelHeader.="</tr>";
    $excelHeader.="</thead>";

    $pageBody="";
    $excelBody="";

    $subtotalCS=0;
    $subtotalUSD=0;
    $statustotalCS=0;
    $statustotalUSD=0;
    
    if ($userRoleId == ROLE_ADMIN || $canSeeExecutiveTables){
      $salesOrdersByDepartment=[];
      $salesOrdersByUser=[];
    }
    foreach ($salesOrdersData as $salesOrder){ 
      //pr($salesOrder);
      if (
        $authorizedOptionId == AUTHORIZATION_ALL 
        || ($authorizedOptionId == AUTHORIZATION_ONLY && $salesOrder['SalesOrder']['bool_authorized'])
        || ($authorizedOptionId == AUTHORIZATION_PENDING && !$salesOrder['SalesOrder']['bool_authorized'])
      ){
        
        if ($userRoleId == ROLE_ADMIN || $canSeeExecutiveTables){
          //if ($salesOrder['VendorUser']['id'] == 23){
          //  echo 'salesorder id is '.$salesOrder['SalesOrder']['sales_order_code'].'<br/>';
          //  pr($salesOrder);
          //}
          if (!empty($salesOrder['SalesOrderProduct'])){
            if (!array_key_exists($salesOrder['VendorUser']['id'],$salesOrdersByUser)){
              $salesOrdersByUser[$salesOrder['VendorUser']['id']]=[
                'total_sales_orders'=>0,
                'total_products'=>0,
              ];
            }
            $salesOrdersByUser[$salesOrder['VendorUser']['id']]['total_sales_orders']+=1;
            
            $departmentsRegisteredForSalesOrder=[];
            foreach ($salesOrder['SalesOrderProduct'] as $salesOrderProduct){
              if (!empty($salesOrderProduct['ProductionOrderProductStatus']) && !array_key_exists($salesOrderProduct['ProductionOrderProductStatus']['department_id'],$salesOrdersByDepartment)){
                $salesOrdersByDepartment[$salesOrderProduct['ProductionOrderProductStatus']['department_id']]=[
                  'total_sales_orders'=>0,
                  'total_products'=>0,
                ];
              }
              $salesOrdersByUser[$salesOrder['VendorUser']['id']]['total_products']+=$salesOrderProduct['product_quantity'];
              if (!empty($salesOrderProduct['ProductionOrderProductStatus'])){
                $salesOrdersByDepartment[$salesOrderProduct['ProductionOrderProductStatus']['department_id']]['total_products']+=$salesOrderProduct['product_quantity'];
                if (!in_array($salesOrderProduct['ProductionOrderProductStatus']['department_id'],$departmentsRegisteredForSalesOrder)){
                  $salesOrdersByDepartment[$salesOrderProduct['ProductionOrderProductStatus']['department_id']]['total_sales_orders']+=1;
                  $departmentsRegisteredForSalesOrder[]=$salesOrderProduct['ProductionOrderProductStatus']['department_id'];
                }
              }
            }
          }  
        }
        
        
        
        $salesOrderDateTime=new DateTime($salesOrder['SalesOrder']['sales_order_date']);
        $authorizationDateTime=(empty($salesOrder['SalesOrder']['authorization_datetime'])?null:(new DateTime($salesOrder['SalesOrder']['authorization_datetime'])));
        /*
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
        */
        $salesOrderProductNumber=0;
        $rowSpan=count($salesOrder['SalesOrderProduct']);
        if ($departmentId > 0){
          $rowSpan=0;
          foreach ($salesOrder['SalesOrderProduct'] as $salesOrderProduct){
            if (
              !empty($salesOrderProduct['ProductionOrderProductStatus']) && 
              $salesOrderProduct['ProductionOrderProductStatus']['department_id'] == $departmentId
            ){
              $rowSpan++;
            }
          }  
        }
        foreach ($salesOrder['SalesOrderProduct'] as $salesOrderProduct){
          if (
            $departmentId == 0 || 
            (
              !empty($salesOrderProduct['ProductionOrderProductStatus']) && 
              $salesOrderProduct['ProductionOrderProductStatus']['department_id'] == $departmentId
            )
          ){
            $pageRow="";
            if ($salesOrderProductNumber == 0){
              //if (in_array($salesOrderType,['authorization_pending','authorization_pending','authorization_pending']) && $bool_autorizar_permission){
              //  $pageRow.="<td class='selector'>".$this->Form->input('Report.selector.'.$salesOrder['SalesOrder']['id'],['checked'=>true,'label'=>false])."</td>";
              //}
              if ($userId == 0){
                $pageRow.='<td rowspan="'.count($salesOrder['SalesOrderProduct']).'">'.$this->Html->link($salesOrder['VendorUser']['first_name']." ".$salesOrder['VendorUser']['last_name'], ['controller' => 'users', 'action' => 'view', $salesOrder['VendorUser']['id']]).'</td>';
              }
              $pageRow.='<td rowspan="'.$rowSpan.'">'.$salesOrderDateTime->format('d-m-Y').'</td>';
              $pageRow.='<td rowspan="'.$rowSpan.'">'.(empty($authorizationDateTime)?'-':($authorizationDateTime->format('d-m-Y'))).'</td>';
              $pageRow.='<td rowspan="'.$rowSpan.'">'.$this->Html->Link($salesOrder['SalesOrder']['sales_order_code'],['action'=>'view',$salesOrder['SalesOrder']['id']]).'</td>';
              $pageRow.='<td rowspan="'.$rowSpan.'">';
                if ($salesOrderType == 'authorized'){
                  //pr($salesOrder['ProductionOrder']);
                  if (empty($salesOrder['ProductionOrder'])){
                    $pageRow.=$this->Html->link('Orden de Producción', ['controller'=>'productionOrders','action' => 'crear',$salesOrder['SalesOrder']['id']],['target'=>'_blank','class'=>'btn btn-primary']);
                  }
                  else {
                    $pageRow.=$this->Html->link($salesOrder['ProductionOrder'][0]['production_order_code'], ['controller'=>'productionOrders','action' => 'detalle',$salesOrder['ProductionOrder'][0]['id']]);
                  }
                }
              $pageRow.='</td>';
              if (empty($salesOrder['InvoiceSalesOrder'])){
                $pageRow.='<td rowspan="'.$rowSpan.'">-</td>';
              }
              else {
                $pageRow.='<td rowspan="'.$rowSpan.'">';
                for ($iso=0;$iso<count($salesOrder['InvoiceSalesOrder']);$iso++){
                  $pageRow.=$this->Html->Link($salesOrder['InvoiceSalesOrder'][$iso]['Invoice']['invoice_code'],['controller'=>'invoices','action'=>'detalle',$salesOrder['InvoiceSalesOrder'][$iso]['Invoice']['id']])."";
                  if ($iso<count($salesOrder['InvoiceSalesOrder'])-1){
                    $pageRow.="<br/>";
                  }
                }
                $pageRow.='</td>';
              }
              $pageRow.='<td rowspan="'.$rowSpan.'">'.($salesOrder['SalesOrder']['bool_completely_delivered']?__('Yes'):('No')).'</td>';
            }
            $pageRow.='<td>'.(
              empty($salesOrderProduct['ProductionOrderProductStatus'])?
              $salesOrderProduct['product_description']:
              $this->Html->Link($salesOrderProduct['product_description'],['controller'=>'ProductionOrderProducts','action'=>'procesar',$salesOrderProduct['ProductionOrderProduct'][0]['id']])
            ).'</td>';
            $pageRow.='<td>'.$salesOrderProduct['product_quantity'].'</td>';
            //$pageRow.='<td class="centered">'.$salesOrderProduct['SalesOrderProductStatus']['status'].'</td>';
            //if (!empty($salesOrderProduct['ProductionOrderProductStatus'])){
            //  pr($salesOrderProduct['ProductionOrderProductStatus']);
            //}
            $pageRow.='<td>'.(empty($salesOrderProduct['ProductionOrderProductStatus'])?"-":$departments[$salesOrderProduct['ProductionOrderProductStatus']['department_id']]).'</td>';
            $pageRow.='<td>'.(empty($salesOrderProduct['ProductionOrderProductStatus'])?"-":$salesOrderProduct['ProductionOrderProductStatus']['department_state_name']).'</td>';
            $excelBody.="<tr>".$pageRow."</tr>";
           
            $pageBody.="<tr>".$pageRow."</tr>";
          }
          $salesOrderProductNumber++;
        }
      }
    }
    $pageTotalRow="";
  /*  
    if ($currencyId==CURRENCY_CS){
      $pageTotalRow.="<tr class='totalrow'>";
        if (in_array($salesOrderType,['authorization_pending']) && $bool_autorizar_permission){
          $pageTotalRow.="<td></td>";
        }
        $pageTotalRow.="<td>Total C$</td>";
        if ($userId==0){
          $pageTotalRow.="<td></td>";
        }
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        //$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$subtotalCS."</span></td>";
        //$status=0;
        //if ($subtotalCS>0){
        //  $status=$statustotalCS/$subtotalCS;
        //}
        //$pageTotalRow.="<td class='percentage'><span class='amountright'>".$status."</span></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
      $pageTotalRow.="</tr>";
    }
    if ($currencyId==CURRENCY_USD){
      $pageTotalRow.="<tr class='totalrow'>";
        if (in_array($salesOrderType,['authorization_pending','authorization_pending','authorization_pending']) && $bool_autorizar_permission){
          $pageTotalRow.="<td></td>";
        }
        $pageTotalRow.="<td>Total US$</td>";
        if ($userId==0){
          $pageTotalRow.="<td></td>";
        }
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
        //$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$subtotalUSD."</span></td>";
        //$status=0;
        //if ($subtotalUSD>0){
        //  $status=$statustotalUSD/$subtotalUSD;
        //}
        //$pageTotalRow.="<td class='percentage'><span class='amountright'>".$status."</span></td>";
        $pageTotalRow.="<td></td>";
        $pageTotalRow.="<td></td>";
      $pageTotalRow.="</tr>";
    }
  */  
    $pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
    $pageOutput="<table id='".$tableId."'>".$pageHeader.$pageBody."</table>";
    
    if ($userRoleId == ROLE_ADMIN || $canSeeExecutiveTables){
      $departmentTableHead='';
      $departmentTableHead.='<thead>';
        $departmentTableHead.='<tr>';
          $departmentTableHead.='<th>Departamento</th>';
          $departmentTableHead.='<th># OV</th>';
          $departmentTableHead.='<th>$ Productos</th>';
        $departmentTableHead.='</tr>';
      $departmentTableHead.='</thead>';
      $departmentTableRows='';
      $salesOrderTotal=0;
      $productTotal=0;
      foreach ($salesOrdersByDepartment as $currentDepartmentId =>$departmentSummary){
        $salesOrderTotal+=$departmentSummary['total_sales_orders'];
        $productTotal+=$departmentSummary['total_products'];
      
        $departmentTableRows.='<tr>';
          $departmentTableRows.='<td>'.$departments[$currentDepartmentId].'</td>';
          $departmentTableRows.='<td class="number"><span class="amountright">'.$departmentSummary['total_sales_orders'].'</span></td>';
          $departmentTableRows.='<td class="number"><span class="amountright">'.$departmentSummary['total_products'].'</span></td>';
        $departmentTableRows.='</tr>';
      }
      $departmentTotalRow='';
      $departmentTotalRow.='<tr class="totalrow">';
        $departmentTotalRow.='<td>Total Departamentos</td>';
        $departmentTotalRow.='<td class="number"><span class="amountright">'.$salesOrderTotal.'</span></td>';
        $departmentTotalRow.='<td class="number"><span class="amountright">'.$productTotal.'</span></td>';
      $departmentTotalRow.='</tr>';
      $departmentTableBody=$departmentTotalRow.$departmentTableRows.$departmentTotalRow;
      $executiveSummaryByDepartment='<table id="resumen_por_departamento">'.$departmentTableHead.$departmentTableBody.'</table>';
      $excelOutput.=$executiveSummaryByDepartment;
      
      
      $userTableHead='';
      $userTableHead.='<thead>';
        $userTableHead.='<tr>';
          $userTableHead.='<th>Usuario</th>';
          $userTableHead.='<th># OV</th>';
          $userTableHead.='<th>$ Productos</th>';
        $userTableHead.='</tr>';
      $userTableHead.='</thead>';
      $userTableRows='';
      $salesOrderTotal=0;
      $productTotal=0;
      foreach ($salesOrdersByUser as $currentUserId =>$userSummary){
        $salesOrderTotal+=$userSummary['total_sales_orders'];
        $productTotal+=$userSummary['total_products'];
      
        $userTableRows.='<tr>';
          $userTableRows.='<td>'.$users[$currentUserId].'</td>';
          $userTableRows.='<td class="number"><span class="amountright">'.$userSummary['total_sales_orders'].'</span></td>';
          $userTableRows.='<td class="number"><span class="amountright">'.$userSummary['total_products'].'</td>';
        $userTableRows.='</span></tr>';
      }
      $userTotalRow='';
      $userTotalRow.='<tr class="totalrow">';
        $userTotalRow.='<td>Total usuarios</td>';
        $userTotalRow.='<td class="number"><span class="amountright">'.$salesOrderTotal.'</span></td>';
        $userTotalRow.='<td class="number"><span class="amountright">'.$productTotal.'</span></td>';
      $userTotalRow.='</tr>';
      $userTableBody=$userTotalRow.$userTableRows.$userTotalRow;
      $executiveSummaryByUser='<table id="resumen_por_usuario">'.$userTableHead.$userTableBody.'</table>';
      $excelOutput.=$executiveSummaryByUser;
    }
    
    echo '<div class="salesOrders reporte fullwidth">';
      echo "<h1>Reporte Producción Pendiente</h1>";
      echo "<div class='container-fluid'>";
        echo "<div class='row'>";
          echo '<div class="'.($userRoleId == ROLE_ADMIN || $canSeeExecutiveTables?"col-sm-6":"col-sm-12").'">';		
            echo $this->Form->create('Report');
            echo "<fieldset>";
              echo "<br/>";
              if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) { 
                echo $this->Form->input('Report.user_id',['label'=>'Mostrar Usuario','options'=>$users,'value'=>$userId,'empty'=>['0'=>'-- Todos Usuarios --']]);
              }
              else {
                echo $this->Form->input('Report.user_id',['label'=>__('Mostrar Usuario'),'options'=>$users,'value'=>$userId]);
              }
              echo $this->Form->input('Report.department_id',['default'=>$departmentId,'empty'=>[0=>'-- Departamento --']]);
              echo $this->Form->input('Report.authorized_option_id',['label'=>__('Mostrar Autorizados'),'default'=>$authorizedOptionId]);
            echo "</fieldset>";
            echo $this->Form->submit(__('Refresh'),['name'=>'refresh', 'id'=>'refresh','div'=>['class'=>'submit']]); 
            echo "<br/>";	
            echo $this->Html->link(__('Guardar como Excel'), ['action' => 'guardarReporteProducciónPendiente'], ['class' => 'btn btn-primary']);
          echo '</div>';  
          if ($userRoleId == ROLE_ADMIN || $canSeeExecutiveTables){
            echo '<div class="col-sm-6">';		
              echo '<h2>Resumen Ejecutivo</h2>';
              echo $executiveSummaryByDepartment;
              echo $executiveSummaryByUser;
            echo '</div>';  
          }
        echo "</div>";
      echo "</div>";

    //$startDateTime=new DateTime($startDate);
    //$endDateTime=new DateTime($endDate);
    echo '</div>';
    
    
    //echo 'department id is '.$departmentId.'<br/>';
    echo '<div style="clear:left;">';
      echo '<div class="salesOrderTypeContainer">';
      if ($salesOrderType == 'authorization_pending'){
        echo '<h2>Ordenes de Venta esperando autorización</h2>';
        echo $this->Form->input('powerselector1'.$salesOrderType,['class'=>'powerselector','checked'=>true,'style'=>'width:5em;','label'=>['text'=>'Seleccionar/Deseleccionar Ordenes de Venta para autorizar','style'=>'padding-left:5em;'],'format' => ['before', 'input', 'between', 'label', 'after', 'error']]);
        $tableId="Ordenes_de_Venta_No_Autorizadas";
      }
      elseif ($salesOrderType == 'authorized') {
        echo '<h2>Productos de Ordenes de Venta autorizadas'.($departmentId > 0 ? (' para departamento '.$departments[$departmentId]):'').'</h2>';
        $tableId="Ordenes_de_Venta_Autorizadas";
      }
      echo $pageOutput;
      if ($salesOrderType == 'authorization_pending' && $bool_autorizar_permission){
        echo $this->Form->input('powerselector2'.$salesOrderType,['class'=>'powerselector','checked'=>true,'style'=>'width:5em;','label'=>['text'=>'Seleccionar/Deseleccionar Ordenes de Venta para autorizar','style'=>'padding-left:5em;'],'format' => ['before', 'input', 'between', 'label', 'after', 'error' ]]);
      }
      	echo $this->Form->end(); 
      echo '</div>';
      $excelOutput.='<table id="'.$tableId.'">'.$excelHeader.$excelBody.'</table>';
      $_SESSION['reporteProduccionPendiente'] = $excelOutput;
    echo '</div>';
  }

	
	
	
	
?>
