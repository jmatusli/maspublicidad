<div class="clients form">
<?php 
	echo $this->Form->create('InventoryClient'); 
		echo "<fieldset>";
			echo "<legend>".__('Edit Client')."</legend>";
			echo $this->Form->input('id');
			echo $this->Form->input('name');
			echo $this->Form->input('ruc');
			echo $this->Form->input('address');
			echo $this->Form->input('phone');
			echo $this->Form->input('cell');
			echo $this->Form->input('bool_active');
	echo "</fieldset>";

	echo "<h3>Contactos para este Cliente</h3>";
	echo "<table id='contactosParaCliente'>"; 
		echo "<thead>";
			echo "<tr>";
				echo "<th>".__('First Name')."</th>";
				echo "<th>".__('Last Name')."</th>";
				echo "<th>".__('Phone')."</th>";
				echo "<th>".__('Cell')."</th>";
				echo "<th>".__('Email')."</th>";
				echo "<th>".__('Department')."</th>";
				echo "<th style='width:20%;'>".__('Actions')."</th>";
			echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		for ($i=0;$i<count($existingInventoryContacts);$i++) { 
			echo "<tr row='".$i."'>";
				echo "<td class='contactfirstname'>".$this->Form->input('InventoryContact.'.$i.'.first_name',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactlastname'>".$this->Form->input('InventoryContact.'.$i.'.last_name',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactphone'>".$this->Form->input('InventoryContact.'.$i.'.phone',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactcell'>".$this->Form->input('InventoryContact.'.$i.'.cell',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactemail'>".$this->Form->input('InventoryContact.'.$i.'.email',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactdepartment'>".$this->Form->input('InventoryContact.'.$i.'.department',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactactions'>";
					//echo "<button class='removeContact'>".__('Remover Contacto')."</button>";
					echo "<button class='addContact' type='button'>".__('Añadir Contacto')."</button>";
				echo "</td>";
			echo "</tr>";			
		}		
		for ($i=count($existingInventoryContacts);$i<25;$i++) { 
			if ($i==count($existingInventoryContacts)){
				echo "<tr row='".$i."'>";
			} 
			else {
				echo "<tr row='".$i."' class='hidden'>";
			} 
				echo "<td class='contactfirstname'>".$this->Form->input('InventoryContact.'.$i.'.first_name',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactlastname'>".$this->Form->input('InventoryContact.'.$i.'.last_name',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactphone'>".$this->Form->input('InventoryContact.'.$i.'.phone',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactcell'>".$this->Form->input('InventoryContact.'.$i.'.cell',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactemail'>".$this->Form->input('InventoryContact.'.$i.'.email',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactdepartment'>".$this->Form->input('InventoryContact.'.$i.'.department',array('label'=>false,'required'=>false))."</td>";
				echo "<td class='contactactions'>";
					echo "<button class='removeContact'>".__('Remover Contacto')."</button>";
					echo "<button class='addContact' type='button'>".__('Añadir Contacto')."</button>";
				echo "</td>";
			echo "</tr>";			
		}		
		echo "</tbody>";
	echo "</table>";
	echo $this->Form->end(__('Submit')); 
?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<!--li><?php //echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Client.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('Client.id'))); ?></li-->
		<li><?php echo $this->Html->link(__('List Inventory Clients'), array('action' => 'index')); ?></li>
		<br/>
	<?php
		if ($bool_inventorycontact_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Contacts'), array('controller' => 'inventory_contacts', 'action' => 'index'))."</li>";
		}
		if ($bool_inventorycontact_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Contact'), array('controller' => 'inventory_contacts', 'action' => 'add'))."</li>";
		}
	?>
	</ul>
</div>
