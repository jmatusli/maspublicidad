<?php
	if (!empty($productsForSalesOrder)){
		echo "<table id='productos'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Product')."</th>";
					echo "<th>".__('Description')."</th>";
					echo "<th>".__('Instruction')."</th>";
					echo "<th>".__('Product Quantity')."</th>";
					echo "<th>".__('Operation Location')."</th>";
					echo "<th style='width:20%;'>".__('Department')."</th>";
					//echo "<th>".__('Actions')."</th>";
					
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
				$i=0;
				$amountProducts=0;			
				foreach ($productsForSalesOrder as $product){
					//pr($product);
					echo "<tr row='".$i."'>";
						echo "<td class='productid'>";
							echo $this->Form->input('ProductionOrderProduct.'.$i.'.product_id',array('label'=>false,'value'=>$product['SalesOrderProduct']['product_id'],'class'=>'fixed'));
							echo $this->Form->input('ProductionOrderProduct.'.$i.'.sales_order_product_id',array('label'=>false,'type'=>'hidden','value'=>$product['SalesOrderProduct']['id']));
						echo "</td>";
						echo "<td class='productdescription'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_description',array('label'=>false,'value'=>$product['SalesOrderProduct']['product_description']))."</td>";
						echo "<td class='productinstruction'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_instruction',array('label'=>false,'default'=>''))."</td>";
						echo "<td class='productquantity amount'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_quantity',array('label'=>false,'value'=>$product['SalesOrderProduct']['product_quantity'],'type'=>'numeric','readonly'=>'readonly'))."</td>";
						echo "<td class='operationlocationid'>".$this->Form->input('ProductionOrderProduct.'.$i.'.operation_location_id',array('multiple'=>true,'label'=>false,'default'=>'0','empty'=>array('0'=>'Seleccione Lugar')))."</td>";
						echo "<td class='departmentid'>";
								for ($j=0;$j<count($departments);$j++){
									if ($j==0){
										echo "<span>".($j+1)."</span>".$this->Form->input('ProductionOrderProduct.'.$i.'.departments.'.$j.'.department_id',array('label'=>false,'default'=>'0','empty'=>array('0'=>'Seleccione Departamento'),'div'=>array('style'=>'display:inline-block;')))."<span class='plusbutton'>+</span>"."<br/>";
									}
									else {
										echo "<span class='hidden'>".($j+1)."</span>".$this->Form->input('ProductionOrderProduct.'.$i.'.departments.'.$j.'.department_id',array('label'=>false,'default'=>'0','empty'=>array('0'=>'Seleccione Departamento'),'class'=>'hidden','div'=>array('style'=>'display:inline-block;')))."<span class='plusbutton hidden'>+</span>"."<br/>";
									}
								}
								echo "</td>";
					echo "</tr>";
					$i++;
				}
				echo "<tr class='totalrow'>";
					echo "<td>Total</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td class='productquantity amount centered'><span></span></td>";
					echo "<td></td>";
					echo "<td></td>";
				echo "</tr>";	
			echo "</tbody>";
		echo "</table>";
	}
	else {
		echo "<h1>No hay productos para esta orden de venta</h1>";
	}
?>
<script>
	$(document).ajaxComplete(function() {	
		//$('td.productid option:not(:selected)').attr('disabled', true);
	});
</script>