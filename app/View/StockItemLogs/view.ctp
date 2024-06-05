<div class="stockItemLogs view">
<?php 
	echo "<h2>".__('Stock Item Log')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Stock Item')."</dt>";
		echo "<dd>".$this->Html->link($stockItemLog['StockItem']['name'], array('controller' => 'stock_items', 'action' => 'view', $stockItemLog['StockItem']['id']))."</dd>";
		echo "<dt>".__('Stock Item Date')."</dt>";
		echo "<dd>".h($stockItemLog['StockItemLog']['stock_item_date'])."</dd>";
		echo "<dt>".__('Product')."</dt>";
		echo "<dd>".$this->Html->link($stockItemLog['Product']['name'], array('controller' => 'products', 'action' => 'view', $stockItemLog['Product']['id']))."</dd>";
		echo "<dt>".__('Product Quantity')."</dt>";
		echo "<dd>".h($stockItemLog['StockItemLog']['product_quantity'])."</dd>";
		echo "<dt>".__('Measuring Unit')."</dt>";
		echo "<dd>".$this->Html->link($stockItemLog['MeasuringUnit']['name'], array('controller' => 'measuring_units', 'action' => 'view', $stockItemLog['MeasuringUnit']['id']))."</dd>";
		echo "<dt>".__('Product Unit Cost')."</dt>";
		echo "<dd>".h($stockItemLog['StockItemLog']['product_unit_cost'])."</dd>";
		echo "<dt>".__('Currency')."</dt>";
		echo "<dd>".$this->Html->link($stockItemLog['Currency']['abbreviation'], array('controller' => 'currencies', 'action' => 'view', $stockItemLog['Currency']['id']))."</dd>";
		echo "<dt>".__('Stock Movement')."</dt>";
		echo "<dd>".$this->Html->link($stockItemLog['StockMovement']['id'], array('controller' => 'stock_movements', 'action' => 'view', $stockItemLog['StockMovement']['id']))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Stock Item Log'), array('action' => 'edit', $stockItemLog['StockItemLog']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Stock Item Log'), array('action' => 'delete', $stockItemLog['StockItemLog']['id']), array(), __('Are you sure you want to delete # %s?', $stockItemLog['StockItemLog']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Stock Item Logs'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Stock Item Log'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Stock Items'), array('controller' => 'stock_items', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Stock Item'), array('controller' => 'stock_items', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Measuring Units'), array('controller' => 'measuring_units', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Measuring Unit'), array('controller' => 'measuring_units', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Currencies'), array('controller' => 'currencies', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Currency'), array('controller' => 'currencies', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Stock Movements'), array('controller' => 'stock_movements', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Stock Movement'), array('controller' => 'stock_movements', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
