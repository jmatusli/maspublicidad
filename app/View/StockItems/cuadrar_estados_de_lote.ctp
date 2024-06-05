<div class="stockItems view report">

<?php
	
	echo "<button id='onlyProblems' type='button'>".__('Only Show Problems')."</button>";
	echo "<h3>".$this->Html->link('Recreate All StockItemLogs',array('action' => 'recreateAllStockItemLogs'))."</h3>";

	$otherMaterialTable="";	
	
	$otherMaterialTable="<table id='finished'>";
	
	$otherMaterialTable.="<thead>";
	$otherMaterialTable.="<tr>";
	$otherMaterialTable.="<th>StockItem Id</th>";
	$otherMaterialTable.="<th>Product Name</th>";
	$otherMaterialTable.="<th>Original Quantity (StockItem)</th>";
	$otherMaterialTable.="<th>Original based on Movements</th>";
	
	$otherMaterialTable.="<th>Quantity Exited</th>";
	$otherMaterialTable.="<th>Remaining based on Movements</th>";
	$otherMaterialTable.="<th>Remaining based (StockItem)</th>";
	$otherMaterialTable.="<th>Remaining based on StockitemLog</th>";
	$otherMaterialTable.="<th>actions</th>";
	$otherMaterialTable.="</tr>";
	$otherMaterialTable.="</thead>";
	
	$otherMaterialTable.="<tbody>";
	
	$totalOriginalStockItem=0;
	$totalOriginalMovement=0;
	$totalExited=0;
	$totalRemainingStockItem=0;
	$totalRemainingStockItemLog=0;
	
	$productname="";
	foreach ($allOtherStockItems as $stockitem){
		$remainingMovement=$stockitem['StockItem']['total_moved_in']-$stockitem['StockItem']['total_moved_out'];
		if ($productname!=$stockitem['InventoryProduct']['name']){
			if ($productname!=""){
				$otherMaterialTable.="<tr class='totalrow'>";
				$otherMaterialTable.="<td>Total</td>";
				$otherMaterialTable.="<td>".$productname."</td>";
				$otherMaterialTable.="<td".($totalOriginalStockItem!=$totalOriginalMovement?" class='warning'":"").">".$totalOriginalStockItem."</td>";
				$otherMaterialTable.="<td".($totalOriginalStockItem!=$totalOriginalMovement?" class='warning'":"").">".$totalOriginalMovement."</td>";
				$otherMaterialTable.="<td>".$totalExited."</td>";
				$otherMaterialTable.="<td".($totalRemainingStockItem!=($totalOriginalMovement-$totalExited)?" class='warning'":"").">".($totalOriginalMovement-$totalExited)."</td>";
				$otherMaterialTable.="<td".(($totalRemainingStockItem!=($totalOriginalMovement-$totalExited))||($totalRemainingStockItem!=$totalRemainingStockItemLog)?" class='warning'":"").">".$totalRemainingStockItem."</td>";
				$otherMaterialTable.="<td".($totalRemainingStockItem!=$totalRemainingStockItemLog?" class='warning'":"").">".$totalRemainingStockItemLog."</td>";
				$otherMaterialTable.="</tr>";
			}
			
			$totalOriginalStockItem=$stockitem['StockItem']['product_original_quantity'];
			$totalOriginalMovement=$stockitem['StockItem']['total_moved_in'];
			$totalExited=$stockitem['StockItem']['total_moved_out'];
			$totalRemainingStockItem=$stockitem['StockItem']['product_remaining_quantity'];
			$totalRemainingStockItemLog=$stockitem['StockItem']['latest_log_quantity'];
			
			$productname=$stockitem['InventoryProduct']['name'];
		}
		else {
			$totalOriginalStockItem+=$stockitem['StockItem']['product_original_quantity'];
			$totalOriginalMovement+=$stockitem['StockItem']['total_moved_in'];
			$totalExited+=$stockitem['StockItem']['total_moved_out'];
			$totalRemainingStockItem+=$stockitem['StockItem']['product_remaining_quantity'];
			$totalRemainingStockItemLog+=$stockitem['StockItem']['latest_log_quantity'];
		}
		
		$otherMaterialTable.="<tr>";
		$otherMaterialTable.="<td>".$this->Html->link($stockitem['StockItem']['id'],array('action' => 'view', $stockitem['StockItem']['id']))."</td>";
		$otherMaterialTable.="<td>".$stockitem['InventoryProduct']['name']."</td>";
		$otherMaterialTable.="<td".($stockitem['StockItem']['product_original_quantity']!=$stockitem['StockItem']['total_moved_in']?" class='warning'":"").">".$stockitem['StockItem']['product_original_quantity']."</td>";
		$otherMaterialTable.="<td".($stockitem['StockItem']['product_original_quantity']!=$stockitem['StockItem']['total_moved_in']?" class='warning'":"").">".$stockitem['StockItem']['total_moved_in']."</td>";
		$otherMaterialTable.="<td>".$stockitem['StockItem']['total_moved_out']."</td>";
		$otherMaterialTable.="<td".($stockitem['StockItem']['product_remaining_quantity']!=$remainingMovement?" class='warning'":"").">".$remainingMovement."</td>";
		$otherMaterialTable.="<td".(($stockitem['StockItem']['product_remaining_quantity']!=$remainingMovement)||($stockitem['StockItem']['product_remaining_quantity']!=$stockitem['StockItem']['latest_log_quantity'])?" class='warning'":"").">".$stockitem['StockItem']['product_remaining_quantity']."</td>";
		$otherMaterialTable.="<td".($stockitem['StockItem']['product_remaining_quantity']!=$stockitem['StockItem']['latest_log_quantity']?" class='warning'":"").">".$stockitem['StockItem']['latest_log_quantity']."</td>";
		//if ($stockitem['StockItem']['product_remaining_quantity']!=$stockitem['StockItem']['latest_log_quantity'] && ($stockitem['StockItem']['product_remaining_quantity']==$remainingMovement)){
			$otherMaterialTable.="<td>".$this->Html->link('Recreate StockItemLogs',array('action' => 'recreateStockItemLogsForSquaring', $stockitem['StockItem']['id']))."</td>";
		//}
		//else {
		//	$otherMaterialTable.="<td></td>";
		//}
		$otherMaterialTable.="</tr>";
	}
	$otherMaterialTable.="<tr class='totalrow'>";
			$otherMaterialTable.="<td>Total</td>";
			$otherMaterialTable.="<td></td>";
			$otherMaterialTable.="<td>".$totalOriginalStockItem."</td>";
			$otherMaterialTable.="<td>".$totalOriginalMovement."</td>";
			
			$otherMaterialTable.="<td>".$totalExited."</td>";
			$otherMaterialTable.="<td>".($totalOriginalMovement-$totalExited)."</td>";
			$otherMaterialTable.="<td>".$totalRemainingStockItem."</td>";
			$otherMaterialTable.="<td>".$totalRemainingStockItemLog."</td>";
			$otherMaterialTable.="</tr>";
	$otherMaterialTable.="</tbody>";
	$otherMaterialTable.="</table>";
	
	//echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarReporteProductos'), array( 'class' => 'btn btn-primary')); 

	echo "<h2>".__('Other Materials')."</h2>"; 
	echo $otherMaterialTable; 
	
	$_SESSION['productsReport'] = $otherMaterialTable;
?>

<script>
	$('#onlyProblems').click(function(){
		$("tbody tr:not(.totalrow)").each(function() {
			$(this).hide();
		});
		$("td.warning").each(function() {
			$(this).parent().show();
		});
	});
</script>