<div class="vipClientObjectives form">
<?php 
	echo $this->Form->create('VipClientObjective'); 
	echo "<fieldset>";
		echo "<legend>".__('Add Vip Client Objective')."</legend>";
		echo $this->Form->input('objective_date',array('dateFormat'=>'DMY'));
		echo $this->Form->input('client_id');
		echo $this->Form->input('minimum_objective');
		echo $this->Form->input('maximum_objective');
	echo "</fieldset>";
	echo $this->Form->end(__('Submit')); 
?>
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('List Vip Client Objectives'), array('action' => 'index'))."</li>";
		if ($bool_client_index_permission){
			echo "<br/>";
			echo "<li>".$this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index'))."</li>";
		}
		if ($bool_client_add_permission){
			echo "<li>".$this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
