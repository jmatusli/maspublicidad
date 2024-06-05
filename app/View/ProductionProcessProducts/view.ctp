<div class="productionProcessProducts view">
<?php 
	echo "<h2>".__('Production Process Product')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Production Process')."</dt>";
		echo "<dd>".$this->Html->link($productionProcessProduct['ProductionProcess']['id'], array('controller' => 'production_processes', 'action' => 'view', $productionProcessProduct['ProductionProcess']['id']))."</dd>";
		echo "<dt>".__('Product')."</dt>";
		echo "<dd>".$this->Html->link($productionProcessProduct['Product']['name'], array('controller' => 'products', 'action' => 'view', $productionProcessProduct['Product']['id']))."</dd>";
		echo "<dt>".__('Product Description')."</dt>";
		echo "<dd>".h($productionProcessProduct['ProductionProcessProduct']['product_description'])."</dd>";
		echo "<dt>".__('Product Quantity')."</dt>";
		echo "<dd>".h($productionProcessProduct['ProductionProcessProduct']['product_quantity'])."</dd>";
		echo "<dt>".__('Operator')."</dt>";
		echo "<dd>".$this->Html->link($productionProcessProduct['Operator']['username'], array('controller' => 'users', 'action' => 'view', $productionProcessProduct['Operator']['id']))."</dd>";
		echo "<dt>".__('Machine')."</dt>";
		echo "<dd>".$this->Html->link($productionProcessProduct['Machine']['name'], array('controller' => 'machines', 'action' => 'view', $productionProcessProduct['Machine']['id']))."</dd>";
		echo "<dt>".__('Sales Order')."</dt>";
		echo "<dd>".$this->Html->link($productionProcessProduct['SalesOrder']['sales_order_code'], array('controller' => 'sales_orders', 'action' => 'view', $productionProcessProduct['SalesOrder']['id']))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Production Process Product'), array('action' => 'edit', $productionProcessProduct['ProductionProcessProduct']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Production Process Product'), array('action' => 'delete', $productionProcessProduct['ProductionProcessProduct']['id']), array(), __('Are you sure you want to delete # %s?', $productionProcessProduct['ProductionProcessProduct']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Production Process Products'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Process Product'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Production Processes'), array('controller' => 'production_processes', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Process'), array('controller' => 'production_processes', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Operator'), array('controller' => 'users', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Machines'), array('controller' => 'machines', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Machine'), array('controller' => 'machines', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Sales Orders'), array('controller' => 'sales_orders', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Sales Order'), array('controller' => 'sales_orders', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Production Process Product Operation Locations'), array('controller' => 'production_process_product_operation_locations', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Process Product Operation Location'), array('controller' => 'production_process_product_operation_locations', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
<div class="related">
<?php 
	if (!empty($productionProcessProduct['ProductionProcessProductOperationLocation'])){
		echo "<h3>".__('Related Production Process Product Operation Locations')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Production Process Product Id')."</th>";
				echo "<th>".__('Operation Location Id')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($productionProcessProduct['ProductionProcessProductOperationLocation'] as $productionProcessProductOperationLocation){ 
			echo "<tr>";
				echo "<td>".$productionProcessProductOperationLocation['production_process_product_id']."</td>";
				echo "<td>".$productionProcessProductOperationLocation['operation_location_id']."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'production_process_product_operation_locations', 'action' => 'view', $productionProcessProductOperationLocation['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'production_process_product_operation_locations', 'action' => 'edit', $productionProcessProductOperationLocation['id']));
					echo $this->Form->postLink(__('Delete'), array('controller' => 'production_process_product_operation_locations', 'action' => 'delete', $productionProcessProductOperationLocation['id']), array(), __('Are you sure you want to delete # %s?', $productionProcessProductOperationLocation['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
