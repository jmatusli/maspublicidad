<div class="productionOrderProducts view">
<?php 
	echo "<h2>".__('Production Order Product')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Production Order')."</dt>";
		echo "<dd>".$this->Html->link($productionOrderProduct['ProductionOrder']['id'], array('controller' => 'production_orders', 'action' => 'view', $productionOrderProduct['ProductionOrder']['id']))."</dd>";
		echo "<dt>".__('Product')."</dt>";
		echo "<dd>".$this->Html->link($productionOrderProduct['Product']['name'], array('controller' => 'products', 'action' => 'view', $productionOrderProduct['Product']['id']))."</dd>";
		echo "<dt>".__('Product Quantity')."</dt>";
		echo "<dd>".h($productionOrderProduct['ProductionOrderProduct']['product_quantity'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Production Order Product'), array('action' => 'edit', $productionOrderProduct['ProductionOrderProduct']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Production Order Product'), array('action' => 'delete', $productionOrderProduct['ProductionOrderProduct']['id']), array(), __('Are you sure you want to delete # %s?', $productionOrderProduct['ProductionOrderProduct']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Production Order Products'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Order Product'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Production Orders'), array('controller' => 'production_orders', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Order'), array('controller' => 'production_orders', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Production Order Product Operation Locations'), array('controller' => 'production_order_product_operation_locations', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Order Product Operation Location'), array('controller' => 'production_order_product_operation_locations', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
<div class="related">
<?php 
	if (!empty($productionOrderProduct['ProductionOrderProductOperationLocation'])){
		echo "<h3>".__('Related Production Order Product Operation Locations')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Production Order Product Id')."</th>";
				echo "<th>".__('Operation Location Id')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($productionOrderProduct['ProductionOrderProductOperationLocation'] as $productionOrderProductOperationLocation){ 
			echo "<tr>";
				echo "<td>".$productionOrderProductOperationLocation['production_order_product_id']."</td>";
				echo "<td>".$productionOrderProductOperationLocation['operation_location_id']."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'production_order_product_operation_locations', 'action' => 'view', $productionOrderProductOperationLocation['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'production_order_product_operation_locations', 'action' => 'edit', $productionOrderProductOperationLocation['id']));
					echo $this->Form->postLink(__('Delete'), array('controller' => 'production_order_product_operation_locations', 'action' => 'delete', $productionOrderProductOperationLocation['id']), array(), __('Are you sure you want to delete # %s?', $productionOrderProductOperationLocation['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
