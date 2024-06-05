<div class="operationLocations view">
<?php 
	echo "<h2>".__('Operation Location')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($operationLocation['OperationLocation']['name'])."</dd>";
		echo "<dt>".__('Description')."</dt>";
		if (!empty($operationLocation['OperationLocation']['description'])){
			echo "<dd>".h($operationLocation['OperationLocation']['description'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Operation Location'), array('action' => 'edit', $operationLocation['OperationLocation']['id']))."</li>";
			echo "<br/>";
		}
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Eliminar'), array('action' => 'delete', $operationLocation['OperationLocation']['id']), array(), __('Está seguro que quiere eliminar lugar de operación %s?', $operationLocation['OperationLocation']['name']))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Operation Locations'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Operation Location'), array('action' => 'add'))."</li>";
		echo "<br/>";
	echo "</ul>";
?> 
</div>
