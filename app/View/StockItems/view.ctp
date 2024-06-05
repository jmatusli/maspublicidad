<div class="stockItems view">
<?php 
	echo "<h2>".__('Stock Item')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($stockItem['StockItem']['name'])."</dd>";
		echo "<dt>".__('Description')."</dt>";
		echo "<dd>".h($stockItem['StockItem']['description'])."</dd>";
		echo "<dt>".__('Product')."</dt>";
		echo "<dd>".$this->Html->link($stockItem['Product']['name'], array('controller' => 'products', 'action' => 'view', $stockItem['Product']['id']))."</dd>";
		echo "<dt>".__('Product Unit Cost')."</dt>";
		echo "<dd>".h($stockItem['StockItem']['product_unit_cost'])."</dd>";
		echo "<dt>".__('Currency')."</dt>";
		echo "<dd>".$this->Html->link($stockItem['Currency']['abbreviation'], array('controller' => 'currencies', 'action' => 'view', $stockItem['Currency']['id']))."</dd>";
		echo "<dt>".__('Product Original Quantity')."</dt>";
		echo "<dd>".h($stockItem['StockItem']['product_original_quantity'])."</dd>";
		echo "<dt>".__('Product Remaining Quantity')."</dt>";
		echo "<dd>".h($stockItem['StockItem']['product_remaining_quantity'])."</dd>";
		echo "<dt>".__('Measuring Unit')."</dt>";
		echo "<dd>".$this->Html->link($stockItem['MeasuringUnit']['name'], array('controller' => 'measuring_units', 'action' => 'view', $stockItem['MeasuringUnit']['id']))."</dd>";
		echo "<dt>".__('Stock Item Creation Date')."</dt>";
		echo "<dd>".h($stockItem['StockItem']['stock_item_creation_date'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Stock Item'), array('action' => 'edit', $stockItem['StockItem']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Stock Item'), array('action' => 'delete', $stockItem['StockItem']['id']), array(), __('Are you sure you want to delete # %s?', $stockItem['StockItem']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Stock Items'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Stock Item'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Currencies'), array('controller' => 'currencies', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Currency'), array('controller' => 'currencies', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Measuring Units'), array('controller' => 'measuring_units', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Measuring Unit'), array('controller' => 'measuring_units', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Stock Item Logs'), array('controller' => 'stock_item_logs', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Stock Item Log'), array('controller' => 'stock_item_logs', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Stock Movements'), array('controller' => 'stock_movements', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Stock Movement'), array('controller' => 'stock_movements', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
<div class="related">
<?php 
	if (!empty($stockItem['StockItemLog'])){
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
		foreach ($stockItem['StockItemLog'] as $stockItemLog){ 
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
<div class="related">
<?php 
	if (!empty($stockItem['StockMovement'])){
		echo "<h3>".__('Related Stock Movements')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Movement Date')."</th>";
				echo "<th>".__('Bool Input')."</th>";
				echo "<th>".__('Entry Id')."</th>";
				echo "<th>".__('Remission Id')."</th>";
				echo "<th>".__('Stock Item Id')."</th>";
				echo "<th>".__('Product Id')."</th>";
				echo "<th>".__('Product Quantity')."</th>";
				echo "<th>".__('Measuring Unit Id')."</th>";
				echo "<th>".__('Product Unit Cost')."</th>";
				echo "<th>".__('Currency Id')."</th>";
				echo "<th>".__('Description')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($stockItem['StockMovement'] as $stockMovement){ 
			echo "<tr>";
				echo "<td>".$stockMovement['movement_date']."</td>";
				echo "<td>".$stockMovement['bool_input']."</td>";
				echo "<td>".$stockMovement['entry_id']."</td>";
				echo "<td>".$stockMovement['remission_id']."</td>";
				echo "<td>".$stockMovement['stock_item_id']."</td>";
				echo "<td>".$stockMovement['product_id']."</td>";
				echo "<td>".$stockMovement['product_quantity']."</td>";
				echo "<td>".$stockMovement['measuring_unit_id']."</td>";
				echo "<td>".$stockMovement['product_unit_cost']."</td>";
				echo "<td>".$stockMovement['currency_id']."</td>";
				echo "<td>".$stockMovement['description']."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'stock_movements', 'action' => 'view', $stockMovement['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'stock_movements', 'action' => 'edit', $stockMovement['id']));
					echo $this->Form->postLink(__('Delete'), array('controller' => 'stock_movements', 'action' => 'delete', $stockMovement['id']), array(), __('Are you sure you want to delete # %s?', $stockMovement['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
