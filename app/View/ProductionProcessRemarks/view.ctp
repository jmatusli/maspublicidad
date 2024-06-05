<div class="productionProcessRemarks view">
<?php 
	echo "<h2>".__('Production Process Remark')."</h2>";
	echo "<dl>";
		echo "<dt>".__('User')."</dt>";
		echo "<dd>".$this->Html->link($productionProcessRemark['User']['username'], array('controller' => 'users', 'action' => 'view', $productionProcessRemark['User']['id']))."</dd>";
		echo "<dt>".__('Production Process')."</dt>";
		echo "<dd>".$this->Html->link($productionProcessRemark['ProductionProcess']['id'], array('controller' => 'production_processes', 'action' => 'view', $productionProcessRemark['ProductionProcess']['id']))."</dd>";
		echo "<dt>".__('Remark Datetime')."</dt>";
		echo "<dd>".h($productionProcessRemark['ProductionProcessRemark']['remark_datetime'])."</dd>";
		echo "<dt>".__('Remark Text')."</dt>";
		echo "<dd>".h($productionProcessRemark['ProductionProcessRemark']['remark_text'])."</dd>";
		echo "<dt>".__('Action Type')."</dt>";
		echo "<dd>".$this->Html->link($productionProcessRemark['ActionType']['name'], array('controller' => 'action_types', 'action' => 'view', $productionProcessRemark['ActionType']['id']))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Production Process Remark'), array('action' => 'edit', $productionProcessRemark['ProductionProcessRemark']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Production Process Remark'), array('action' => 'delete', $productionProcessRemark['ProductionProcessRemark']['id']), array(), __('Are you sure you want to delete # %s?', $productionProcessRemark['ProductionProcessRemark']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Production Process Remarks'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Process Remark'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Production Processes'), array('controller' => 'production_processes', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Process'), array('controller' => 'production_processes', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Action Types'), array('controller' => 'action_types', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Action Type'), array('controller' => 'action_types', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
