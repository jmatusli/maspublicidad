<div class="products view">
<?php 
	echo "<h2>".__('Inventory Product')." ".$inventoryProduct['InventoryProduct']['name'].($inventoryProduct['InventoryProduct']['bool_active']?"":" (Desactivado)")."</h2>";
	echo "<dl style='display:block;float:left;'>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($inventoryProduct['InventoryProduct']['name'])."</dd>";
		echo "<dt>".__('Code')."</dt>";
		if (!empty($inventoryProduct['InventoryProduct']['code'])){
			echo "<dd>".h($inventoryProduct['InventoryProduct']['code'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Brand')."</dt>";
		if (!empty($inventoryProduct['InventoryProduct']['brand'])){
			echo "<dd>".h($inventoryProduct['InventoryProduct']['brand'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		
		echo "<dt>".__('Description')."</dt>";
		if (!empty($inventoryProduct['InventoryProduct']['description'])){
			echo "<dd>".h($inventoryProduct['InventoryProduct']['description'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Inventory Product Line')."</dt>";
		echo "<dd>".$this->Html->link($inventoryProduct['InventoryProductLine']['name'], array('controller' => 'product_categories', 'action' => 'view', $inventoryProduct['InventoryProductLine']['id']))."</dd>";
		echo "<dt>".__('Product Unit Cost')."</dt>";
		if (!empty($inventoryProduct['InventoryProduct']['product_unit_cost'])){
			echo "<dd>".$inventoryProduct['Currency']['abbreviation']." ".$inventoryProduct['InventoryProduct']['product_unit_cost']."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		if ($inventoryProduct['InventoryProduct']['bool_promotion']){
			echo "<dt>".__('Product Unit Price')." A</dt>";
			if (!empty($inventoryProduct['InventoryProduct']['product_unit_price_A'])){
				echo "<dd>".$inventoryProduct['Currency']['abbreviation']." ".$inventoryProduct['InventoryProduct']['product_unit_price_A']."</dd>";
			}
			else {
				echo "<dd>-</dd>";
			}
			echo "<dt>".__('Product Unit Price')." B</dt>";
			if (!empty($inventoryProduct['InventoryProduct']['product_unit_price_B'])){
				echo "<dd>".$inventoryProduct['Currency']['abbreviation']." ".$inventoryProduct['InventoryProduct']['product_unit_price_B']."</dd>";
			}
			else {
				echo "<dd>-</dd>";
			}
			echo "<dt>".__('Product Unit Price')." C</dt>";
			if (!empty($inventoryProduct['InventoryProduct']['product_unit_price_C'])){
				echo "<dd>".$inventoryProduct['Currency']['abbreviation']." ".$inventoryProduct['InventoryProduct']['product_unit_price_C']."</dd>";
			}
			else {
				echo "<dd>-</dd>";
			}
		}
		echo "<dt>".__('Measuring Unit')."</dt>";
		if (!empty($inventoryProduct['MeasuringUnit']['id'])){
			echo "<dd>".$inventoryProduct['MeasuringUnit']['name']."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		if ($inventoryProduct['InventoryProductLine']['id']==INVENTORY_PRODUCT_LINE_PER_METER){
			echo "<dt>".__('Width')."</dt>";
			echo "<dd>".$inventoryProduct['InventoryProduct']['width']."</dd>";
		}
		echo "<dt>".__('Producto Promocional')."</dt>";
		echo "<dd>".($inventoryProduct['InventoryProduct']['bool_promotion']?__('Yes'):__('No'))."</dd>";
	echo "</dl>";
	
	
	echo "<div class='images'>";
		if (!empty($inventoryProduct['InventoryProduct']['url_image'])){
			$url=$inventoryProduct['InventoryProduct']['url_image'];
			echo "<img src='".$this->Html->url('/').$url."' alt='Producto' class='resize'></img>";
		}
	echo "</div>";
	echo "<br/>";
	echo "<br/>";
	echo "<div class='related'>";
		echo "<h2>".__('Proveedores para este Producto')."</h2>";
		echo "<ul>";
		foreach ($inventoryProviders as $inventoryProvider){
			echo "<li>".$this->Html->Link($inventoryProvider['InventoryProvider']['name'],array('controller'=>'inventory_providers','action'=>'view',$inventoryProvider['InventoryProvider']['id']))."</li>";
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
			echo "<li>".$this->Html->link(__('Edit Inventory Product'), array('action' => 'edit', $inventoryProduct['InventoryProduct']['id']))."</li>";
			echo "<br/>";
		}
		if ($bool_delete_permission){
			//echo "<li>".$this->Form->postLink(__('Delete Inventory Product'), array('action' => 'delete', $inventoryProduct['InventoryProduct']['id']), array(), __('Está seguro que quiere eliminar producto # %s?', $inventoryProduct['InventoryProduct']['code']." ".$inventoryProduct['InventoryProduct']['name']))."</li>";
			//echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Inventory Products'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Inventory Product'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_inventoryprovider_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Providers'), array('controller' => 'inventory_providers', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryprovider_index_permission){
			echo "<li>".$this->Html->link(__('New Inventory Provider'), array('controller' => 'inventory_providers', 'action' => 'add'))."</li>";
		}
		if ($bool_inventoryproductline_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Product Lines'), array('controller' => 'product_categories', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryproductline_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Product Line'), array('controller' => 'product_categories', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
<div class="related">
<?php 
	if (!empty($inventoryProduct['InventoryProvider'])){
		echo "<h3>".__('Related Providers')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Name')."</th>";
				echo "<th>".__('Address')."</th>";
				echo "<th>".__('Phone')."</th>";
				echo "<th>".__('Email')."</th>";
				echo "<th>".__('Bool Active')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($inventoryProduct['InventoryProvider'] as $inventoryProvider){
			echo "<tr>";
				echo "<td>".$inventoryProvider['name']."</td>";
				echo "<td>".$inventoryProvider['address']."</td>";
				echo "<td>".$inventoryProvider['phone']."</td>";
				echo "<td>".$inventoryProvider['email']."</td>";
				echo "<td>".$inventoryProvider['bool_active']."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'inventory_providers', 'action' => 'view', $inventoryProvider['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'inventory_providers', 'action' => 'edit', $inventoryProvider['id']));
					echo $this->Form->postLink(__('Delete'), array('controller' => 'inventory_providers', 'action' => 'delete', $inventoryProvider['id']), array(), __('Are you sure you want to delete # %s?', $inventoryProvider['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
