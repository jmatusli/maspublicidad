<div class="providers view">
<?php 
	echo "<h2>".__('Provider')." ".$provider['Provider']['name']."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($provider['Provider']['name'])."</dd>";
		echo "<dt>".__('Address')."</dt>";
		if (!empty($provider['Provider']['address'])){
			echo "<dd>".h($provider['Provider']['address'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Phone')."</dt>";
		if (!empty($provider['Provider']['phone'])){
			echo "<dd>".h($provider['Provider']['phone'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Email')."</dt>";
		if (!empty($provider['Provider']['email'])){
			echo "<dd>".$this->Text->autoLinkEmails($provider['Provider']['email'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Bool Active')."</dt>";
		echo "<dd>".($provider['Provider']['bool_active']?__('Yes'):__('No'))."</dd>";
	echo "</dl>";
	
	echo "<br/>";
	echo "<div class='related'>";
		echo "<h2>".__('Productos para este Proveedor')."</h2>";
		echo "<ul>";
		foreach ($products as $product){
			echo "<li>".$this->Html->Link($product['Product']['name'],array('controller'=>'products','action'=>'view',$product['Product']['id']))."</li>";
		}
		echo "</ul>";
	echo "</div>";
?> 
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Provider'), array('action' => 'edit', $provider['Provider']['id']))."</li>";
		}
		//echo "<li>".$this->Form->postLink(__('Delete Provider'), array('action' => 'delete', $provider['Provider']['id']), array(), __('Are you sure you want to delete # %s?', $provider['Provider']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Providers'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Provider'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_product_index_permission){
			echo "<li>".$this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index'))."</li>";
		}
		if ($bool_product_add_permission){
			echo "<li>".$this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
