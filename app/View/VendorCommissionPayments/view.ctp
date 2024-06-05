<div class="vendorCommissionPayments view">
<?php 
	echo "<h2>".__('Vendor Commission Payment')."</h2>";
	echo "<dl>";
		$paymentDateTime=new DateTime($vendorCommissionPayment['VendorCommissionPayment']['payment_date']);
		echo "<dt>".__('User')."</dt>";
		echo "<dd>".$this->Html->link($vendorCommissionPayment['User']['username'], array('controller' => 'users', 'action' => 'view', $vendorCommissionPayment['User']['id']))."</dd>";
		echo "<dt>".__('Invoice')."</dt>";
		echo "<dd>".$this->Html->link($vendorCommissionPayment['Invoice']['invoice_code'], array('controller' => 'invoices', 'action' => 'view', $vendorCommissionPayment['Invoice']['id']))."</dd>";
		echo "<dt>".__('Payment Date')."</dt>";
		echo "<dd>".$paymentDateTime->format('d-m-Y')."</dd>";
		echo "<dt>".__('Commission Paid')."</dt>";
		echo "<dd>".h($vendorCommissionPayment['VendorCommissionPayment']['commission_paid'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Vendor Commission Payment'), array('action' => 'edit', $vendorCommissionPayment['VendorCommissionPayment']['id']))."</li>";
			echo "<br/>";
		}
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete Vendor Commission Payment'), array('action' => 'delete', $vendorCommissionPayment['VendorCommissionPayment']['id']), array(), 'Está seguro que quiere eliminar el pago para la factura ', $vendorCommissionPayment['Invoice']['invoice_code']))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Vendor Commission Payments'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Vendor Commission Payment'), array('action' => 'add'))."</li>";
		if ($bool_invoice_index_permission){
			echo "<br/>";
			echo "<li>".$this->Html->link(__('List Invoices'), array('controller' => 'invoices', 'action' => 'index'))."</li>";
		}
		if ($bool_invoice_add_permission){
			echo "<li>".$this->Html->link(__('New Invoice'), array('controller' => 'invoices', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?> 
</div>
