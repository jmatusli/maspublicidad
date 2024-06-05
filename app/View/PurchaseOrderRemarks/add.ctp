<div class="purchaseOrderRemarks form">
<?php echo $this->Form->create('PurchaseOrderRemark'); ?>
	<fieldset>
		<legend><?php echo __('Add Purchase Order Remark'); ?></legend>
	<?php
		echo $this->Form->input('user_id');
		echo $this->Form->input('purchase_order_id');
		echo $this->Form->input('remark_datetime');
		echo $this->Form->input('remark_text');
		echo $this->Form->input('action_type_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Purchase Order Remarks'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Purchase Orders'), array('controller' => 'purchase_orders', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Purchase Order'), array('controller' => 'purchase_orders', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Action Types'), array('controller' => 'action_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Action Type'), array('controller' => 'action_types', 'action' => 'add')); ?> </li>
	</ul>
</div>
