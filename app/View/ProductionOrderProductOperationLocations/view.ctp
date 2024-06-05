<div class="productionOrderProductOperationLocations view">
<?php 
	echo "<h2>".__('Production Order Product Operation Location')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Production Order Product')."</dt>";
		echo "<dd>".$this->Html->link($productionOrderProductOperationLocation['ProductionOrderProduct']['id'], array('controller' => 'production_order_products', 'action' => 'view', $productionOrderProductOperationLocation['ProductionOrderProduct']['id']))."</dd>";
		echo "<dt>".__('Operation Location')."</dt>";
		echo "<dd>".$this->Html->link($productionOrderProductOperationLocation['OperationLocation']['name'], array('controller' => 'operation_locations', 'action' => 'view', $productionOrderProductOperationLocation['OperationLocation']['id']))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Production Order Product Operation Location'), array('action' => 'edit', $productionOrderProductOperationLocation['ProductionOrderProductOperationLocation']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Production Order Product Operation Location'), array('action' => 'delete', $productionOrderProductOperationLocation['ProductionOrderProductOperationLocation']['id']), array(), __('Are you sure you want to delete # %s?', $productionOrderProductOperationLocation['ProductionOrderProductOperationLocation']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Production Order Product Operation Locations'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Order Product Operation Location'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Production Order Products'), array('controller' => 'production_order_products', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Order Product'), array('controller' => 'production_order_products', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Operation Locations'), array('controller' => 'operation_locations', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Operation Location'), array('controller' => 'operation_locations', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
