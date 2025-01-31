<div class="contacts form">
<?php echo $this->Form->create('InventoryContact'); ?>
	<fieldset>
		<legend><?php echo __('Add Contact'); ?></legend>
	<?php
		echo $this->Form->input('inventory_client_id',array('default'=>'0','empty'=>array('0'=>'Seleccione Cliente...')));
		echo $this->Form->input('first_name');
		echo $this->Form->input('last_name');
		echo $this->Form->input('phone');
		echo $this->Form->input('cell');
		echo $this->Form->input('email');
		echo $this->Form->input('department');
		echo $this->Form->input('bool_active',array('checked'=>true,'div'=>array('class'=>'hidden')));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Inventory Contacts'), array('action' => 'index')); ?></li>
		<br/>
	<?php
		if ($bool_inventoryclient_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Clients'), array('controller' => 'inventory_clients', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryclient_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Client'), array('controller' => 'inventory_clients', 'action' => 'add'))."</li>";
		}
		if ($bool_quotation_index_permission){
			echo "<li>".$this->Html->link(__('List Quotations'), array('controller' => 'quotations', 'action' => 'index'))."</li>";
		}
		if ($bool_quotation_add_permission){
			echo "<li>".$this->Html->link(__('New Quotation'), array('controller' => 'quotations', 'action' => 'add'))."</li>";
		}
	?>
	</ul>
</div>
