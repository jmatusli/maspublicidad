<div class="machines view">
<?php echo "<h2>".__('Machine')." ".$machine['Machine']['name']; ?></h2>
<?php 
	
	echo "<dl>";
		echo "<dt>".__('Description')."</dt>";
		if (!empty($machine['Machine']['description'])){
			echo "<dd>".h($machine['Machine']['description'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Activo')."</dt>";
		echo "<dd>".($machine['Machine']['bool_active']?"Activo":"Deshabilitado")."</dd>";
	echo "</dl>";
	
?>
	<?php echo $this->Form->create('Report'); ?>
	<fieldset>
	<?php
		echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate));
		echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate));
	?>
	</fieldset>
	<button id='previousmonth' class='monthswitcher'><?php echo __('Previous Month'); ?></button>
	<button id='nextmonth' class='monthswitcher'><?php echo __('Next Month'); ?></button>
	<?php echo $this->Form->end(__('Refresh')); ?>

</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){ 
			echo "<li>".$this->Html->link(__('Edit Machine'), array('action' => 'edit', $machine['Machine']['id']))."</li>";
			echo "<br/>";
		}
		if ($bool_delete_permission){ 		
			echo "<li>".$this->Form->postLink(__('Delete Machine'), array('action' => 'delete', $machine['Machine']['id']), array(), __('Está seguro que quiere eliminar la máquina %s?', $machine['Machine']['name']))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Machines'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Machine'), array('action' => 'add'))."</li>";
		echo "<br/>";
		
		//foreach ($otherMachines as $otherMachine){
		//	echo "<li>".$this->Html->link($otherMachine['Machine']['name'], array('controller' => 'machines', 'action' => 'view',$otherMachine['Machine']['id']))."</li>";
		//}
	echo "</ul>";	
?>
</div>
<div class="related">

