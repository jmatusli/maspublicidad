<div class="productCategories form">
<?php echo $this->Form->create('ExchangeRate'); ?>
	<fieldset>
		<legend><?php echo __('Edit Exchange Rate'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('application_date',array('dateFormat'=>'DMY'));
		echo $this->Form->input('conversion_currency_id');
		echo $this->Form->input('rate');
		echo $this->Form->input('base_currency_id');
		
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
<?php echo $this->Html->Link('Cancelar',array('action'=>'edit',$id),array( 'class' => 'btn btn-primary cancel')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<!--li><?php //echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('ExchangeRate.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('ExchangeRate.id'))); ?></li-->
		<li><?php echo $this->Html->link(__('List Exchange Rates'), array('action' => 'index')); ?></li>
		<br/>
		<li><?php echo $this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add')); ?> </li>
	</ul>
</div>