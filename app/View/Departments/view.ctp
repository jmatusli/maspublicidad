<div class="departments view">
<?php 
	echo "<h2>".__('Department')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($department['Department']['name'])."</dd>";
		echo "<dt>".__('Abbreviation')."</dt>";
		echo "<dd>".h($department['Department']['abbreviation'])."</dd>";
		echo "<dt>".__('Description')."</dt>";
		if (!empty($department['Department']['description'])){
			echo "<dd>".h($department['Department']['description'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Department'), array('action' => 'edit', $department['Department']['id']))."</li>";
			echo "<br/>";
		}
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete Department'), array('action' => 'delete', $department['Department']['id']), array(), __('Está seguro que quiere eliminar departamento %s?', $department['Department']['name']))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Departments'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Department'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_inventoryproductline_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Product Lines'), array('controller' => 'inventory_product_lines', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryproductline_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Product Line'), array('controller' => 'inventory_product_lines', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?> 
</div>
<div class="related">
<?php 
	if (!empty($department['InventoryProductLine'])){
		echo "<h3>".__('Familias de Producto Relacionadas')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Name')."</th>";
				echo "<th>".__('Description')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($department['InventoryProductLine'] as $inventoryProductLine){ 
			echo "<tr>";
				echo "<td>".$inventoryProductLine['name']."</td>";
				echo "<td>".$inventoryProductLine['description']."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'inventory_product_lines', 'action' => 'view', $inventoryProductLine['id']));
					//echo $this->Html->link(__('Edit'), array('controller' => 'inventory_product_lines', 'action' => 'edit', $inventoryProductLine['id']));
					//echo $this->Form->postLink(__('Delete'), array('controller' => 'inventory_product_lines', 'action' => 'delete', $inventoryProductLine['id']), array(), __('Are you sure you want to delete # %s?', $inventoryProductLine['id']));
					
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>