<div class="taskTypes view">
<?php 
	echo "<h2>".__('Task Type')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($taskType['TaskType']['name'])."</dd>";
		echo "<dt>".__('Description')."</dt>";
		echo "<dd>".h($taskType['TaskType']['description'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Task Type'), array('action' => 'edit', $taskType['TaskType']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Task Type'), array('action' => 'delete', $taskType['TaskType']['id']), array(), __('Are you sure you want to delete # %s?', $taskType['TaskType']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Task Types'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Task Type'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Tasks'), array('controller' => 'tasks', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Task'), array('controller' => 'tasks', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
<div class="related">
<?php 
	if (!empty($taskType['Task'])){
		echo "<h3>".__('Related Tasks')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Requesting User Id')."</th>";
				echo "<th>".__('Task Type Id')."</th>";
				echo "<th>".__('Requirements')."</th>";
				echo "<th>".__('Acting User Id')."</th>";
				echo "<th>".__('Implementation')."</th>";
				echo "<th>".__('Date Executed')."</th>";
				echo "<th>".__('Receiving User Id')."</th>";
				echo "<th>".__('Hours Estimated')."</th>";
				echo "<th>".__('Remarks')."</th>";
				echo "<th>".__('Priority')."</th>";
				echo "<th>".__('Bool Active')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($taskType['Task'] as $task){ 
			echo "<tr>";
				echo "<td>".$task['requesting_user_id']."</td>";
				echo "<td>".$task['task_type_id']."</td>";
				echo "<td>".$task['requirements']."</td>";
				echo "<td>".$task['acting_user_id']."</td>";
				echo "<td>".$task['implementation']."</td>";
				echo "<td>".$task['date_executed']."</td>";
				echo "<td>".$task['receiving_user_id']."</td>";
				echo "<td>".$task['hours_estimated']."</td>";
				echo "<td>".$task['remarks']."</td>";
				echo "<td>".$task['priority']."</td>";
				echo "<td>".$task['bool_active']."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'tasks', 'action' => 'view', $task['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'tasks', 'action' => 'edit', $task['id']));
					echo $this->Form->postLink(__('Delete'), array('controller' => 'tasks', 'action' => 'delete', $task['id']), array(), __('Are you sure you want to delete # %s?', $task['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
