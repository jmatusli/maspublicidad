<div class="inventoryProductInventoryProviders form">
<?php echo $this->Form->create('InventoryProductInventoryProvider'); ?>
	<fieldset>
		<legend><?php echo __('Add Inventory Product Inventory Provider'); ?></legend>
	<?php
		echo $this->Form->input('inventory_product_id');
		echo $this->Form->input('inventory_provider_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Inventory Product Inventory Providers'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Inventory Products'), array('controller' => 'inventory_products', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Inventory Product'), array('controller' => 'inventory_products', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Inventory Providers'), array('controller' => 'inventory_providers', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Inventory Provider'), array('controller' => 'inventory_providers', 'action' => 'add')); ?> </li>
	</ul>
</div>
