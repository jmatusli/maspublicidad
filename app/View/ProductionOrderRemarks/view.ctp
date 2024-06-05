<div class="productionOrderRemarks view">
<?php 
	echo "<h2>".__('Production Order Remark')."</h2>";
	echo "<dl>";
		echo "<dt>".__('User')."</dt>";
		echo "<dd>".$this->Html->link($productionOrderRemark['User']['username'], array('controller' => 'users', 'action' => 'view', $productionOrderRemark['User']['id']))."</dd>";
		echo "<dt>".__('Production Order')."</dt>";
		echo "<dd>".$this->Html->link($productionOrderRemark['ProductionOrder']['id'], array('controller' => 'production_orders', 'action' => 'view', $productionOrderRemark['ProductionOrder']['id']))."</dd>";
		echo "<dt>".__('Remark Datetime')."</dt>";
		echo "<dd>".h($productionOrderRemark['ProductionOrderRemark']['remark_datetime'])."</dd>";
		echo "<dt>".__('Remark Text')."</dt>";
		echo "<dd>".h($productionOrderRemark['ProductionOrderRemark']['remark_text'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Production Order Remark'), array('action' => 'edit', $productionOrderRemark['ProductionOrderRemark']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Production Order Remark'), array('action' => 'delete', $productionOrderRemark['ProductionOrderRemark']['id']), array(), __('Are you sure you want to delete # %s?', $productionOrderRemark['ProductionOrderRemark']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Production Order Remarks'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Order Remark'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Production Orders'), array('controller' => 'production_orders', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Production Order'), array('controller' => 'production_orders', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
