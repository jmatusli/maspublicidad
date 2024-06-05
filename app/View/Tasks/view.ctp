<div class="tasks view">
<?php 
  $requestedDateTime=new DateTime($task['Task']['created']);

	echo "<h2>".__('Task')."</h2>";
	echo "<dl>";
    echo "<dt>".__('Fecha Solicitada')."</dt>";
    echo "<dd>".$requestedDateTime->format('d/m/y H:i')."</dd>";
		echo "<dt>".__('Requesting User')."</dt>";
		echo "<dd>".$this->Html->link($task['RequestingUser']['username'], array('controller' => 'users', 'action' => 'view', $task['RequestingUser']['id']))."</dd>";
		echo "<dt>".__('Task Type')."</dt>";
		echo "<dd>".$this->Html->link($task['TaskType']['name'], array('controller' => 'task_types', 'action' => 'view', $task['TaskType']['id']))."</dd>";
		echo "<dt>".__('Requirements')."</dt>";
		echo "<dd>".h($task['Task']['requirements'])."</dd>";
		echo "<dt>".__('Acting User')."</dt>";
    if (!empty($task['ReceivingUser']['id'])){
      echo "<dd>".$this->Html->link($task['ActingUser']['username'], array('controller' => 'users', 'action' => 'view', $task['ActingUser']['id']))."</dd>";
    }
    else {
      echo "<dd>-</dd>";
    }
		echo "<dt>".__('Implementation')."</dt>";
    if (!empty($task['Task']['implementation'])){
      echo "<dd>".h($task['Task']['implementation'])."</dd>";
    }
    else {
      echo "<dd>-</dd>";
    }
		echo "<dt>".__('Date Executed')."</dt>";
    if (!empty($task['Task']['date_executed'])){
      $executedDateTime=new DateTime($task['Task']['date_executed']);
      echo "<dd>".$executedDateTime->format('d/m/y H:i')."</dd>";
    }
    else {
      echo "<dd>-</dd>";
    }
		echo "<dt>".__('Receiving User')."</dt>";
    
    if (!empty($task['ReceivingUser']['username'])){
		echo "<dd>".$this->Html->link($task['ReceivingUser']['username'], array('controller' => 'users', 'action' => 'view', $task['ReceivingUser']['id']))."</dd>";
    }
    else {
      echo "<dd>-</dd>";
    }
		//echo "<dt>".__('Hours Estimated')."</dt>";
		//echo "<dd>".h($task['Task']['hours_estimated'])."</dd>";
		//echo "<dt>".__('Remarks')."</dt>";
		//echo "<dd>".h($task['Task']['remarks'])."</dd>";
		//echo "<dt>".__('Priority')."</dt>";
		//echo "<dd>".h($task['Task']['priority'])."</dd>";
		//echo "<dt>".__('Bool Active')."</dt>";
		//echo "<dd>".h($task['Task']['bool_active'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
    if ($bool_edit_permission){
      echo "<li>".$this->Html->link(__('Edit Task'), array('action' => 'edit', $task['Task']['id']))."</li>";
      echo "<br>";
    }
    if ($bool_delete_permission){
      echo "<li>".$this->Form->postLink(__('Delete Task'), array('action' => 'delete', $task['Task']['id']), array(), __('Está seguor que quiere eliminar la tarea %s?', $task['Task']['requirements']))."</li>";
      echo "<br>";
    }
		echo "<li>".$this->Html->link(__('List Tasks'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Task'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Task Types'), array('controller' => 'task_types', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Task Type'), array('controller' => 'task_types', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
