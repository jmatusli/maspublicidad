<div class="productionOrders index">
<?php 
	echo "<h2>".__('Production Orders')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate));
			echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate));
			echo $this->Form->input('Report.production_order_display',array('label'=>__('Mostrar Ordenes'),'options'=>$productionOrderOptions,'default'=>$production_order_display));
		echo "</fieldset>";
		echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
		echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
	echo "<br/>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumen'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('New Production Order'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_salesorder_index_permission){
			echo "<li>".$this->Html->link(__('List Sales Orders'), array('controller' => 'sales_orders', 'action' => 'index'))." </li>";
		}
		if ($bool_salesorder_add_permission){
			echo "<li>".$this->Html->link(__('New Sales Order'), array('controller' => 'sales_orders', 'action' => 'add'))." </li>";
		}
	echo "</ul>";
?>
</div>
<div>
<?php
	$pageHeader="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('production_order_date')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('production_order_code')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('sales_order_id',__('Sales Order'))."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('bool_annulled')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('url_doc','Documento Diseño')."</th>";
			$pageHeader.="<th>% Listo</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader="<thead>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('production_order_date')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('production_order_code')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('sales_order_id',__('Sales Order'))."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('bool_annulled')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('url_doc')."</th>";
			$excelHeader.="<th>% Listo</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";
	
	//echo "bool annulled is ".$bool_annul_permission."<br/>";

	foreach ($productionOrders as $productionOrder){ 
		if ($production_order_display==0||($production_order_display==1&&$productionOrder['ProductionOrder']['percentage_processed']==100)||($production_order_display==2&&$productionOrder['ProductionOrder']['percentage_processed']<100)){
			$productionOrderDateTime=new DateTime($productionOrder['ProductionOrder']['production_order_date']);
			$pageRow="";		
				$pageRow.="<td>".$productionOrderDateTime->format('d-m-Y')."</td>";
				$pageRow.="<td>".$this->Html->link($productionOrder['ProductionOrder']['production_order_code'], array('action' => 'view', $productionOrder['ProductionOrder']['id']))."</td>";
				$pageRow.="<td>".$this->Html->link($productionOrder['SalesOrder']['sales_order_code'], array('controller' => 'sales_orders', 'action' => 'view', $productionOrder['SalesOrder']['id']))."</td>";
				$pageRow.="<td>".h($productionOrder['ProductionOrder']['bool_annulled']?__('Yes'):__('No'))."</td>";
				if (!empty($productionOrder['ProductionOrder']['url_doc'])){
					$pageRow.="<td>".$this->Html->url("/").h($productionOrder['ProductionOrder']['url_doc'])."</td>";
				}
				else {
					$pageRow.="<td>-</td>";
				}
				$pageRow.="<td>".h($productionOrder['ProductionOrder']['percentage_processed'])."</td>";
			$excelBody.="<tr>".$pageRow."</tr>";
				$pageRow.="<td class='actions'>";
					if ($bool_edit_permission){
						$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $productionOrder['ProductionOrder']['id']));
					}
					//if ($bool_delete_permission){
					//	$pageRow.=$this->Form->postLink(__('Delete'), array('action' => 'delete', $productionOrder['ProductionOrder']['id']), array(), __('Está seguro que quiere eliminar orden de producción # %s?', $productionOrder['ProductionOrder']['production_order_code']));
					//}
					//if ($bool_annul_permission){
					//	$pageRow.=$this->Form->postLink(__('Anular'), array('action' => 'annul', $productionOrder['ProductionOrder']['id']), array(), __('Está seguro que quiere anular orden de producción # %s?', $productionOrder['ProductionOrder']['production_order_code']));
					//}
				$pageRow.="</td>";

			if (!$productionOrder['ProductionOrder']['bool_annulled']){
				$pageBody.="<tr>".$pageRow."</tr>";
			}
			else {
				$pageBody.="<tr class='italic'>".$pageRow."</tr>";
			}
		}
	}

	$pageTotalRow="";
	//$pageTotalRow.="<tr class='totalrow'>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//$pageTotalRow.="</tr>";

	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	$table_id="ordenes_produccion";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo $pageOutput;
	$excelOutput="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	$_SESSION['resumen'] = $excelOutput;
?>
</div>
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