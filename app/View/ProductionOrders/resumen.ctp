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
	});
</script>
<div class="productionOrders index">
<?php 
	echo "<h2>".__('Production Orders')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo $this->Form->input('Report.startdate',['type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015,'maxYear'=>date('Y')]);
			echo $this->Form->input('Report.enddate',['type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015,'maxYear'=>date('Y')]);
		echo "</fieldset>";
		echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
		echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
	echo "<br/>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), ['action' => 'guardarResumenOrdenesDeProduccion'],['class' => 'btn btn-primary']);
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('New Production Order'), ['action' => 'crear'])."</li>";
		echo "<br/>";
		if ($bool_salesorder_index_permission){
			echo "<li>".$this->Html->link(__('List Sales Orders'), ['controller' => 'salesOrders', 'action' => 'index'])." </li>";
		}
		if ($bool_salesorder_add_permission){
			echo "<li>".$this->Html->link(__('New Sales Order'), ['controller' => 'salesOrders', 'action' => 'add'])." </li>";
		}
	echo "</ul>";
?>
</div>
<div>
<?php
	$tableHead='<thead>';
		$tableHead.='<tr>';
			$tableHead.='<th>'.$this->Paginator->sort('production_order_date').'</th>';
			$tableHead.='<th>'.$this->Paginator->sort('production_order_code').'</th>';
			$tableHead.='<th>'.$this->Paginator->sort('sales_order_id',__('Sales Order')).'</th>';
		$tableHead.='</tr>';
	$tableHead.='</thead>';
	$tableBody='';
	
	foreach ($productionOrders as $productionOrder){ 
		$productionOrderDateTime=new DateTime($productionOrder['ProductionOrder']['production_order_date']);
    
    $pageRow='';		
    $pageRow.='<td>'.$productionOrderDateTime->format('d-m-Y').'</td>';
    $pageRow.='<td>'.$this->Html->link($productionOrder['ProductionOrder']['production_order_code'], ['action' => 'detalle', $productionOrder['ProductionOrder']['id']]).($productionOrder['ProductionOrder']['bool_annulled']?' (Anulada)':'').'</td>';
    $pageRow.='<td>'.$this->Html->link($productionOrder['SalesOrder']['sales_order_code'], ['controller' => 'salesOrders', 'action' => 'view', $productionOrder['SalesOrder']['id']]).'</td>';
      
    if (!$productionOrder['ProductionOrder']['bool_annulled']){
      $tableBody.='<tr>'.$pageRow.'</tr>';
    }
    else {
      $tableBody.='<tr class="italic">'.$pageRow.'</tr>';
    }
	}

	$pageTotalRow='';
	//$pageTotalRow.='<tr class="totalrow">';
	//	$pageTotalRow.='<td></td>';
	//	$pageTotalRow.='<td></td>';
	//	$pageTotalRow.='<td></td>';
	//	$pageTotalRow.='<td></td>';
	//	$pageTotalRow.='<td></td>';
	//	$pageTotalRow.='<td></td>';
	//	$pageTotalRow.='<td></td>';
	//	$pageTotalRow.='<td></td>';
	//	$pageTotalRow.='<td></td>';
	//	$pageTotalRow.='<td></td>';
	//	$pageTotalRow.='<td></td>';
	//$pageTotalRow.='</tr>';

	$tableBody='<tbody>'.$pageTotalRow.$tableBody.$pageTotalRow.'</tbody>';
	$pageOutput='<table id="ordenes_produccion">'.$tableHead.$tableBody.'</table>';
	echo $pageOutput;
	$_SESSION['resumenOrdenesDeProduccion'] = $pageOutput;
?>
</div>