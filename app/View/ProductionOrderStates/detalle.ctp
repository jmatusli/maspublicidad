<div class="productionOrderStates view">
<?php 
	echo '<h1>'.__('Production Order State').' '.$productionOrderState['ProductionOrderState']['name'].'</h1>';
	echo '<dl>';
		echo '<dt>'.__('Name').'</dt>';
		echo '<dd>'.h($productionOrderState['ProductionOrderState']['name']).'</dd>';
    echo '<dt>'.__('List order').'</dt>';
		echo '<dd>'.h($productionOrderState['ProductionOrderState']['list_order']).'</dd>';
	echo '</dl>';
?> 
</div>
<div class="actions">
<?php 
	echo '<h2>'.__('Actions').'</h2>';
	echo '<ul>';
		if ($bool_edit_permission){
			echo '<li>'.$this->Html->link(__('Edit Production Order State'), ['action' => 'editar', $productionOrderState['ProductionOrderState']['id']]).'</li>';
	echo '<br/>';
		}
		echo '<li>'.$this->Html->link(__('List Production Order States'), ['action' => 'resumen']).'</li>';
		echo '<li>'.$this->Html->link(__('New Production Order State'), ['action' => 'crear']).'</li>';
	echo '</ul>';
?> 
</div>
<div class="related">
<?php 
/*
	if (!empty($productionOrderState['ProductionOrderDepartmentState'])){
		echo '<h3>'.__('Related Production Order Department States').'</h3>';
		echo '<table cellpadding="0" cellspacing="0">';
			echo '<tr>';
				echo '<th>'.__('User Id').'</th>';
				echo '<th>'.__('Production Order Id').'</th>';
				echo '<th>'.__('Production Order Department Id').'</th>';
				echo '<th>'.__('Department Id').'</th>';
				echo '<th>'.__('Production Order State Id').'</th>';
				echo '<th>'.__('State Datetime').'</th>';
			echo '</tr>';
		foreach ($productionOrderState['ProductionOrderDepartmentState'] as $productionOrderDepartmentState){ 
			echo '<tr>';
				echo '<td>'.$productionOrderDepartmentState['user_id'].'</td>';
				echo '<td>'.$productionOrderDepartmentState['production_order_id'].'</td>';
				echo '<td>'.$productionOrderDepartmentState['production_order_department_id'].'</td>';
				echo '<td>'.$productionOrderDepartmentState['department_id'].'</td>';
				echo '<td>'.$productionOrderDepartmentState['production_order_state_id'].'</td>';
				echo '<td>'.$productionOrderDepartmentState['state_datetime'].'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
?>
</div>
<div class="related">
<?php 
	if (!empty($productionOrderState['ProductionOrderProductDepartmentState'])){
		echo '<h3>'.__('Related Production Order Product Department States').'</h3>';
		echo '<table cellpadding="0" cellspacing="0">';
			echo '<tr>';
				echo '<th>'.__('User Id').'</th>';
				echo '<th>'.__('Production Order Id').'</th>';
				echo '<th>'.__('Production Order Product Id').'</th>';
				echo '<th>'.__('Production Order Product Department Id').'</th>';
				echo '<th>'.__('Product Id').'</th>';
				echo '<th>'.__('Department Id').'</th>';
				echo '<th>'.__('Production Order State Id').'</th>';
				echo '<th>'.__('State Datetime').'</th>';
			echo '</tr>';
		foreach ($productionOrderState['ProductionOrderProductDepartmentState'] as $productionOrderProductDepartmentState){ 
			echo '<tr>';
				echo '<td>'.$productionOrderProductDepartmentState['user_id'].'</td>';
				echo '<td>'.$productionOrderProductDepartmentState['production_order_id'].'</td>';
				echo '<td>'.$productionOrderProductDepartmentState['production_order_product_id'].'</td>';
				echo '<td>'.$productionOrderProductDepartmentState['production_order_product_department_id'].'</td>';
				echo '<td>'.$productionOrderProductDepartmentState['product_id'].'</td>';
				echo '<td>'.$productionOrderProductDepartmentState['department_id'].'</td>';
				echo '<td>'.$productionOrderProductDepartmentState['production_order_state_id'].'</td>';
				echo '<td>'.$productionOrderProductDepartmentState['state_datetime'].'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
*/  
?>
</div>
<link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet" type="text/css">
<div style="float:left;width:100%;">
<?php 
		if ($bool_delete_permission){
			echo $this->Form->postLink(__($this->Html->tag('i', '', ['class' => 'glyphicon glyphicon-fire']).' '.'Eliminar Estado de Producción'), ['action' => 'delete', $productionOrderState['ProductionOrderState']['id']], ['class' => 'btn btn-danger btn-sm','style'=>'text-decoration:none;','escape'=>false], __('Está seguro que quiere eliminar el estado de producción # %s?  PELIGRO, NO SE PUEDE DESHACER ESTA OPERACIÓN.  LOS DATOS DESPARECERÁN DE LA BASE DE DATOS!!!', $productionOrderState['ProductionOrderState']['name']));
	echo '<br/>';
		}
?>
</div>