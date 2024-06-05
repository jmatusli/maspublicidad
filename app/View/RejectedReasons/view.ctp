<div class="RejectedReasons view">
<?php 
	echo "<h2>".__('Rejected Reason')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($rejectedReason['RejectedReason']['name'])."</dd>";
		echo "<dt>".__('Description')."</dt>";
		if (!empty($rejectedReason['RejectedReason']['description'])){
			echo "<dd>".h($rejectedReason['RejectedReason']['description'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('List Order')."</dt>";
		echo "<dd>".h($rejectedReason['RejectedReason']['list_order'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Rejected Reason'), array('action' => 'edit', $rejectedReason['RejectedReason']['id']))."</li>";
			echo "<br/>";
		}
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete Rejected Reason'), array('action' => 'delete', $rejectedReason['RejectedReason']['id']), array(), __('Está seguro que quiere eliminar la razón de caída %s?', $rejectedReason['RejectedReason']['name']))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Rejected Reasons'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Rejected Reason'), array('action' => 'add'))."</li>";
		echo "<br/>";
	echo "</ul>";
?> 
</div>