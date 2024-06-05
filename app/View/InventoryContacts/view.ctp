<div class="contacts view">
<?php 
	echo "<h2>".__('InventoryContact')."</h2>";
	echo "<dl>";
		echo "<dt>".__('InventoryClient')."</dt>";
		echo "<dd>".$this->Html->link($inventoryContact['InventoryClient']['name'], array('controller' => 'inventory_clients', 'action' => 'view', $inventoryContact['InventoryClient']['id']))."</dd>";
		echo "<dt>".__('First Name')."</dt>";
		echo "<dd>".h($inventoryContact['InventoryContact']['first_name'])."</dd>";
		echo "<dt>".__('Last Name')."</dt>";
		echo "<dd>".h($inventoryContact['InventoryContact']['last_name'])."</dd>";
		echo "<dt>".__('Phone')."</dt>";
		if (!empty($inventoryContact['InventoryContact']['phone'])){
			echo "<dd>".h($inventoryContact['InventoryContact']['phone'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		if (!empty($inventoryContact['InventoryContact']['cell'])){
			echo "<dd>".h($inventoryContact['InventoryContact']['cell'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Email')."</dt>";
		if (!empty($inventoryContact['InventoryContact']['email'])){
			echo "<dd>".$this->Text->autoLinkEmails($inventoryContact['InventoryContact']['email'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Department')."</dt>";
		if (!empty($inventoryContact['InventoryContact']['department'])){
			echo "<dd>".h($inventoryContact['InventoryContact']['department'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Bool Active')."</dt>";
		echo "<dd>".h($inventoryContact['InventoryContact']['bool_active']?__('Yes'):__('No'))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Contact'), array('action' => 'edit', $inventoryContact['InventoryContact']['id']))."</li>";
		}
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Eliminar Contacto'), array('action' => 'delete', $inventoryContact['InventoryContact']['id']), array(), __('Está seguro que quiere eliminar %s?', $inventoryContact['InventoryContact']['first_name']." ".$inventoryContact['InventoryContact']['last_name']))."</li>";
		}
		echo "<li>".$this->Html->link(__('List Inventory Contacts'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Contact'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_inventoryclient_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Clients'), array('controller' => 'inventory_clients', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryclient_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Client'), array('controller' => 'inventory_clients', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
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