<div class="contacts form">
<?php echo $this->Form->create('InventoryContact'); ?>
	<fieldset>
		<legend><?php echo __('Edit Contact'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('inventory_client_id',array('empty'=>array('0'=>'Seleccione Cliente...')));
		echo $this->Form->input('first_name');
		echo $this->Form->input('last_name');
		echo $this->Form->input('phone');
		echo $this->Form->input('cell');
		echo $this->Form->input('email');
		echo $this->Form->input('department');
		echo $this->Form->input('bool_active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<!--li><?php //echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Contact.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('Contact.id'))); ?></li-->
		<li><?php echo $this->Html->link(__('List Inventory Contacts'), array('action' => 'index')); ?></li>
		<br/>
	<?php
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Eliminar Contacto de Inventario'), array('action' => 'delete', $this->request->data['InventoryContact']['id']), array(), __('Está seguro que quiere eliminar %s?', $this->request->data['InventoryContact']['first_name']." ".$this->request->data['InventoryContact']['last_name']))."</li>";
		}
		echo "<li>".$this->Html->link(__('List Inventory Contacts'), array('action' => 'index'))."</li>";
		if ($bool_inventoryclient_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Clients'), array('controller' => 'clients', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryclient_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Client'), array('controller' => 'clients', 'action' => 'add'))."</li>";
		}
	?>
	</ul>
</div>
