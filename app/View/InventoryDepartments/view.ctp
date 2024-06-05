<div class="inventoryDepartments view">
<?php 
	echo "<h2>".__('Inventory Department')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($inventoryDepartment['InventoryDepartment']['name'])."</dd>";
		echo "<dt>".__('Description')."</dt>";
		echo "<dd>".h($inventoryDepartment['InventoryDepartment']['description'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Inventory Department'), array('action' => 'edit', $inventoryDepartment['InventoryDepartment']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Inventory Department'), array('action' => 'delete', $inventoryDepartment['InventoryDepartment']['id']), array(), __('Are you sure you want to delete # %s?', $inventoryDepartment['InventoryDepartment']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Inventory Departments'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Inventory Department'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Inventory Product Lines'), array('controller' => 'inventory_product_lines', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Inventory Product Line'), array('controller' => 'inventory_product_lines', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
<div class="related">
<?php 
	if (!empty($inventoryDepartment['InventoryProductLine'])){
		echo "<h3>".__('Related Inventory Product Lines')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Name')."</th>";
				echo "<th>".__('Description')."</th>";
				echo "<th>".__('Inventory Department Id')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($inventoryDepartment['InventoryProductLine'] as $inventoryProductLine){ 
			echo "<tr>";
				echo "<td>".$inventoryProductLine['name']."</td>";
				echo "<td>".$inventoryProductLine['description']."</td>";
				echo "<td>".$inventoryProductLine['inventory_department_id']."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'inventory_product_lines', 'action' => 'view', $inventoryProductLine['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'inventory_product_lines', 'action' => 'edit', $inventoryProductLine['id']));
					echo $this->Form->postLink(__('Delete'), array('controller' => 'inventory_product_lines', 'action' => 'delete', $inventoryProductLine['id']), array(), __('Are you sure you want to delete # %s?', $inventoryProductLine['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
