<div class="companies form">
<?php 
	echo $this->Form->create('Company'); 
	echo "<fieldset>";
		echo "<legend>".__('Edit Company')."</legend>";
	
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
	
	echo "</fieldset>";

	echo $this->Form->end(__('Submit')); 
?>
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Company.id')), array(), __('Está seguro que quiere eliminar la compañía %s?', $this->Form->value('Company.name')));."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Companies'), array('action' => 'index'))."</li>";
	echo "</ul>";
</div>
