<div class="vendorCommissionPayments form">
<?php echo $this->Form->create('VendorCommissionPayment'); ?>
	<fieldset>
		<legend><?php echo __('Edit Vendor Commission Payment'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_id');
		echo $this->Form->input('invoice_id');
		echo $this->Form->input('payment_date',array('dateFormat'=>'DMY'));
		echo $this->Form->input('commission_paid');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('VendorCommissionPayment.id')), array(), 'Está seguro que quiere eliminar este pago?')."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Vendor Commission Payments'), array('action' => 'index'))."</li>";
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
