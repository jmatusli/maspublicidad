<div class="productionOrderStates form">
<?php
	echo $this->Form->create('ProductionOrderState'); 
	echo '<fieldset>';
		echo '<legend>'.__('Add Production Order State').'</legend>';
		echo $this->Form->input('name');
    echo $this->Form->input('list_order');
	echo '</fieldset>';
	echo $this->Form->Submit(__('Submit'));
	echo $this->Form->end();
?>
</div>
<div class="actions">
<?php
	echo '<h3>'.__('Actions').'</h3>';
	echo '<ul>';
		echo '<li>'.$this->Html->link(__('List Production Order States'), ['action' => 'resumen']).'</li>';
	echo '</ul>';
?>
</div>