<?php 
	if (!empty($producedProducts)){
		echo "<h3>Productos fabricados en la máquina en el período</h3>";
		echo "<table>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Raw Material')."</th>";
					echo "<th>".__('Finished Product')."</th>";
					foreach ($productionResultCodes as $productionResultCode){
						echo "<th class='centered'>".$productionResultCode['ProductionResultCode']['code']."</th>";
					}
					echo "<th class='centered'>".__('Total Value')."</th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
			
			$totalQuantityA=0;
			$totalQuantityB=0;
			$totalQuantityC=0;
			$totalValue=0;
			
			$productOverview="";
			foreach ($producedProducts as $producedProduct){
				$productOverview.="<tr>";
					$productOverview.="<td>".$this->Html->link($producedProduct['raw_material_name'], array('controller' => 'products','action' => 'view',$producedProduct['raw_material_id']))."</td>";
					$productOverview.="<td>".$this->Html->link($producedProduct['finished_product_name'], array('controller' => 'products','action' => 'view',$producedProduct['finished_product_id']))."</td>";
					$productOverview.="<td class='centered number'>".$producedProduct['produced_quantities'][PRODUCTION_RESULT_CODE_A]."</td>";
					$productOverview.="<td class='centered number'>".$producedProduct['produced_quantities'][PRODUCTION_RESULT_CODE_B]."</td>";
					$productOverview.="<td class='centered number'>".$producedProduct['produced_quantities'][PRODUCTION_RESULT_CODE_C]."</td>";
					$productOverview.="<td class='centered currency'><span>".$producedProduct['total_value']."</span></td>";
				$productOverview.="</tr>";
				$totalQuantityA+=$producedProduct['produced_quantities'][PRODUCTION_RESULT_CODE_A];
				$totalQuantityB+=$producedProduct['produced_quantities'][PRODUCTION_RESULT_CODE_B];
				$totalQuantityC+=$producedProduct['produced_quantities'][PRODUCTION_RESULT_CODE_C];
				$totalValue+=$producedProduct['total_value'];
			}
				$totalRows="";
				$totalRows.="<tr class='totalrow'>";
					$totalRows.="<td>Total</td>";
					$totalRows.="<td></td>";
					$totalRows.="<td class='centered number'>".$totalQuantityA."</td>";
					$totalRows.="<td class='centered number'>".$totalQuantityB."</td>";
					$totalRows.="<td class='centered number'>".$totalQuantityC."</td>";
					$totalRows.="<td class='centered currency'><span>".$totalValue."</span></td>";
				$totalRows.="</tr>";
			
				$totalRows.="<tr class='totalrow'>";
					$totalRows.="<td>Porcentajes</td>";
					$totalRows.="<td></td>";
					$totalRows.="<td class='centered percentage'><span>".(100*$totalQuantityA/($totalQuantityA+$totalQuantityB+$totalQuantityC))."</span></td>";
					$totalRows.="<td class='centered percentage'><span>".(100*$totalQuantityB/($totalQuantityA+$totalQuantityB+$totalQuantityC))."</span></td>";
					$totalRows.="<td class='centered percentage'><span>".(100*$totalQuantityC/($totalQuantityA+$totalQuantityB+$totalQuantityC))."</span></td>";
					$totalRows.="<td></td>";
				$totalRows.="</tr>";
			echo $totalRows.$productOverview.$totalRows;
			echo "</tbody>";
		echo "</table>";
	}


	if (!empty($producedProductsPerOperator)){
		echo "<h3>Productos fabricados en la máquina por cada operador en el período</h3>";
		echo "<table class='grid'>";
			echo "<thead>";
			// First the line with the raw material names
				echo "<tr>";
					echo "<th></th>";
					foreach ($producedProductsPerOperator[0]['rawmaterial'] as $rawMaterial){
						//pr($rawMaterial);
						echo "<th  class='centered' colspan='".$rawMaterialsUse[$rawMaterial['raw_material_id']]."'>".$rawMaterial['raw_material_name']."</th>";					
					}
				echo "</tr>";
			echo "</thead>";
					
			echo "<tbody>";
			// Then the line with the finished product names 
				echo "<tr>";
					echo "<td></td>";
					foreach ($producedProductsPerOperator[0]['rawmaterial'] as $rawMaterial){
						foreach ($rawMaterial['products'] as $product){
							if ($visibleArray[$rawMaterial['raw_material_id']][$product['finished_product_id']]['visible']>0){
								echo "<td class='centered' colspan='3'>".$product['finished_product_name']."</td>";					
							}
						}
					}
				echo "</tr>";

				// Then the line with the production result codes 
				echo "<tr>";
					echo "<td></td>";
					foreach ($producedProductsPerOperator[0]['rawmaterial'] as $rawMaterial){
						foreach ($rawMaterial['products'] as $product){
							if ($visibleArray[$rawMaterial['raw_material_id']][$product['finished_product_id']]['visible']>0){
								echo "<td class='centered'>A</td>";					
								echo "<td class='centered'>B</td>";
								echo "<td class='centered'>C</td>";
							}
						}
					}
				echo "</tr>";
			
				$totalsArray=array();
				//pr($producedProductsPerOperator);
				$firstrow=true;
				$operatorRows="";
				foreach ($producedProductsPerOperator as $operatorData){
					$operatorRow="";
					$productQuantityForRow=0;
					$operatorRow.="<tr>";
						$operatorRow.="<td>".$this->Html->link($operatorData['operator_name'], array('controller' => 'operators','action' => 'view',$operatorData['operator_id']))."</td>";
						$productCounter=0;
						foreach ($operatorData['rawmaterial'] as $rawMaterial){
							foreach ($rawMaterial['products'] as $product){
								if ($visibleArray[$rawMaterial['raw_material_id']][$product['finished_product_id']]['visible']>0){
									foreach($product['product_quantity'] as $quantity){
										if ($quantity>0){
											$operatorRow.="<td class='centered bold number'>".$quantity."</td>";
										}
										else {
											$operatorRow.="<td class='centered'>-</td>";
										}
										if ($firstrow){
											$totalsArray[$productCounter]=$quantity;
										}
										else{
											$totalsArray[$productCounter]+=$quantity;
										}
										$productQuantityForRow+=$quantity;
										$productCounter++;
									}
								}
							}
						}
						//pr($totalsArray);
						$firstrow=false;
					$operatorRow.="</tr>";
					if ($productQuantityForRow){
						$operatorRows.=$operatorRow;
					}
				}
				$totalRows="";
				$totalRows.="<tr class='totalrow'>";
					$totalRows.="<td>Total</td>";
					for ($i=0;$i<count($totalsArray);$i++){
						$totalRows.="<td class='centered number'>".$totalsArray[$i]."</td>";
					}
				$totalRows.="</tr>";
				
				$totalRows.="<tr class='totalrow'>";
					$totalRows.="<td>Porcentajes</td>";
					for ($i=0;$i<count($totalsArray);$i++){
						if ($i%3==0){
							$totalRows.="<td class='centered percentage'><span>".(100*$totalsArray[$i]/($totalsArray[$i]+$totalsArray[$i+1]+$totalsArray[$i+2]))."</span></td>";
						}
						elseif ($i%3==1){
							$totalRows.="<td class='centered percentage'><span>".(100*$totalsArray[$i]/($totalsArray[$i-1]+$totalsArray[$i]+$totalsArray[$i+1]))."</span></td>";
						}
						elseif ($i%3==2){
							$totalRows.="<td class='centered percentage'><span>".(100*$totalsArray[$i]/($totalsArray[$i-2]+$totalsArray[$i-1]+$totalsArray[$i]))."</span></td>";
						}
					}
				$totalRows.="</tr>";
				echo $totalRows.$operatorRows.$totalRows;
			echo "</tbody>";
		echo "</table>";
	}
?>


<script>
	function formatNumbers(){
		$("td.number").each(function(){
			$(this).number(true,0);
		});
	}
	
	function formatCurrencies(){
		$("td.currency span").each(function(){
			$(this).number(true,2);
			$(this).parent().prepend("C$ ");
		});
	}
	
	function formatPercentages(){
		$("td.percentage span").each(function(){
			$(this).number(true,2);
			$(this).parent().append(" %");
		});
	}
	
	$(document).ready(function(){
		formatNumbers();
		formatCurrencies();
		formatPercentages();
	});

</script>