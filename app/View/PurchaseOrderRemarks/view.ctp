<div class="purchaseOrderRemarks view">
<?php 
	echo "<h2>".__('Purchase Order Remark')."</h2>";
	echo "<dl>";
		echo "<dt>".__('User')."</dt>";
		echo "<dd>".$this->Html->link($purchaseOrderRemark['User']['username'], array('controller' => 'users', 'action' => 'view', $purchaseOrderRemark['User']['id']))."</dd>";
		echo "<dt>".__('Purchase Order')."</dt>";
		echo "<dd>".$this->Html->link($purchaseOrderRemark['PurchaseOrder']['id'], array('controller' => 'purchase_orders', 'action' => 'view', $purchaseOrderRemark['PurchaseOrder']['id']))."</dd>";
		echo "<dt>".__('Remark Datetime')."</dt>";
		echo "<dd>".h($purchaseOrderRemark['PurchaseOrderRemark']['remark_datetime'])."</dd>";
		echo "<dt>".__('Remark Text')."</dt>";
		echo "<dd>".h($purchaseOrderRemark['PurchaseOrderRemark']['remark_text'])."</dd>";
		echo "<dt>".__('Action Type')."</dt>";
		echo "<dd>".$this->Html->link($purchaseOrderRemark['ActionType']['name'], array('controller' => 'action_types', 'action' => 'view', $purchaseOrderRemark['ActionType']['id']))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Edit Purchase Order Remark'), array('action' => 'edit', $purchaseOrderRemark['PurchaseOrderRemark']['id']))."</li>";
		echo "<li>".$this->Form->postLink(__('Delete Purchase Order Remark'), array('action' => 'delete', $purchaseOrderRemark['PurchaseOrderRemark']['id']), array(), __('Are you sure you want to delete # %s?', $purchaseOrderRemark['PurchaseOrderRemark']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Purchase Order Remarks'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Purchase Order Remark'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Purchase Orders'), array('controller' => 'purchase_orders', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Purchase Order'), array('controller' => 'purchase_orders', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Action Types'), array('controller' => 'action_types', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Action Type'), array('controller' => 'action_types', 'action' => 'add'))."</li>";
	echo "</ul>";
?> 
</div>
