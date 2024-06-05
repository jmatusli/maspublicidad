<div class="inventoryProductLines index">
<?php 
	echo "<h2>".__('Inventory Product Lines')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate));
			echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate));
		echo "</fieldset>";
		echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
		echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardar'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('New Inventory Product Line'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_department_index_permission){
			echo "<li>".$this->Html->link(__('List Departments'), array('controller' => 'departments', 'action' => 'index'))."</li>";
		}
		if ($bool_department_add_permission){
			echo "<li>".$this->Html->link(__('New Department'), array('controller' => 'departments', 'action' => 'add'))."</li>";
		}
		if ($bool_inventoryproduct_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Products'), array('controller' => 'inventory_products', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryproduct_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Product'), array('controller' => 'inventory_products', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
<div>
<?php
	$pageHeader="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('name')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('description')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('department_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('bool_promotion')."</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader="<thead>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('name')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('description')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('department_id')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('bool_promotion')."</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	foreach ($inventoryProductLines as $inventoryProductLine){ 
		$pageRow="";
		$pageRow.="<td>".h($inventoryProductLine['InventoryProductLine']['name'])."</td>";
		$pageRow.="<td>".h($inventoryProductLine['InventoryProductLine']['description'])."</td>";
		$pageRow.="<td>".$this->Html->link($inventoryProductLine['Department']['name'], array('controller' => 'inventory_departments', 'action' => 'view', $inventoryProductLine['Department']['id']))."</td>";
		$pageRow.="<td>".($inventoryProductLine['InventoryProductLine']['bool_promotion']?__("Yes"):__("No"))."</td>";
		
		$excelBody.="<tr>".$pageRow."</tr>";

		$pageRow.="<td class='actions'>";
			$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $inventoryProductLine['InventoryProductLine']['id']));
			$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $inventoryProductLine['InventoryProductLine']['id']));
			//$pageRow.=->postLink(__('Delete'), array('action' => 'delete', $inventoryProductLine['InventoryProductLine']['id']), array(), __('Are you sure you want to delete # %s?', $inventoryProductLine['InventoryProductLine']['id']));
		$pageRow.="</td>";

		$pageBody.="<tr>".$pageRow."</tr>";
	}

	$pageTotalRow="";
	$pageTotalRow.="<tr class=\'totalrow\'>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
	$pageTotalRow.="</tr>";

	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	$table_id="";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo $pageOutput;
	$excelOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	$_SESSION['resumen'] = $excelOutput;
?>
</div>