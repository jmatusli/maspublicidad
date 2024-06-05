<div class="stockMovements view">
<?php 
	echo "<h2>".__('Stock Movement')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Movement Date')."</dt>";
		echo "<dd>".h($stockMovement['StockMovement']['movement_date'])."</dd>";
		echo "<dt>".__('Bool Input')."</dt>";
		echo "<dd>".h($stockMovement['StockMovement']['bool_input'])."</dd>";
		echo "<dt>".__('Entry')."</dt>";
		echo "<dd>".$this->Html->link($stockMovement['Entry']['id'], array('controller' => 'entries', 'action' => 'view', $stockMovement['Entry']['id']))."</dd>";
		echo "<dt>".__('Remission')."</dt>";
		echo "<dd>".$this->Html->link($stockMovement['Remission']['id'], array('controller' => 'remissions', 'action' => 'view', $stockMovement['Remission']['id']))."</dd>";
		echo "<dt>".__('Stock Item')."</dt>";
		echo "<dd>".$this->Html->link($stockMovement['StockItem']['name'], array('controller' => 'stock_items', 'action' => 'view', $stockMovement['StockItem']['id']))."</dd>";
		echo "<dt>".__('Product')."</dt>";
		echo "<dd>".$this->Html->link($stockMovement['Product']['name'], array('controller' => 'products', 'action' => 'view', $stockMovement['Product']['id']))."</dd>";
		echo "<dt>".__('Product Quantity')."</dt>";
		echo "<dd>".h($stockMovement['StockMovement']['product_quantity'])."</dd>";
		echo "<dt>".__('Measuring Unit')."</dt>";
		echo "<dd>".$this->Html->link($stockMovement['MeasuringUnit']['name'], array('controller' => 'measuring_units', 'action' => 'view', $stockMovement['MeasuringUnit']['id']))."</dd>";
		echo "<dt>".__('Product Unit Cost')."</dt>";
		echo "<dd>".h($stockMovement['StockMovement']['product_unit_cost'])."</dd>";
		echo "<dt>".__('Currency')."</dt>";
		echo "<dd>".$this->Html->link($stockMovement['Currency']['abbreviation'], array('controller' => 'currencies', 'action' => 'view', $stockMovement['Currency']['id']))."</dd>";
		echo "<dt>".__('Description')."</dt>";
		echo "<dd>".h($stockMovement['StockMovement']['description'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Stock Movement'), array('action' => 'edit', $stockMovement['StockMovement']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Stock Movement'), array('action' => 'delete', $stockMovement['StockMovement']['id']), array(), __('Are you sure you want to delete # %s?', $stockMovement['StockMovement']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Stock Movements'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Stock Movement'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Entries'), array('controller' => 'entries', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Entry'), array('controller' => 'entries', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Remissions'), array('controller' => 'remissions', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Remission'), array('controller' => 'remissions', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Stock Items'), array('controller' => 'stock_items', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Stock Item'), array('controller' => 'stock_items', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Measuring Units'), array('controller' => 'measuring_units', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Measuring Unit'), array('controller' => 'measuring_units', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Currencies'), array('controller' => 'currencies', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Currency'), array('controller' => 'currencies', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Stock Item Logs'), array('controller' => 'stock_item_logs', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Stock Item Log'), array('controller' => 'stock_item_logs', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
<div class="related">
<?php 
	if (!empty($stockMovement['StockItemLog'])){
		echo "<h3>".__('Related Stock Item Logs')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Stock Item Id')."</th>";
				echo "<th>".__('Stock Item Date')."</th>";
				echo "<th>".__('Product Id')."</th>";
				echo "<th>".__('Product Quantity')."</th>";
				echo "<th>".__('Measuring Unit Id')."</th>";
				echo "<th>".__('Product Unit Cost')."</th>";
				echo "<th>".__('Currency Id')."</th>";
				echo "<th>".__('Stock Movement Id')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($stockMovement['StockItemLog'] as $stockItemLog){ 
			echo "<tr>";
				echo "<td>".$stockItemLog['stock_item_id']."</td>";
				echo "<td>".$stockItemLog['stock_item_date']."</td>";
				echo "<td>".$stockItemLog['product_id']."</td>";
				echo "<td>".$stockItemLog['product_quantity']."</td>";
				echo "<td>".$stockItemLog['measuring_unit_id']."</td>";
				echo "<td>".$stockItemLog['product_unit_cost']."</td>";
				echo "<td>".$stockItemLog['currency_id']."</td>";
				echo "<td>".$stockItemLog['stock_movement_id']."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'stock_item_logs', 'action' => 'view', $stockItemLog['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'stock_item_logs', 'action' => 'edit', $stockItemLog['id']));
					echo $this->Form->postLink(__('Delete'), array('controller' => 'stock_item_logs', 'action' => 'delete', $stockItemLog['id']), array(), __('Are you sure you want to delete # %s?', $stockItemLog['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
