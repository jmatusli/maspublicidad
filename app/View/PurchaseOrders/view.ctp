<div class="purchaseOrders view">
<?php 
	echo "<h2>".__('Purchase Order')." ".$purchaseOrder['PurchaseOrder']['purchase_order_code'].($purchaseOrder['PurchaseOrder']['bool_annulled']?" (Anulada)":"")."</h2>";
	echo "<div class='container-fluid'>";
		echo "<div class='rows'>";	
			echo "<div class='col-md-6'>";
				echo "<dl>";
					$purchaseOrderDateTime=new DateTime($purchaseOrder['PurchaseOrder']['purchase_order_date']);
								
					echo "<dt>".__('Purchase Order Date')."</dt>";
					echo "<dd>".$purchaseOrderDateTime->format('d-m-Y')."</dd>";
					echo "<dt>".__('Purchase Order Code')."</dt>";
					echo "<dd>".h($purchaseOrder['PurchaseOrder']['purchase_order_code'])."</dd>";
					echo "<dt>".__('Provider')."</dt>";
					echo "<dd>".$this->Html->link($purchaseOrder['Provider']['name'], array('controller' => 'providers', 'action' => 'view', $purchaseOrder['Provider']['id']))."</dd>";
					echo "<dt>".__('User')."</dt>";
					echo "<dd>".$this->Html->link($purchaseOrder['User']['username'], array('controller' => 'users', 'action' => 'view', $purchaseOrder['User']['id']))."</dd>";
					echo "<dt>".__('Bool Annulled')."</dt>";
					echo "<dd>".($purchaseOrder['PurchaseOrder']['bool_annulled']?__('Yes'):__('No'))."</dd>";
					echo "<dt>".__('Bool Iva')."</dt>";
					echo "<dd>".($purchaseOrder['PurchaseOrder']['bool_iva']?__('Yes'):__('No'))."</dd>";
					echo "<dt>".__('Cost Subtotal')."</dt>";
					echo "<dd>".$purchaseOrder['Currency']['abbreviation']." ".h($purchaseOrder['PurchaseOrder']['cost_subtotal'])."</dd>";
					echo "<dt>".__('Cost Iva')."</dt>";
					echo "<dd>".$purchaseOrder['Currency']['abbreviation']." ".h($purchaseOrder['PurchaseOrder']['cost_iva'])."</dd>";
					echo "<dt>".__('Cost Total')."</dt>";
					echo "<dd>".$purchaseOrder['Currency']['abbreviation']." ".h($purchaseOrder['PurchaseOrder']['cost_total'])."</dd>";
					echo "<dt>".__('Cost Other Total')."</dt>";
					echo "<dd>".$purchaseOrder['Currency']['abbreviation']." ".h($purchaseOrder['PurchaseOrder']['cost_other_total'])."</dd>";
					echo "<dt>".__('Payment Mode')."</dt>";
					echo "<dd>".$this->Html->link($purchaseOrder['PaymentMode']['name'], array('controller' => 'payment_modes', 'action' => 'view', $purchaseOrder['PaymentMode']['id']))."</dd>";
					echo "<dt>".__('Payment Document')."</dt>";
					if (!empty($purchaseOrder['PurchaseOrder']['payment_document'])){
						echo "<dd>".h($purchaseOrder['PurchaseOrder']['payment_document'])."</dd>";
					}
					else {
						echo "<dd>-</dd>";
					}
					echo "<dt>".__('Bool Received')."</dt>";
					echo "<dd>".($purchaseOrder['PurchaseOrder']['bool_received']?__('Yes'):__('No'))."</dd>";
				echo "</dl>";
			echo "</div>";
			echo "<div class='col-md-6'>";
				if (!empty($purchaseOrder['PurchaseOrderRemark'])){
					echo "<table>";
						echo "<thead>";
							echo "<tr>";
								echo "<th>Fecha</th>";
								echo "<th>Vendedor</th>";
								echo "<th>Remarca</th>";
							echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						foreach ($purchaseOrder['PurchaseOrderRemark'] as $purchaseOrderRemark){
							$remarkDateTime=new DateTime($purchaseOrderRemark['remark_datetime']);
							echo "<tr>";
								echo "<td>".$remarkDateTime->format('d-m-Y H:i')."</td>";
								echo "<td>".$purchaseOrderRemark['User']['username']."</td>";
								echo "<td>".$purchaseOrderRemark['remark_text']."</td>";
							echo "</tr>";
						}
						echo "</tbody>";
					echo "</table>";
				}
			echo "</div>";
		echo "</div>";
	echo "</div>";	
