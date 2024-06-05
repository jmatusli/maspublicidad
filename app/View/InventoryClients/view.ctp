<div class="clients view">
<?php 
	echo "<h2>".__('Inventory Client')." ".$inventoryClient['InventoryClient']['name']."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($inventoryClient['InventoryClient']['name'])."</dd>";
		echo "<dt>".__('RUC')."</dt>";
		if (!empty($inventoryClient['InventoryClient']['ruc'])){
			echo "<dd>".h($inventoryClient['InventoryClient']['ruc'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Address')."</dt>";
		if (!empty($inventoryClient['InventoryClient']['address'])){
			echo "<dd>".h($inventoryClient['InventoryClient']['address'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Phone')."</dt>";
		if (!empty($inventoryClient['InventoryClient']['phone'])){
			echo "<dd>".h($inventoryClient['InventoryClient']['phone'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Cell')."</dt>";
		if (!empty($inventoryClient['InventoryClient']['cell'])){
			echo "<dd>".h($inventoryClient['InventoryClient']['cell'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Bool Active')."</dt>";
		echo "<dd>".($inventoryClient['InventoryClient']['bool_active']?__('Yes'):__('No'))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Inventory Client'), array('action' => 'edit', $inventoryClient['InventoryClient']['id']))."</li>";
		}
		//echo "<li>".$this->Form->postLink(__('Delete Inventory Client'), array('action' => 'delete', $inventoryClient['InventoryClient']['id']), array(), __('Are you sure you want to delete # %s?', $inventoryClient['InventoryClient']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Inventory Clients'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Inventory Client'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_inventorycontact_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Contacts'), array('controller' => 'inventory_contacts', 'action' => 'index'))."</li>";
		}
		if ($bool_inventorycontact_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Contact'), array('controller' => 'inventory_contacts', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
<div class="related">
<?php 
	if (!empty($inventoryClient['InventoryContact'])){
		echo "<h3>".__('Contactos para este Cliente')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('First Name')."</th>";
				echo "<th>".__('Last Name')."</th>";
				echo "<th>".__('Phone')."</th>";
				echo "<th>".__('Cell')."</th>";
				echo "<th>".__('Email')."</th>";
				echo "<th>".__('Department')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($inventoryClient['InventoryContact'] as $contact){
			if ($contact['bool_active']){
				echo "<tr>";
			}
			else {
				echo "<tr class='italic'>";
			}
				echo "<td>".$contact['first_name']."</td>";
				echo "<td>".$contact['last_name'].($contact['bool_active']?"":" (Inactivo)")."</td>";
				echo "<td>".$contact['phone']."</td>";
				echo "<td>".$contact['cell']."</td>";
				echo "<td>".$contact['email']."</td>";
				echo "<td>".$contact['department']."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'inventory_contacts', 'action' => 'view', $contact['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'inventory_contacts', 'action' => 'edit', $contact['id']));
					//echo $this->Form->postLink(__('Delete'), array('controller' => 'inventory_contacts', 'action' => 'delete', $contact['id']), array(), __('Are you sure you want to delete # %s?', $contact['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
<script>
	function formatNumbers(){
		$("td.number span.amountright").each(function(){
			if (Math.abs(parseFloat($(this).text()))<0.001){
				$(this).text("0");
			}
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,0,'.',',');
		});
	}
	
	function formatCSCurrencies(){
		$("td.CScurrency").each(function(){
			
			if (parseFloat($(this).find('.amountright').text())<0){
				$(this).find('.amountright').prepend("-");
			}
			$(this).find('.amountright').number(true,2);
			$(this).find('.currency').text("C$");
		});
	}
	
	function formatUSDCurrencies(){
		$("td.USDcurrency").each(function(){
			
			if (parseFloat($(this).find('.amountright').text())<0){
				$(this).find('.amountright').prepend("-");
			}
			$(this).find('.amountright').number(true,2);
			$(this).find('.currency').text("US$");
		});
	}
	
	$(document).ready(function(){
		formatNumbers();
		formatCSCurrencies();
		formatUSDCurrencies();
	});
</script>