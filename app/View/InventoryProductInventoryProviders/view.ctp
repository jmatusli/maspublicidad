<div class="inventoryProductInventoryProviders view">
<?php 
	echo "<h2>".__('Inventory Product Inventory Provider')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Inventory Product')."</dt>";
		echo "<dd>".$this->Html->link($inventoryProductInventoryProvider['InventoryProduct']['name'], array('controller' => 'inventory_products', 'action' => 'view', $inventoryProductInventoryProvider['InventoryProduct']['id']))."</dd>";
		echo "<dt>".__('Inventory Provider')."</dt>";
		echo "<dd>".$this->Html->link($inventoryProductInventoryProvider['InventoryProvider']['name'], array('controller' => 'inventory_providers', 'action' => 'view', $inventoryProductInventoryProvider['InventoryProvider']['id']))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Inventory Product Inventory Provider'), array('action' => 'edit', $inventoryProductInventoryProvider['InventoryProductInventoryProvider']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Inventory Product Inventory Provider'), array('action' => 'delete', $inventoryProductInventoryProvider['InventoryProductInventoryProvider']['id']), array(), __('Are you sure you want to delete # %s?', $inventoryProductInventoryProvider['InventoryProductInventoryProvider']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Inventory Product Inventory Providers'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Inventory Product Inventory Provider'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Inventory Products'), array('controller' => 'inventory_products', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Inventory Product'), array('controller' => 'inventory_products', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Inventory Providers'), array('controller' => 'inventory_providers', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Inventory Provider'), array('controller' => 'inventory_providers', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
