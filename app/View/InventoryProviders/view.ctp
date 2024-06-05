<div class="providers view">
<?php 
	echo "<h2>".__('Proveedor de Inventario')." ".$inventoryProvider['InventoryProvider']['name']."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($inventoryProvider['InventoryProvider']['name'])."</dd>";
		echo "<dt>".__('Address')."</dt>";
		if (!empty($inventoryProvider['InventoryProvider']['address'])){
			echo "<dd>".h($inventoryProvider['InventoryProvider']['address'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Phone')."</dt>";
		if (!empty($inventoryProvider['InventoryProvider']['phone'])){
			echo "<dd>".h($inventoryProvider['InventoryProvider']['phone'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Email')."</dt>";
		if (!empty($inventoryProvider['InventoryProvider']['email'])){
			echo "<dd>".$this->Text->autoLinkEmails($inventoryProvider['InventoryProvider']['email'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Bool Active')."</dt>";
		echo "<dd>".($inventoryProvider['InventoryProvider']['bool_active']?__('Yes'):__('No'))."</dd>";
	echo "</dl>";
	
	echo "<br/>";
	echo "<div class='related'>";
		echo "<h2>".__('Productos para este Proveedor')."</h2>";
		echo "<ul>";
		foreach ($inventoryProducts as $inventoryProduct){
			echo "<li>".$this->Html->Link($inventoryProduct['InventoryProduct']['name'].(!empty($inventoryProduct['InventoryProduct']['code'])?" (".$inventoryProduct['InventoryProduct']['code'].") ":"").$inventoryProduct['InventoryProductLine']['name'],array('controller'=>'inventory_products','action'=>'view',$inventoryProduct['InventoryProduct']['id']))."</li>";
		}
		echo "</ul>";
	echo "</div>";
?> 
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Inventory Provider'), array('action' => 'edit', $inventoryProvider['InventoryProvider']['id']))."</li>";
		}
		//echo "<li>".$this->Form->postLink(__('Delete Inventory Provider'), array('action' => 'delete', $inventoryProvider['InventoryProvider']['id']), array(), __('Are you sure you want to delete # %s?', $inventoryProvider['InventoryProvider']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Inventory Providers'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Inventory Provider'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_inventoryproduct_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Products'), array('controller' => 'inventory_products', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryproduct_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Product'), array('controller' => 'inventory_products', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
