<div class="roles form">
<?php echo $this->Form->create('Role'); ?>
	<fieldset>
		<legend><?php echo __('Editar Papel')." ".$this->request->data['Role']['name']; ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
    echo $this->Form->input('list_order');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
<?php 
  echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		//echo "<li>".$this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Role.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('Role.id')))."</li>";
		echo "<li>".$this->Html->link(__('List Roles'), array('action' => 'index'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add'))."</li>";
	echo "</ul>";
?>
</div>