?> 
</div>
<div class='actions'>
<?php 	
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Guardar como pdf'), array('action' => 'viewPdf','ext'=>'pdf', $purchaseOrder['PurchaseOrder']['id'],$filename),array('target'=>'_blank'))."</li>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Purchase Order'), array('action' => 'edit', $purchaseOrder['PurchaseOrder']['id']))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('Recepción Orden de Compra'), array('action' => 'recibirOrdenDeCompra', $purchaseOrder['PurchaseOrder']['id']))."</li>";
		echo "<br/>";
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete'), array('action' => 'delete', $purchaseOrder['PurchaseOrder']['id']), array(), __('Está seguro que quiere eliminar orden de compra # %s?', $purchaseOrder['PurchaseOrder']['purchase_order_code']))."</li>";
		}
		if ($bool_annul_permission){
			echo "<li>".$this->Form->postLink(__('Anular'), array('action' => 'annul', $purchaseOrder['PurchaseOrder']['id']), array(), __('Está seguro que quiere anular orden de compra # %s?', $purchaseOrder['PurchaseOrder']['purchase_order_code']))."</li>";
		}
		if ($bool_delete_permission||$bool_annul_permission){
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Purchase Orders'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Purchase Order'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_provider_index_permission){
			echo "<li>".$this->Html->link(__('List Providers'), array('controller' => 'providers', 'action' => 'index'))." </li>";
		}
		if ($bool_provider_add_permission){
			echo "<li>".$this->Html->link(__('New Provider'), array('controller' => 'providers', 'action' => 'add'))." </li>";
		}
	echo "</ul>";
?>
</div>
<div class="related">
<?php 
	if (!empty($purchaseOrder['PurchaseOrderProduct'])){
		echo "<h3>".__('Productos en esta Orden de Compra')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Product Id')."</th>";
					echo "<th>".__('Product Description')."</th>";
					echo "<th class='centered'>".__('Product Quantity')."</th>";
					echo "<th>".__('Product Unit Cost')."</th>";
					echo "<th>".__('Product Total Cost')."</th>";
					echo "<th>".__('Recibido')."</th>";
					echo "<th>".__('Fecha de Recepción')."</th>";
					//echo "<th class='actions'>".__('Actions')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			$totalProductQuantity=0;	
			if ($purchaseOrder['PurchaseOrder']['currency_id']==CURRENCY_CS){
				$classCurrency=" class='CScurrency'";
			}
			elseif ($purchaseOrder['PurchaseOrder']['currency_id']==CURRENCY_USD){
				$classCurrency=" class='USDcurrency'";
			}
			foreach ($purchaseOrder['PurchaseOrderProduct'] as $purchaseOrderProduct){ 
				$totalProductQuantity+=$purchaseOrderProduct['product_quantity'];
				if ($purchaseOrderProduct['currency_id']==CURRENCY_CS){
					$classCurrency=" class='CScurrency'";
				}
				elseif ($purchaseOrderProduct['currency_id']==CURRENCY_USD){
					$classCurrency=" class='USDcurrency'";
				}
				echo "<tr>";
					//pr($purchaseOrderProduct);
					$receivedDateTime=new DateTime($purchaseOrderProduct['date_received']);
					echo "<td>".$purchaseOrderProduct['Product']['name'].(empty($purchaseOrderProduct['Product']['code'])?"":" (".$purchaseOrderProduct['Product']['code'].")")."</td>";
					echo "<td>".$purchaseOrderProduct['product_description']."</td>";
					echo "<td class='amount centered'>".$purchaseOrderProduct['product_quantity']."</td>";
					echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$purchaseOrderProduct['product_unit_cost']."</span></td>";
					echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$purchaseOrderProduct['product_total_cost']."</span></td>";
					echo "<td>".($purchaseOrderProduct['bool_received']?__('Yes'):__('No'))."</td>";
					echo "<td>".$receivedDateTime->format('d-m-Y')."</td>";
					//echo "<td class='actions'>";
						//echo $this->Html->link(__('View'), array('controller' => 'purchase_order_products', 'action' => 'view', $purchaseOrderProduct['id']));
						//echo $this->Html->link(__('Edit'), array('controller' => 'purchase_order_products', 'action' => 'edit', $purchaseOrderProduct['id']));
						//echo $this->Form->postLink(__('Delete'), array('controller' => 'purchase_order_products', 'action' => 'delete', $purchaseOrderProduct['id']), array(), __('Are you sure you want to delete # %s?', $purchaseOrderProduct['id']));
					//echo "</td>";
				echo "</tr>";
			}
				echo "<tr class='totalrow'>";
					echo "<td>Subtotal</td>";
					echo "<td></td>";
					echo "<td class='amount centered'>".$totalProductQuantity."</td>";
					echo "<td></td>";
					echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".number_format($purchaseOrder['PurchaseOrder']['cost_subtotal'],2,".",",")."</span></td>";
					echo "<td></td>";
					echo "<td></td>";
				echo "</tr>";
			echo "</tbody>";
		echo "</table>";
	}
