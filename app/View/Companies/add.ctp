<div class="companies form">
<?php 
	echo $this->Form->create('Company'); 
	echo "<fieldset>";
		echo "<legend>".__('Add Company')."</legend>";

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
		echo "<li>".$this->Html->link(__('List Companies'), array('action' => 'index'))."</li>";
	echo "</ul>";
?>
</div>
