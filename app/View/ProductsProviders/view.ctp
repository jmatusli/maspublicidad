<div class="productsProviders view">
<?php 
	echo "<h2>".__('Products Provider')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Product')."</dt>";
		echo "<dd>".$this->Html->link($productsProvider['Product']['name'], array('controller' => 'products', 'action' => 'view', $productsProvider['Product']['id']))."</dd>";
		echo "<dt>".__('Provider')."</dt>";
		echo "<dd>".$this->Html->link($productsProvider['Provider']['name'], array('controller' => 'providers', 'action' => 'view', $productsProvider['Provider']['id']))."</dd>";
		echo "<dt>".__('Application Date')."</dt>";
		echo "<dd>".h($productsProvider['ProductsProvider']['application_date'])."</dd>";
		echo "<dt>".__('Bool Active')."</dt>";
		echo "<dd>".h($productsProvider['ProductsProvider']['bool_active'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Products Provider'), array('action' => 'edit', $productsProvider['ProductsProvider']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Products Provider'), array('action' => 'delete', $productsProvider['ProductsProvider']['id']), array(), __('Are you sure you want to delete # %s?', $productsProvider['ProductsProvider']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Products Providers'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Products Provider'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Providers'), array('controller' => 'providers', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Provider'), array('controller' => 'providers', 'action' => 'add'))."</li>";
	echo "</ul>";
?>
</div>
