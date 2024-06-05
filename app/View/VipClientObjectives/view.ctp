<div class="vipClientObjectives view">
<?php 
	echo "<h2>".__('Vip Client Objective')."</h2>";
	echo "<dl>";
		$objectiveDateTime=new DateTime($vipClientObjective['VipClientObjective']['objective_date']);
		echo "<dt>".__('Client')."</dt>";
		echo "<dd>".$this->Html->link($vipClientObjective['Client']['name'], array('controller' => 'clients', 'action' => 'view', $vipClientObjective['Client']['id']))."</dd>";
		echo "<dt>".__('Minimum Objective')."</dt>";
		echo "<dd>".h($vipClientObjective['VipClientObjective']['minimum_objective'])."</dd>";
		echo "<dt>".__('Maximum Objective')."</dt>";
		echo "<dd>".h($vipClientObjective['VipClientObjective']['maximum_objective'])."</dd>";
		echo "<dt>".__('Objective Date')."</dt>";
		echo "<dd>".$objectiveDateTime->format('d-m-y')."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Vip Client Objective'), array('action' => 'edit', $vipClientObjective['VipClientObjective']['id']))."</li>";
			echo "<br/>";
		}
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete Vip Client Objective'), array('action' => 'delete', $vipClientObjective['VipClientObjective']['id']), array(), __('Está seguro que quiere eliminar el objetivo para el cliente VIP %s de fecha %s?', $vipClientObjective['Client']['name'], $objectiveDateTime->format('d-m-y')));
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Vip Client Objectives'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Vip Client Objective'), array('action' => 'add'))."</li>";
		if ($bool_client_index_permission){
			echo "<br/>";
			echo "<li>".$this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index'))."</li>";
		}
		if ($bool_client_add_permission){
			echo "<li>".$this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add'))."</li>";
		}
		"</ul>";
?> 
</div>
