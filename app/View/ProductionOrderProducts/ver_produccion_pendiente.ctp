<div class="productionOrders index fullwidth">
<?php 
	echo "<h2>".__('Producción Pendiente')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo $this->Form->input('Report.department_id',array('label'=>__('Department'),'default'=>$departmentId,'empty'=>array('0'=>'Seleccione Departamento')));
		echo "</fieldset>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardar'), array( 'class' => 'btn btn-primary'));

	$pageHeader="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('Department.name','Departamento')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('product_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('sales_order_id',__('Sales Order'))."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('production_order_id',__('Production Order'))."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('product_quantity')."</th>";
			$pageHeader.="<th># Operaciones</th>";
			$pageHeader.="<th>Estado</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader="<thead>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('Departamento')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('product_id')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('sales_order_id',__('Sales Order'))."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('production_order_id',__('Production Order'))."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('product_quantity')."</th>";
			$excelHeader.="<th># Operaciones</th>";
			$excelHeader.="<th>Estado</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	foreach ($productionOrderProducts as $productionOrderProduct){ 
		$pageRow="";		
			$pageRow.="<td>";
			foreach ($productionOrderProduct['ProductionOrderProductDepartment'] as $productDepartment){
				$pageRow.=$this->Html->link($productDepartment['Department']['name'], array('controller' => 'departments', 'action' => 'view', $productDepartment['Department']['id']))."<br/>";
			}
			$pageRow.="</td>";
			$pageRow.="<td>".$this->Html->link($productionOrderProduct['Product']['name'], array('controller' => 'products', 'action' => 'view', $productionOrderProduct['Product']['id']))."</td>";
			$pageRow.="<td>".$this->Html->link($productionOrderProduct['ProductionOrder']['SalesOrder']['sales_order_code'], array('controller' => 'sales_orders', 'action' => 'view', $productionOrderProduct['ProductionOrder']['SalesOrder']['id']))."</td>";
			$pageRow.="<td>".$this->Html->link($productionOrderProduct['ProductionOrder']['production_order_code'], array('controller' => 'production_orders', 'action' => 'view', $productionOrderProduct['ProductionOrder']['id']))."</td>";
			$pageRow.="<td>".h($productionOrderProduct['ProductionOrderProduct']['product_quantity'])."</td>";
			if (!empty($productionOrderProduct['ProductionOrderProductOperationLocation'])){
				$pageRow.="<td>".count($productionOrderProduct['ProductionOrderProductOperationLocation'])."</td>";
			}
			else {
				$pageRow.="<td>1</td>";
			}
			//pr($productionOrderProduct['SalesOrderProduct']['SalesOrderProductStatus']);
			$pageRow.="<td>".h($productionOrderProduct['SalesOrderProduct']['SalesOrderProductStatus']['status'])."</td>";
		$excelBody.="<tr>".$pageRow."</tr>";
		$pageBody.="<tr>".$pageRow."</tr>";
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
	$table_id="";
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