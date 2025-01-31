<div class="stockItems form">
<?php echo $this->Form->create('StockItem'); ?>
	<fieldset>
		<legend><?php echo __('Edit Stock Item'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('product_id');
		echo $this->Form->input('product_unit_cost');
		echo $this->Form->input('currency_id');
		echo $this->Form->input('product_original_quantity');
		echo $this->Form->input('product_remaining_quantity');
		echo $this->Form->input('measuring_unit_id');
		echo $this->Form->input('stock_item_creation_date');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('StockItem.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('StockItem.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Stock Items'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Currencies'), array('controller' => 'currencies', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Currency'), array('controller' => 'currencies', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Measuring Units'), array('controller' => 'measuring_units', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Measuring Unit'), array('controller' => 'measuring_units', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Stock Item Logs'), array('controller' => 'stock_item_logs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Stock Item Log'), array('controller' => 'stock_item_logs', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Stock Movements'), array('controller' => 'stock_movements', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Stock Movement'), array('controller' => 'stock_movements', 'action' => 'add')); ?> </li>
	</ul>
</div>
