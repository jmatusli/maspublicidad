<script>
	$('body').on('change','#ReportCurrencyId',function(){
		$('#ReportInventarioForm').submit();
	});
	
	function formatNumbers(){
		$("td.number").each(function(){
			$(this).number(true,0);
		});
	}
	
	function formatCurrencies(){
		var currencyName=$('#ReportCurrencyId option:selected').text();
		$("td.currency span.amountright").each(function(){
			$(this).number(true,2);
			
			$(this).parent().find('span.currency').text(currencyName);
		});
	}
	
	$(document).ready(function(){
		formatNumbers();
		formatCurrencies();
	});
</script>

<div class="stockItems inventory">
<?php 	
	echo "<h2>Inventario</h2>";

	echo $this->Form->create('Report'); 
	
	echo "<fieldset>"; 
		echo  $this->Form->input('Report.inventorydate',array('type'=>'date','label'=>__('Inventory Date'),'dateFormat'=>'DMY','default'=>$inventoryDate));
		echo  $this->Form->input('Report.currency_id',array('label'=>__('Mostrar valores en '),'default'=>$currency_id));
	echo  "</fieldset>";
	echo  "<button id='previousmonth' class='monthswitcher'>".__('Previous Month')."</button>";
	echo  "<button id='nextmonth' class='monthswitcher'>".__('Next Month')."</button>";
	echo $this->Form->end(__('Refresh')); 
	echo "<br/>";
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarReporteInventario'), array( 'class' => 'btn btn-primary')); 
	echo "<br/>";
	echo "<br/>";
	echo $this->Html->link(__('Hoja de Inventario'), array('action' => 'verPdfHojaInventario','ext'=>'pdf',$inventoryDate,$currency_id,$filename),array( 'class' => 'btn btn-primary','target'=>'blank')); 
	
	echo "<h2>Productos</h2>";
	//pr($inventoryProducts);
	$inventoryProductTable="";
	if (!empty($inventoryProducts)){
		$inventoryProductTable.="<table id='inventario' cellpadding='0' cellspacing='0'>";
			$inventoryProductTable.="<thead>";
				$inventoryProductTable.="<tr>";
					$inventoryProductTable.="<th>Familia de Producto</th>";
					$inventoryProductTable.="<th>".$this->Paginator->sort('InventoryProduct.name','Producto')."</th>";
					//if($userrole!=ROLE_FOREMAN) {
						$inventoryProductTable.="<th class='centered'>".__('Average Unit Price')."</th>";
					//}
					$inventoryProductTable.="<th class='centered'>".$this->Paginator->sort('Remaining')."</th>";
					//if($userrole!=ROLE_FOREMAN) {
						$inventoryProductTable.="<th class='centered'>".$this->Paginator->sort('Total Value')."</th>";
					//}
				$inventoryProductTable.="</tr>";
			$inventoryProductTable.="</thead>";
			$inventoryProductTable.="<tbody>";

			$valueInventoryProducts=0;
			$quantityInventoryProducts=0; 
			$tableRows="";
			foreach ($inventoryProducts as $stockItem){
				//pr($stockItem);
				$remaining="";
				$average="";
				$totalvalue="";
				if ($stockItem['0']['Remaining']!=""){
					$remaining= number_format($stockItem['0']['Remaining'],0,".",","); 
					//$packagingunit=$stockItem['InventoryProduct']['packaging_unit'];
					// if there are products and the value of packaging unit is not 0, show the number of packages
					//if ($packagingunit!=0){
					//	$numberpackagingunits=floor($stockItem['0']['Remaining']/$packagingunit);
					//	$leftovers=$stockItem['0']['Remaining']-$numberpackagingunits*$packagingunit;
					//	$remaining .= " (".number_format($numberpackagingunits,0,".",",")." ".__("packaging units");
					//	if ($leftovers >0){
					//		$remaining.= " ".__("and")." ".number_format($leftovers,0,".",",")." ".__("leftover units").")";
					//	}
					//	else {
					//		$remaining.=")";
					//	}
					//}
					$average=$stockItem['0']['Remaining']>0?number_format($stockItem['0']['Saldo']/$stockItem['0']['Remaining'],4,".",","):0;
					$totalValue=$stockItem['0']['Saldo'];
					$valueInventoryProducts+=$stockItem['0']['Saldo'];
					$quantityInventoryProducts+=$stockItem['0']['Remaining'];
				}
				else {
					$remaining= "0";
					$average="0";
					$totalValue="0";
				}
				$tableRows.="<tr>";
					$tableRows.="<td>".$this->Html->link($stockItem['InventoryProduct']['InventoryProductLine']['name'], array('controller' => 'inventory_product_lines', 'action' => 'view', $stockItem['InventoryProduct']['InventoryProductLine']['id']))."</td>";
					$tableRows.="<td>".$this->Html->link($stockItem['InventoryProduct']['name'].(!empty($stockItem['InventoryProduct']['code'])?" (".($stockItem['InventoryProduct']['code']).")":""), array('controller' => 'inventory_products', 'action' => 'view', $stockItem['InventoryProduct']['id']))."</td>";
					//if($userrole!=ROLE_FOREMAN) {
						$tableRows.="<td class='centered currency'><span class='currency'></span><span class='amountright'>".$average."</span></td>";
					//}
					$tableRows.="<td class='centered'>".$remaining."</td>";
					//if($userrole!=ROLE_FOREMAN) {
						$tableRows.="<td class='centered currency'><span class='currency'></span><span class='amountright'>".$totalValue."</span></td>";
					//}
				$tableRows.="</tr>";
			}
				$totalRow="";
				$totalRow.="<tr class='totalrow'>";
					$totalRow.="<td>Total</td>";
					$totalRow.="<td></td>";
					if($quantityInventoryProducts>0){
						$avg=$valueInventoryProducts/$quantityInventoryProducts;
					}
					else {
						$avg=0;
					}
					//if($userrole!=ROLE_FOREMAN) {
						$totalRow.="<td class='centered currency'><span class='currency'></span><span class='amountright'>".number_format($avg,2,".",",")."</span></td>";
					//}
					$totalRow.="<td class='centered number'>".$quantityInventoryProducts."</td>";
					//if($userrole!=ROLE_FOREMAN) {
						$totalRow.="<td class='centered currency'><span class='currency'></span><span class='amountright'>".number_format($valueInventoryProducts,2,".",",")."</span></td>";
					//}
				$totalRow.="</tr>";
				$inventoryProductTable.=$totalRow.$tableRows.$totalRow;
			$inventoryProductTable.="</tbody>";
		$inventoryProductTable.="</table>";
	}
	echo $inventoryProductTable;
	
	$_SESSION['inventoryReport'] = $inventoryProductTable;
?>

</div>