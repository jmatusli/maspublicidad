<?php
	if (!empty($productsForSalesOrder)){
		echo "<table id='productos'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Product')."</th>";
					echo "<th>".__('Description')."</th>";
					echo "<th>".__('Pending Quantity')."</th>";
					echo "<th>".__('Product Quantity')."</th>";
					echo "<th>".__('Product Unit Price')."</th>";
					echo "<th>".__('Product Total Price')."</th>";
					echo "<th class='hidden'>".__('Without IVA')."</th>";
					echo "<th>".__('IVA?')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			$i=0;
			$subtotal=0;
			$ivatotal=0;
			foreach ($productsForSalesOrder as $product){
				//pr($product);
				$subtotal+=round($product['SalesOrderProduct']['product_quantity']*$product['SalesOrderProduct']['product_unit_price'],2);
				if($product['SalesOrderProduct']['bool_iva']){
					$ivatotal+=round(0.15*$product['SalesOrderProduct']['product_quantity']*$product['SalesOrderProduct']['product_unit_price'],2);	
				}
				echo "<tr row='".$i."'>";
					echo "<td class='productid'>";
						echo $this->Form->input('InvoiceProduct.'.$i.'.product_id',array('label'=>false,'class'=>'fixed','default'=>$product['SalesOrderProduct']['product_id']));
						echo $this->Form->input('InvoiceProduct.'.$i.'.sales_order_product_id',array('label'=>false,'type'=>'hidden','value'=>$product['SalesOrderProduct']['id']));
					echo "</td>";
					echo "<td class='productdescription'>".$this->Form->textarea('InvoiceProduct.'.$i.'.product_description',array('label'=>false,'rows'=>10,'default'=>$product['SalesOrderProduct']['product_description'],'readonly'=>'readonly'))."</td>";
					echo "<td class='pendingquantity amount'>".$this->Form->input('InvoiceProduct.'.$i.'.pending_quantity',array('label'=>false,'value'=>$product['SalesOrderProduct']['product_quantity'],'type'=>'numeric','readonly'=>'readonly'))."</td>";
					echo "<td class='productquantity amount'>".$this->Form->input('InvoiceProduct.'.$i.'.product_quantity',array('label'=>false,'default'=>$product['SalesOrderProduct']['product_quantity'],'type'=>'numeric'))."</td>";
					if ($product['SalesOrderProduct']['currency_id']==$currencyId){
						$unitPrice=$product['SalesOrderProduct']['product_unit_price'];
					}
					else {
						if ($currencyId==CURRENCY_USD){
							$unitPrice=round($product['SalesOrderProduct']['product_unit_price']/$exchangeRate,2);
						}
						else {
							$unitPrice=round($product['SalesOrderProduct']['product_unit_price']*$exchangeRate,2);
						}
					}
					$totalPrice=round($product['SalesOrderProduct']['product_quantity']*$unitPrice,2);
					echo "<td class='productunitprice amount'><span class='currency'>".($currencyId==CURRENCY_USD?"US$":"C$")."</span><span class='amount right'>".$this->Form->input('InvoiceProduct.'.$i.'.product_unit_price',array('label'=>false,'default'=>$unitPrice,'type'=>'decimal','readonly'=>'readonly'))."</span></td>";
					echo "<td class='producttotalprice amount'><span class='currency'>".($currencyId==CURRENCY_USD?"US$":"C$")."</span><span class='amount right'>".$this->Form->input('InvoiceProduct.'.$i.'.product_total_price',array('label'=>false,'default'=>$totalPrice,'type'=>'decimal','readonly'=>'readonly'))."</span></td>";
					echo "<td class='boolnoiva hidden'>".$this->Form->input('InvoiceProduct.'.$i.'.bool_no_iva',array('label'=>false,'type'=>'checkbox','default'=>$product['Product']['bool_no_iva'],'onclick'=>'return false'))."</td>";
					echo "<td class='booliva'>".$this->Form->input('InvoiceProduct.'.$i.'.bool_iva',array('label'=>false,'default'=>$product['SalesOrderProduct']['bool_iva'],'onclick'=>'return false'))."</td>";
				echo "</tr>";
				$i++;
			}
			$total=$subtotal+$ivatotal;
				echo "<tr class='totalrow subtotal'>";
					echo "<td>Subtotal</td>";
					echo "<td></td>";
					echo "<td class='productquantity amount centered'><span></span></td>";
					echo "<td></td>";
					echo "<td class='totalprice amount right'><span class='currency'>".($currencyId==CURRENCY_USD?"US$":"C$")."</span>".$this->Form->input('Invoice.price_subtotal',array('label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>$subtotal))."</td>";
					echo "<td></td>";
				echo "</tr>";		
				echo "<tr class='totalrow iva'>";
					echo "<td>IVA</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td class='totalprice amount right'><span class='currency'>".($currencyId==CURRENCY_USD?"US$":"C$")."</span>".$this->Form->input('Invoice.price_iva',array('label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>$ivatotal))."</td>";
					echo "<td></td>";
				echo "</tr>";		
				echo "<tr class='totalrow total'>";
					echo "<td>Total</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td class='totalprice amount right'><span class='currency'>".($currencyId==CURRENCY_USD?"US$":"C$")."</span>".$this->Form->input('Invoice.price_total',array('label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>$total))."</td>";
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
		$('td.productid option:not(:selected)').attr('disabled', true);
	});
</script>