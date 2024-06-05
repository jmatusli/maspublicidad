<div class="measuringUnits view">
<?php 
	echo "<h2>".__('Measuring Unit')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Abbreviation')."</dt>";
		echo "<dd>".h($measuringUnit['MeasuringUnit']['abbreviation'])."</dd>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($measuringUnit['MeasuringUnit']['name'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Measuring Unit'), array('action' => 'edit', $measuringUnit['MeasuringUnit']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Measuring Unit'), array('action' => 'delete', $measuringUnit['MeasuringUnit']['id']), array(), __('Are you sure you want to delete # %s?', $measuringUnit['MeasuringUnit']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Measuring Units'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Measuring Unit'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Stock Item Logs'), array('controller' => 'stock_item_logs', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Stock Item Log'), array('controller' => 'stock_item_logs', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Stock Items'), array('controller' => 'stock_items', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Stock Item'), array('controller' => 'stock_items', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Stock Movements'), array('controller' => 'stock_movements', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Stock Movement'), array('controller' => 'stock_movements', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
<div class="related">
<?php 
	if (!empty($measuringUnit['StockItemLog'])){
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
		foreach ($measuringUnit['StockItemLog'] as $stockItemLog){ 
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
	if (!empty($measuringUnit['StockItem'])){
		echo "<h3>".__('Related Stock Items')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Name')."</th>";
				echo "<th>".__('Description')."</th>";
				echo "<th>".__('Product Id')."</th>";
				echo "<th>".__('Product Unit Cost')."</th>";
				echo "<th>".__('Currency Id')."</th>";
				echo "<th>".__('Product Original Quantity')."</th>";
				echo "<th>".__('Product Remaining Quantity')."</th>";
				echo "<th>".__('Measuring Unit Id')."</th>";
				echo "<th>".__('Stock Item Creation Date')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($measuringUnit['StockItem'] as $stockItem){ 
			echo "<tr>";
				echo "<td>".$stockItem['name']."</td>";
				echo "<td>".$stockItem['description']."</td>";
				echo "<td>".$stockItem['product_id']."</td>";
				echo "<td>".$stockItem['product_unit_cost']."</td>";
				echo "<td>".$stockItem['currency_id']."</td>";
				echo "<td>".$stockItem['product_original_quantity']."</td>";
				echo "<td>".$stockItem['product_remaining_quantity']."</td>";
				echo "<td>".$stockItem['measuring_unit_id']."</td>";
				echo "<td>".$stockItem['stock_item_creation_date']."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'stock_items', 'action' => 'view', $stockItem['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'stock_items', 'action' => 'edit', $stockItem['id']));
					echo $this->Form->postLink(__('Delete'), array('controller' => 'stock_items', 'action' => 'delete', $stockItem['id']), array(), __('Are you sure you want to delete # %s?', $stockItem['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
<div class="related">
<?php 
	if (!empty($measuringUnit['StockMovement'])){
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
		foreach ($measuringUnit['StockMovement'] as $stockMovement){ 
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
