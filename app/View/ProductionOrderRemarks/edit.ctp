<div class="productionOrderRemarks form">
<?php echo $this->Form->create('ProductionOrderRemark'); ?>
	<fieldset>
		<legend><?php echo __('Edit Production Order Remark'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_id');
		echo $this->Form->input('production_order_id');
		echo $this->Form->input('remark_datetime');
		echo $this->Form->input('remark_text');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('ProductionOrderRemark.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('ProductionOrderRemark.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Production Order Remarks'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Production Orders'), array('controller' => 'production_orders', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Production Order'), array('controller' => 'production_orders', 'action' => 'add')); ?> </li>
	</ul>
</div>
