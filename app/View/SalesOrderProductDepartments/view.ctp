<div class="salesOrderProductDepartments view">
<?php 
	echo "<h2>".__('Sales Order Product Department')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Sales Order Product')."</dt>";
		echo "<dd>".$this->Html->link($salesOrderProductDepartment['SalesOrderProduct']['id'], array('controller' => 'sales_order_products', 'action' => 'view', $salesOrderProductDepartment['SalesOrderProduct']['id']))."</dd>";
		echo "<dt>".__('Department')."</dt>";
		echo "<dd>".$this->Html->link($salesOrderProductDepartment['Department']['name'], array('controller' => 'departments', 'action' => 'view', $salesOrderProductDepartment['Department']['id']))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Sales Order Product Department'), array('action' => 'edit', $salesOrderProductDepartment['SalesOrderProductDepartment']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Sales Order Product Department'), array('action' => 'delete', $salesOrderProductDepartment['SalesOrderProductDepartment']['id']), array(), __('Are you sure you want to delete # %s?', $salesOrderProductDepartment['SalesOrderProductDepartment']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Sales Order Product Departments'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Sales Order Product Department'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Sales Order Products'), array('controller' => 'sales_order_products', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Sales Order Product'), array('controller' => 'sales_order_products', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Departments'), array('controller' => 'departments', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Department'), array('controller' => 'departments', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
