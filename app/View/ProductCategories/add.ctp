<div class="productCategories form">
<?php echo $this->Form->create('ProductCategory'); ?>
	<fieldset>
		<legend><?php echo __('Add Product Category'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Product Categories'), array('action' => 'index')); ?></li>
	</ul>
</div>
