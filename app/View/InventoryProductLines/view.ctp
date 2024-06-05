<div class="inventoryProductLines view">
<?php 
	echo "<h2>".__('Inventory Product Line')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($inventoryProductLine['InventoryProductLine']['name'])."</dd>";
		echo "<dt>".__('Description')."</dt>";
		if (!empty($inventoryProductLine['InventoryProductLine']['description'])){
		echo "<dd>".h($inventoryProductLine['InventoryProductLine']['description'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Department')."</dt>";
		echo "<dd>".$this->Html->link($inventoryProductLine['Department']['name'], array('controller' => 'departments', 'action' => 'view', $inventoryProductLine['Department']['id']))."</dd>";
		echo "<dt>".__('Producto Promocional')."</dt>";
		echo "<dd>".($inventoryProductLine['InventoryProductLine']['bool_promotion']?__("Yes"):__("No"))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Inventory Product Line'), array('action' => 'edit', $inventoryProductLine['InventoryProductLine']['id']))."</li>";
			echo "<br/>";
		}
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete Inventory Product Line'), array('action' => 'delete', $inventoryProductLine['InventoryProductLine']['id']), array(), __('Está seguro que quiere eliminar línea de producto %s?', $inventoryProductLine['InventoryProductLine']['name']))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Inventory Product Lines'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Inventory Product Line'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_department_index_permission){
			echo "<li>".$this->Html->link(__('List Departments'), array('controller' => 'departments', 'action' => 'index'))."</li>";
		}
		if ($bool_department_add_permission){
			echo "<li>".$this->Html->link(__('New Department'), array('controller' => 'departments', 'action' => 'add'))."</li>";
		}
		if ($bool_inventoryproduct_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Products'), array('controller' => 'inventory_products', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryproduct_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Product'), array('controller' => 'inventory_products', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?> 
</div>
<div class="related">
<?php 
	if (!empty($inventoryProductLine['InventoryProduct'])){
		echo "<h3>".__('Productos Relacionados')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Name')."</th>";
				echo "<th>".__('Code')."</th>";
				echo "<th>".__('Brand')."</th>";
				echo "<th>".__('Description')."</th>";
				echo "<th>".__('Url Image')."</th>";
				echo "<th>".__('Bool Active')."</th>";
				echo "<th>".__('Product Unit Cost')."</th>";
				if ($inventoryProductLine['InventoryProductLine']['bool_promotion']){
					echo "<th>".__('Product Unit Price A')."</th>";
					echo "<th>".__('Product Unit Price B')."</th>";
					echo "<th>".__('Product Unit Price C')."</th>";
				}
				echo "<th>".__('Currency')."</th>";
				//echo "<th>".__('Bool Complicated')."</th>";
				echo "<th>".__('Linear Meters')."</th>";
				echo "<th>".__('Width')."</th>";
				echo "<th>".__('Color')."</th>";
				echo "<th>".__('Weight')."</th>";
				echo "<th>".__('Measuring Unit')."</th>";
				echo "<th>".__('Producto Promocional')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($inventoryProductLine['InventoryProduct'] as $inventoryProduct){ 
			echo "<tr>";
				echo "<td>".$this->Html->link($inventoryProduct['name'],array('controller'=>'inventory_products','action'=>'view',$inventoryProduct['id']))."</td>";
				echo "<td>".$inventoryProduct['code']."</td>";
				echo "<td>".$inventoryProduct['brand']."</td>";
				echo "<td>".$inventoryProduct['description']."</td>";
				echo "<td>".$inventoryProduct['url_image']."</td>";
				echo "<td>".($inventoryProduct['bool_active']?__('Yes'):__('No'))."</td>";
				echo "<td>".$inventoryProduct['product_unit_cost']."</td>";
				if ($inventoryProductLine['InventoryProductLine']['bool_promotion']){
					echo "<td>".$inventoryProduct['product_unit_price_A']."</td>";
					echo "<td>".$inventoryProduct['product_unit_price_B']."</td>";
					echo "<td>".$inventoryProduct['product_unit_price_C']."</td>";
				}
				echo "<td>".$inventoryProduct['Currency']['abbreviation']."</td>";
				//echo "<td>".($inventoryProduct['bool_complicated']?__('Yes'):__('No'))."</td>";
				echo "<td>".$inventoryProduct['linear_meters']."</td>";
				echo "<td>".$inventoryProduct['width']."</td>";
				echo "<td>".$inventoryProduct['color']."</td>";
				echo "<td>".$inventoryProduct['weight']."</td>";
				echo "<td>".$inventoryProduct['MeasuringUnit']['abbreviation']."</td>";
				echo "<td>".($inventoryProduct['bool_promotion']?__('Yes'):__('No'))."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'inventory_products', 'action' => 'view', $inventoryProduct['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'inventory_products', 'action' => 'edit', $inventoryProduct['id']));
					echo $this->Form->postLink(__('Delete'), array('controller' => 'inventory_products', 'action' => 'delete', $inventoryProduct['id']), array(), __('Are you sure you want to delete # %s?', $inventoryProduct['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
