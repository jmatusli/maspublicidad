<div class="invoiceProducts view">
<?php 
	echo "<h2>".__('Invoice Product')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Invoice')."</dt>";
		echo "<dd>".$this->Html->link($invoiceProduct['Invoice']['id'], array('controller' => 'invoices', 'action' => 'view', $invoiceProduct['Invoice']['id']))."</dd>";
		echo "<dt>".__('Product')."</dt>";
		echo "<dd>".$this->Html->link($invoiceProduct['Product']['name'], array('controller' => 'products', 'action' => 'view', $invoiceProduct['Product']['id']))."</dd>";
		echo "<dt>".__('Product Unit Price')."</dt>";
		echo "<dd>".h($invoiceProduct['InvoiceProduct']['product_unit_price'])."</dd>";
		echo "<dt>".__('Product Quantity')."</dt>";
		echo "<dd>".h($invoiceProduct['InvoiceProduct']['product_quantity'])."</dd>";
		echo "<dt>".__('Product Total Price')."</dt>";
		echo "<dd>".h($invoiceProduct['InvoiceProduct']['product_total_price'])."</dd>";
		echo "<dt>".__('Currency')."</dt>";
		echo "<dd>".$this->Html->link($invoiceProduct['Currency']['abbreviation'], array('controller' => 'currencies', 'action' => 'view', $invoiceProduct['Currency']['id']))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Invoice Product'), array('action' => 'edit', $invoiceProduct['InvoiceProduct']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Invoice Product'), array('action' => 'delete', $invoiceProduct['InvoiceProduct']['id']), array(), __('Are you sure you want to delete # %s?', $invoiceProduct['InvoiceProduct']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Invoice Products'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Invoice Product'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Invoices'), array('controller' => 'invoices', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Invoice'), array('controller' => 'invoices', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Currencies'), array('controller' => 'currencies', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Currency'), array('controller' => 'currencies', 'action' => 'add'))."</li>";
	echo "</ul>";
?>
</div>
