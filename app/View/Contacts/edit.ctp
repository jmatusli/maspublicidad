<div class="contacts form">
<?php echo $this->Form->create('Contact'); ?>
	<fieldset>
		<legend><?php echo __('Edit Contact'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('client_id',array('empty'=>array('0'=>'Seleccione Cliente...')));
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
		<li><?php echo $this->Html->link(__('List Contacts'), array('action' => 'index')); ?></li>
		<br/>
	<?php
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Eliminar Contacto'), array('action' => 'delete', $this->request->data['Contact']['id']), array(), __('Está seguro que quiere eliminar %s?', $this->request->data['Contact']['first_name']." ".$this->request->data['Contact']['last_name']))."</li>";
		}
		echo "<li>".$this->Html->link(__('List Contacts'), array('action' => 'index'))."</li>";
		if ($bool_client_index_permission){
			echo "<li>".$this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index'))."</li>";
		}
		if ($bool_client_add_permission){
			echo "<li>".$this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add'))."</li>";
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
