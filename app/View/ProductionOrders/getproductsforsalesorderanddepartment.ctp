<?php
	if (!empty($productsForSalesOrder)){
		echo "<table id='productos'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Product')."</th>";
					//echo "<th>".__('Description')."</th>";
					echo "<th>".__('Product Quantity')."</th>";
					echo "<th>".__('Operation Location')."</th>";
					if (!$bool_first_production_order){
						echo "<th>".__('Actions')."</th>";
					}
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			$i=0;
			$amountProducts=0;
			if ($bool_first_production_order){
				foreach ($productsForSalesOrderAndDepartment as $product){
					//pr($product);
					echo "<tr row='".$i."'>";
						echo "<td class='productid'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_id',array('label'=>false,'value'=>$product['SalesOrderProduct']['product_id'],'class'=>'fixed'))."</td>";
						echo "<td class='productdescription'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_description',array('label'=>false,'value'=>$product['SalesOrderProduct']['product_description'],'class'=>'fixed'))."</td>";
						echo "<td class='productquantity amount'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_quantity',array('label'=>false,'value'=>$product['SalesOrderProduct']['product_quantity'],'type'=>'numeric','readonly'=>'readonly'))."</td>";
						echo "<td class='productsalesorderproductid'>".$this->Form->input('ProductionOrderProduct.'.$i.'.sales_order_product_id',array('label'=>false,'value'=>$product['SalesOrderProduct']['id'],'type'=>'hidden','readonly'=>'readonly'))."</td>";
						echo "<td class='operationlocationid'>".$this->Form->input('ProductionOrderProduct.'.$i.'.operation_location_id',array('multiple'=>true,'label'=>false,'default'=>'0','empty'=>array('0'=>'Seleccione Lugar')))."</td>";
					echo "</tr>";
					$i++;
				}
			}
			else {
				// MANUALLY ADDED PRODUCTION ORDER
				for ($i=0;$i<30;$i++){
					//pr($product);
					echo "<tr row='".$i."'>";
						echo "<td class='productid'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_id',array('label'=>false,'default'=>0,'empty'=>array('0'=>'Seleccione Producto')))."</td>";
						echo "<td class='productdescription'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_description',array('label'=>false,'default'=>0,'empty'=>array('0'=>'Seleccione Producto')))."</td>";
						echo "<td class='productquantity amount'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_quantity',array('label'=>false,'default'=>'0','type'=>'numeric'))."</td>";
						echo "<td class='productsalesorderproductid'>".$this->Form->input('ProductionOrderProduct.'.$i.'.sales_order_product_id',array('label'=>false,'value'=>'0','type'=>'hidden','readonly'=>'readonly'))."</td>";
						echo "<td class='operationlocationid'>".$this->Form->input('ProductionOrderProduct.'.$i.'.operation_location_id',array('multiple'=>true,'label'=>false,'default'=>'0','empty'=>array('0'=>'Seleccione Lugar')))."</td>";
						echo "<td>";
								echo "<button class='removeItem' type='button'>".__('Remove Item')."</button>";
								echo "<button class='addItem' type='button'>".__('Add Item')."</button>";
						echo "</td>";
					echo "</tr>";
				}
			}
				echo "<tr class='totalrow subtotal'>";
					echo "<td>Subtotal</td>";
					//echo "<td></td>";
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