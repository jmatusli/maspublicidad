<div class="productionProcessProductOperationLocations view">
<?php 
	echo "<h2>".__('Production Process Product Operation Location')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Production Process Product')."</dt>";
		echo "<dd>".$this->Html->link($productionProcessProductOperationLocation['ProductionProcessProduct']['id'], array('controller' => 'production_process_products', 'action' => 'view', $productionProcessProductOperationLocation['ProductionProcessProduct']['id']))."</dd>";
		echo "<dt>".__('Operation Location')."</dt>";
		echo "<dd>".$this->Html->link($productionProcessProductOperationLocation['OperationLocation']['name'], array('controller' => 'operation_locations', 'action' => 'view', $productionProcessProductOperationLocation['OperationLocation']['id']))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Production Process Product Operation Location'), array('action' => 'edit', $productionProcessProductOperationLocation['ProductionProcessProductOperationLocation']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Production Process Product Operation Location'), array('action' => 'delete', $productionProcessProductOperationLocation['ProductionProcessProductOperationLocation']['id']), array(), __('Are you sure you want to delete # %s?', $productionProcessProductOperationLocation['ProductionProcessProductOperationLocation']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Production Process Product Operation Locations'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Process Product Operation Location'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Production Process Products'), array('controller' => 'production_process_products', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Process Product'), array('controller' => 'production_process_products', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Operation Locations'), array('controller' => 'operation_locations', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Operation Location'), array('controller' => 'operation_locations', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
