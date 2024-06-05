<div class="productionOrderProductDepartments view">
<?php 
	echo "<h2>".__('Production Order Product Department')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Production Order Product')."</dt>";
		echo "<dd>".$this->Html->link($productionOrderProductDepartment['ProductionOrderProduct']['id'], array('controller' => 'production_order_products', 'action' => 'view', $productionOrderProductDepartment['ProductionOrderProduct']['id']))."</dd>";
		echo "<dt>".__('Department')."</dt>";
		echo "<dd>".$this->Html->link($productionOrderProductDepartment['Department']['name'], array('controller' => 'departments', 'action' => 'view', $productionOrderProductDepartment['Department']['id']))."</dd>";
		echo "<dt>".__('Rank')."</dt>";
		echo "<dd>".h($productionOrderProductDepartment['ProductionOrderProductDepartment']['rank'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Production Order Product Department'), array('action' => 'edit', $productionOrderProductDepartment['ProductionOrderProductDepartment']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Production Order Product Department'), array('action' => 'delete', $productionOrderProductDepartment['ProductionOrderProductDepartment']['id']), array(), __('Are you sure you want to delete # %s?', $productionOrderProductDepartment['ProductionOrderProductDepartment']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Production Order Product Departments'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Order Product Department'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Production Order Products'), array('controller' => 'production_order_products', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Order Product'), array('controller' => 'production_order_products', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Departments'), array('controller' => 'departments', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Department'), array('controller' => 'departments', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
