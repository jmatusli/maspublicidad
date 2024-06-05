<?php
	if (!empty($productsForQuotation)){
		echo "<table id='productos'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Product')."</th>";
					echo "<th>".__('Description')."</th>";
					echo "<th class='centered'>".__('Product Quantity')."</th>";
					echo "<th class='centered'>".__('Unit Price')."</th>";
					echo "<th class='centered'>".__('Total Price')."</th>";
					echo "<th class='hidden'>".__('WITHOUTIVA')."</th>";
					echo "<th>".__('IVA?')."</th>";
					echo "<th>".__('No producción')."</th>";
					echo "<th>".__('Status')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			$i=0;
			foreach ($productsForQuotation as $product){
				//pr($product);
				echo "<tr row='".$i."'>";
					echo "<td class='productid'>".$this->Form->input('SalesOrderProduct.'.$i.'.product_id',array('label'=>false,'default'=>$product['QuotationProduct']['product_id']))."</td>";
					echo "<td class='productdescription'>".$this->Form->input('SalesOrderProduct.'.$i.'.product_description',['label'=>false,'readonly'=>true,'default'=>$product['QuotationProduct']['product_description']])."</td>";
					echo "<td class='productquantity amount'>".$this->Form->input('SalesOrderProduct.'.$i.'.product_quantity',array('label'=>false,'default'=>$product['QuotationProduct']['product_quantity'],'type'=>'numeric','readonly'=>'readonly'))."</td>";
					echo "<td class='productunitprice amount'><span class='currency'></span>".$this->Form->input('SalesOrderProduct.'.$i.'.product_unit_price',array('type'=>'decimal','label'=>false,'default'=>$product['QuotationProduct']['product_unit_price'],'readonly'=>'readonly'))."</td>";
					echo "<td class='producttotalprice amount'><span class='currency'></span>".$this->Form->input('SalesOrderProduct.'.$i.'.product_total_price',array('type'=>'decimal','label'=>false,'default'=>$product['QuotationProduct']['product_total_price'],'readonly'=>'readonly'))."</td>";
					echo "<td class='hidden boolnoiva'>".$this->Form->input('SalesOrderProduct.'.$i.'.bool_no_iva',array('label'=>false,'default'=>$product['Product']['bool_no_iva'],'onclick'=>'return false'))."</td>";
					echo "<td class='booliva'>".$this->Form->input('SalesOrderProduct.'.$i.'.bool_iva',array('label'=>false,'default'=>$product['QuotationProduct']['bool_iva'],'onclick'=>'return false'))."</td>";
					echo "<td class='boolnoproduction'>".$this->Form->input('SalesOrderProduct.'.$i.'.bool_no_production',array('label'=>false,'default'=>0))."</td>";
					echo "<td class='salesorderproductstatusid'>".$this->Form->input('SalesOrderProduct.'.$i.'.sales_order_product_status_id',array('label'=>false,'default'=>1))."</td>";
				echo "</tr>";
				$i++;
			}
				echo "<tr class='totalrow subtotal'>";
					echo "<td>Subtotal</td>";
					echo "<td></td>";
					echo "<td class='productquantity amount centered'><span></span></td>";
					echo "<td></td>";
					echo "<td class='totalprice amount right'><span class='currency'></span>".$this->Form->input('SalesOrder.price_subtotal',array('label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>'0'))."</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
				echo "</tr>";	
				echo "<tr class='totalrow iva'>";
					echo "<td>IVA</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td class='totalprice amount right'><span class='currency'></span>".$this->Form->input('SalesOrder.price_iva',array('label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>'0'))."</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
				echo "</tr>";	
				echo "<tr class='totalrow total'>";
					echo "<td>Total</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td class='totalprice amount right'><span class='currency'></span>".$this->Form->input('SalesOrder.price_total',array('label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>'0'))."</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
				echo "</tr>";	
			echo "</tbody>";
		echo "</table>";
	}
?>
<script>
	$(document).ajaxComplete(function() {	
		$('td.productid option:not(:selected)').attr('disabled', true);
		$('td.salesorderproductstatusid option:not(:selected)').attr('disabled', true);
		formatCurrencies();
		calculateTotal();
	});
</script>