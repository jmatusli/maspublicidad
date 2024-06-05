<div class="invoiceSalesOrders view">
<?php 
	echo "<h2>".__('Invoice Sales Order')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Invoice')."</dt>";
		echo "<dd>".$this->Html->link($invoiceSalesOrder['Invoice']['invoice_code'], array('controller' => 'invoices', 'action' => 'view', $invoiceSalesOrder['Invoice']['id']))."</dd>";
		echo "<dt>".__('Sales Order')."</dt>";
		echo "<dd>".$this->Html->link($invoiceSalesOrder['SalesOrder']['sales_order_code'], array('controller' => 'sales_orders', 'action' => 'view', $invoiceSalesOrder['SalesOrder']['id']))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Invoice Sales Order'), array('action' => 'edit', $invoiceSalesOrder['InvoiceSalesOrder']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Invoice Sales Order'), array('action' => 'delete', $invoiceSalesOrder['InvoiceSalesOrder']['id']), array(), __('Are you sure you want to delete # %s?', $invoiceSalesOrder['InvoiceSalesOrder']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Invoice Sales Orders'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Invoice Sales Order'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Invoices'), array('controller' => 'invoices', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Invoice'), array('controller' => 'invoices', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Sales Orders'), array('controller' => 'sales_orders', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Sales Order'), array('controller' => 'sales_orders', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
