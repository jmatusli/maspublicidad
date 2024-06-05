<div class="ActionTypes view">
<?php 
	echo "<h2>".__('Action Type')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($actionType['ActionType']['name'])."</dd>";
		echo "<dt>".__('Description')."</dt>";
		if (!empty($actionType['ActionType']['description'])){
			echo "<dd>".h($actionType['ActionType']['description'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('List Order')."</dt>";
		echo "<dd>".h($actionType['ActionType']['list_order'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Action Type'), array('action' => 'edit', $actionType['ActionType']['id']))."</li>";
			echo "<br/>";
		}
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete Action Type'), array('action' => 'delete', $actionType['ActionType']['id']), array(), __('Está seguro que quiere eliminar el tipo de seguimiento %s?', $actionType['ActionType']['name']))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Action Types'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Action Type'), array('action' => 'add'))."</li>";
		echo "<br/>";
	echo "</ul>";
?> 
</div>