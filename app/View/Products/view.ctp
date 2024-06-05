<div class="products view">
<?php 
	echo "<h2>".__('Product')." ".$product['Product']['name']." (".($product['Product']['bool_active']?"Activo":"Desactivado").")</h2>";
	echo "<dl style='display:block;float:left;'>";
		echo "<dt>".__('Product Category')."</dt>";
		echo "<dd>".$this->Html->link($product['ProductCategory']['name'], array('controller' => 'product_categories', 'action' => 'view', $product['ProductCategory']['id']))."</dd>";
		echo "<dt>".__('Code')."</dt>";
		if (!empty($product['Product']['code'])){
			echo "<dd>".h($product['Product']['code'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($product['Product']['name'])."</dd>";
		echo "<dt>".__('Description')."</dt>";
		if (!empty($product['Product']['description'])){
			echo "<dd>".h($product['Product']['description'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Product Unit Price')."</dt>";
		if (!empty($product['Product']['product_unit_price'])){
			echo "<dd>".$product['Currency']['abbreviation']." ".$product['Product']['product_unit_price']."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Se aplica IVA?')."</dt>";
		echo "<dd>".($product['Product']['bool_no_iva']?"Producto sin IVA":"Producto normal")."</dd>";
	echo "</dl>";
	
	echo "<div class='images'>";
		if (!empty($product['Product']['url_image'])){
			$url=$product['Product']['url_image'];
			echo "<img src='".$this->Html->url('/').$url."' alt='Producto' class='resize'></img>";
		}
	echo "</div>";
	
	/*
	echo "<br/>";
	
	echo "<div class='related'>";
		echo "<h2>".__('Proveedores para este Producto')."</h2>";
		echo "<ul>";
		foreach ($providers as $provider){
			echo "<li>".$this->Html->Link($provider['Provider']['name'],array('controller'=>'providers','action'=>'view',$provider['Provider']['id']))."</li>";
		}
		echo "</ul>";
	echo "</div>";
	*/
?> 
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Product'), array('action' => 'edit', $product['Product']['id']))."</li>";
		}
		//echo "<li>".$this->Form->postLink(__('Delete Product'), array('action' => 'delete', $product['Product']['id']), array(), __('Are you sure you want to delete # %s?', $product['Product']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Products'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Product'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_provider_index_permission){
			echo "<li>".$this->Html->link(__('List Providers'), array('controller' => 'providers', 'action' => 'index'))."</li>";
		}
		if ($bool_provider_index_permission){
			echo "<li>".$this->Html->link(__('New Provider'), array('controller' => 'providers', 'action' => 'add'))."</li>";
		}
		if ($bool_productcategory_index_permission){
			echo "<li>".$this->Html->link(__('List Product Categories'), array('controller' => 'product_categories', 'action' => 'index'))."</li>";
		}
		if ($bool_productcategory_add_permission){
			echo "<li>".$this->Html->link(__('New Product Category'), array('controller' => 'product_categories', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
<div class="related">
<?php 
	if (!empty($product['Provider'])){
		echo "<h3>".__('Related Providers')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Name')."</th>";
				echo "<th>".__('Address')."</th>";
				echo "<th>".__('Phone')."</th>";
				echo "<th>".__('Email')."</th>";
				echo "<th>".__('Bool Active')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($product['Provider'] as $provider){
			echo "<tr>";
				echo "<td>".$provider['name']."</td>";
				echo "<td>".$provider['address']."</td>";
				echo "<td>".$provider['phone']."</td>";
				echo "<td>".$provider['email']."</td>";
				echo "<td>".$provider['bool_active']."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'providers', 'action' => 'view', $provider['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'providers', 'action' => 'edit', $provider['id']));
					echo $this->Form->postLink(__('Delete'), array('controller' => 'providers', 'action' => 'delete', $provider['id']), array(), __('Are you sure you want to delete # %s?', $provider['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
