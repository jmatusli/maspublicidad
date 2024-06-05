<div class="remissions view">
<?php 
	echo "<h2>".__('Remission')." ".$remission['Remission']['remission_code'].($remission['Remission']['bool_annulled']?" (Anulada)":"")."</h2>";
	$remissionDateTime= new DateTime($remission['Remission']['remission_date']);
	$dueDateTime= new DateTime($remission['Remission']['due_date']);
	echo "<dl>";
		echo "<dt>".__('Remission Date')."</dt>";
		echo "<dd>".$remissionDateTime->format('d-m-Y')."</dd>";
		echo "<dt>".__('Remission Code')."</dt>";
		echo "<dd>".h($remission['Remission']['remission_code']).($remission['Remission']['bool_annulled']?" (Anulada)":"")."</dd>";
		echo "<dt>".__('Inventory Client')."</dt>";
		echo "<dd>".$this->Html->link($remission['InventoryClient']['name'], array('controller' => 'inventory_clients', 'action' => 'view', $remission['InventoryClient']['id']))."</dd>";
		//echo "<dt>".__('Bool Credit')."</dt>";
		//echo "<dd>".h($remission['Remission']['bool_credit'])."</dd>";
		echo "<dt>".__('Due Date')."</dt>";
		echo "<dd>".$dueDateTime->format('d-m-Y')."</dd>";
		echo "<dt>".__('Bool Iva')."</dt>";
		echo "<dd>".($remission['Remission']['bool_iva']?__('Yes'):__('No'))."</dd>";
		echo "<dt>".__('Price Subtotal')."</dt>";
		echo "<dd>".$remission['Currency']['abbreviation']." ".h($remission['Remission']['price_subtotal'])."</dd>";
		echo "<dt>".__('Price Iva')."</dt>";
		echo "<dd>".$remission['Currency']['abbreviation']." ".h($remission['Remission']['price_iva'])."</dd>";
		echo "<dt>".__('Price Total')."</dt>";
		echo "<dd>".$remission['Currency']['abbreviation']." ".h($remission['Remission']['price_total'])."</dd>";
		echo "<dt>".__('Bool Paid')."</dt>";
		echo "<dd>".h($remission['Remission']['bool_paid']?__('Yes'):__('No'))."</dd>";
		echo "<dt>".__('Observation')."</dt>";
		if (!empty($remission['Remission']['observation'])){
		echo "<dd>".h($remission['Remission']['observation'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
	echo "</dl>";
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Guardar como pdf'), array('action' => 'viewPdf','ext'=>'pdf',$remission['Remission']['id'],$filename),array("target"=>'_blank'))."</li>";
		
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Remission'), array('action' => 'edit', $remission['Remission']['id']))."</li>";
			echo "<br/>";
		}
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Eliminar Salida'), array('action' => 'delete', $remission['Remission']['id']), array(), __('Está seguro que quiere eliminar salida #%s?', $remission['Remission']['remission_code']))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Remissions'), array('action' => 'index'))."</li>";
		if ($bool_add_permission){
			echo "<li>".$this->Html->link(__('New Remission'), array('action' => 'add'))."</li>";
		}
		if ($bool_inventoryclient_index_permission){
			echo "<br/>";
			echo "<li>".$this->Html->link(__('List Inventory Clients'), array('controller' => 'inventory_clients', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryclient_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Client'), array('controller' => 'inventory_clients', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?> 
</div>
<div class="related">
<?php 
	if (!empty($remission['StockMovement'])){
		if ($remission['Currency']['id']==CURRENCY_USD){
			$currencyClass="USDcurrency";
		}
		else {
			$currencyClass="CScurrency";
		}
		echo "<h3>".__('Related Stock Movements')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Movement Date')."</th>";
					echo "<th>".__('Inventory Product')."</th>";
					echo "<th class='centered'>".__('Product Quantity')."</th>";
					echo "<th class='centered'>".__('Product Unit Price')."</th>";
					echo "<th class='centered'>".__('Subtotal')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			foreach ($remission['StockMovement'] as $stockMovement){ 
				$movementDateTime=new DateTime($stockMovement['movement_date']);
				echo "<tr>";
					echo "<td>".$movementDateTime->format('d-m-Y')."</td>";
					echo "<td>".$stockMovement['InventoryProduct']['name']."</td>";
					echo "<td class='centered'>".$stockMovement['product_quantity']." ".$stockMovement['MeasuringUnit']['abbreviation']."</td>";
					echo "<td class='".$currencyClass."'><span class='currency'>".$remission['Currency']['abbreviation']."</span><span class='amountright'>".$stockMovement['product_unit_price']."</span></td>";
					echo "<td class='".$currencyClass."'><span class='currency'>".$remission['Currency']['abbreviation']."</span><span class='amountright'>".$stockMovement['product_unit_price']*$stockMovement['product_quantity']."</span></td>";
				echo "</tr>";
			}
				echo "<tr class='totalrow'>";
					echo "<td>SubTotal</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td class='".$currencyClass."'><span class='currency'>".$remission['Currency']['abbreviation']."</span><span class='amountright'>".$remission['Remission']['price_subtotal']."</span></td>";
				echo "</tr>";	
			echo "</tbody>";
		echo "</table>";
	}
?>
</div>