?>
</div>
<div class="related">
<?php 
	if (!empty($purchaseOrder['PurchaseOrderOtherCost'])){
		echo "<h3>".__('Otros Costos en esta Orden de Compra')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Department')."</th>";
					echo "<th>".__('Task Description')."</th>";
					echo "<th class='centered'>".__('Task Quantity')."</th>";
					echo "<th>".__('Task Unit Cost')."</th>";
					echo "<th>".__('Task Total Cost')."</th>";
					//echo "<th class='actions'>".__('Actions')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			$totalOtherQuantity=0;	
			if ($purchaseOrder['PurchaseOrder']['currency_id']==CURRENCY_CS){
				$classCurrency=" class='CScurrency'";
			}
			elseif ($purchaseOrder['PurchaseOrder']['currency_id']==CURRENCY_USD){
				$classCurrency=" class='USDcurrency'";
			}
			foreach ($purchaseOrder['PurchaseOrderOtherCost'] as $purchaseOrderOtherCost){ 
				$totalOtherQuantity+=$purchaseOrderOtherCost['task_quantity'];
				if ($purchaseOrderOtherCost['currency_id']==CURRENCY_CS){
					$classCurrency=" class='CScurrency'";
				}
				elseif ($purchaseOrderOtherCost['currency_id']==CURRENCY_USD){
					$classCurrency=" class='USDcurrency'";
				}
				
				echo "<tr>";
					echo "<td>".$purchaseOrderOtherCost['Department']['name']."</td>";
					echo "<td>".$purchaseOrderOtherCost['task_description']."</td>";
					echo "<td class='amount centered'>".$purchaseOrderOtherCost['task_quantity']."</td>";
					echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$purchaseOrderOtherCost['task_unit_cost']."</span></td>";
					echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$purchaseOrderOtherCost['task_total_cost']."</span></td>";
					//echo "<td class='actions'>";
					//	echo $this->Html->link(__('View'), array('controller' => 'purchase_order_other_costs', 'action' => 'view', $purchaseOrderOtherCost['id']));
					//	echo $this->Html->link(__('Edit'), array('controller' => 'purchase_order_other_costs', 'action' => 'edit', $purchaseOrderOtherCost['id']));
					//	echo $this->Form->postLink(__('Delete'), array('controller' => 'purchase_order_other_costs', 'action' => 'delete', $purchaseOrderOtherCost['id']), array(), __('Are you sure you want to delete # %s?', $purchaseOrderOtherCost['id']));
					//echo "</td>";
				echo "</tr>";
			}
				echo "<tr class='totalrow'>";
					echo "<td>Subtotal</td>";
					echo "<td></td>";
					echo "<td class='amount centered'>".$totalOtherQuantity."</td>";
					echo "<td></td>";
					echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".number_format($purchaseOrder['PurchaseOrder']['cost_subtotal'],2,".",",")."</span></td>";
				echo "</tr>";
			echo "</tbody>";
		echo "</table>";
	}
?>
</div>

<div class="related">
<?php 
	if (!empty($purchaseOrder['PurchaseOrderRemark'])){
		echo "<h3>".__('Remarcas para Orden de Compra')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Remark Datetime')."</th>";
				echo "<th>".__('User Id')."</th>";
				echo "<th>".__('Remark Text')."</th>";
				echo "<th>".__('Action Type')."</th>";
				//echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($purchaseOrder['PurchaseOrderRemark'] as $purchaseOrderRemark){ 
			$remarkDateTime=new DateTime($purchaseOrderRemark['remark_datetime']);
			echo "<tr>";
				echo "<td>".$remarkDateTime->format('d-m-Y H:i:s')."</td>";
				echo "<td>".$purchaseOrderRemark['User']['first_name']." ".$purchaseOrderRemark['User']['last_name']."</td>";
				echo "<td>".$purchaseOrderRemark['remark_text']."</td>";
				echo "<td>".$purchaseOrderRemark['ActionType']['name']."</td>";
				//echo "<td class='actions'>";
				//	echo $this->Html->link(__('View'), array('controller' => 'purchase_order_remarks', 'action' => 'view', $purchaseOrderRemark['id']));
				//	echo $this->Html->link(__('Edit'), array('controller' => 'purchase_order_remarks', 'action' => 'edit', $purchaseOrderRemark['id']));
				//	echo $this->Form->postLink(__('Delete'), array('controller' => 'purchase_order_remarks', 'action' => 'delete', $purchaseOrderRemark['id']), array(), __('Are you sure you want to delete # %s?', $purchaseOrderRemark['id']));
				//echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
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
			$(this).number(true,0,'.',',');
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
		
		//var pdfUrl="<?php echo $this->Html->url(array('action'=>'viewPdf','ext'=>'pdf',$purchaseOrder['PurchaseOrder']['id'],$filename),true); ?>";
		//window.open(pdfUrl,'_blank');

	});
</script>