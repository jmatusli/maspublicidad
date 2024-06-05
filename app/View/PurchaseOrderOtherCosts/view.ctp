<div class="purchaseOrderOtherCosts view">
<?php 
	echo "<h2>".__('Purchase Order Other Cost')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Purchase Order')."</dt>";
		echo "<dd>".$this->Html->link($purchaseOrderOtherCost['PurchaseOrder']['id'], array('controller' => 'purchase_orders', 'action' => 'view', $purchaseOrderOtherCost['PurchaseOrder']['id']))."</dd>";
		echo "<dt>".__('Task Description')."</dt>";
		echo "<dd>".h($purchaseOrderOtherCost['PurchaseOrderOtherCost']['task_description'])."</dd>";
		echo "<dt>".__('Department')."</dt>";
		echo "<dd>".$this->Html->link($purchaseOrderOtherCost['Department']['name'], array('controller' => 'departments', 'action' => 'view', $purchaseOrderOtherCost['Department']['id']))."</dd>";
		echo "<dt>".__('Task Quantity')."</dt>";
		echo "<dd>".h($purchaseOrderOtherCost['PurchaseOrderOtherCost']['task_quantity'])."</dd>";
		echo "<dt>".__('Task Unit Cost')."</dt>";
		echo "<dd>".h($purchaseOrderOtherCost['PurchaseOrderOtherCost']['task_unit_cost'])."</dd>";
		echo "<dt>".__('Task Total Cost')."</dt>";
		echo "<dd>".h($purchaseOrderOtherCost['PurchaseOrderOtherCost']['task_total_cost'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Purchase Order Other Cost'), array('action' => 'edit', $purchaseOrderOtherCost['PurchaseOrderOtherCost']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Purchase Order Other Cost'), array('action' => 'delete', $purchaseOrderOtherCost['PurchaseOrderOtherCost']['id']), array(), __('Are you sure you want to delete # %s?', $purchaseOrderOtherCost['PurchaseOrderOtherCost']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Purchase Order Other Costs'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Purchase Order Other Cost'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Purchase Orders'), array('controller' => 'purchase_orders', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Purchase Order'), array('controller' => 'purchase_orders', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Departments'), array('controller' => 'departments', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Department'), array('controller' => 'departments', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
