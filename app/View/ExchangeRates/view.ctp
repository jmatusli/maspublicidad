<div class="productCategories view">
<h2><?php echo __('Exchange Rate'); ?></h2>
	<dl>
		<dt><?php echo __('Application Date'); ?></dt>
		<dd>
			<?php 
				$applicationdate=new DateTime($exchangeRate['ExchangeRate']['application_date']); 
				echo $applicationdate->format('d-m-Y');
			?>
		</dd>
		<dt><?php echo __('Conversion Currency'); ?></dt>
		<dd>
			<?php echo h($exchangeRate['ConversionCurrency']['abbreviation']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Rate'); ?></dt>
		<dd>
			<?php echo h($exchangeRate['ExchangeRate']['rate']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Base Currency'); ?></dt>
		<dd>
			<?php echo h($exchangeRate['BaseCurrency']['abbreviation']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Exchange Rate'), array('action' => 'edit', $exchangeRate['ExchangeRate']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Exchange Rates'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Exchange Rate'), array('action' => 'add')); ?> </li>
		<br/>
		<li><?php echo $this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add')); ?> </li>
	</ul>
</div>