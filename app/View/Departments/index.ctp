<div class="departments index">
<?php 
	echo "<h2>".__('Departments')."</h2>";
	
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardar'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('New Department'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_inventoryproductline_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Product Lines'), array('controller' => 'inventory_product_lines', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryproductline_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Product Line'), array('controller' => 'inventory_product_lines', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
<div>
<?php
	$pageHeader="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('name')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('abbreviation')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('description')."</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader="<thead>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('name')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('abbreviation')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('description')."</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	foreach ($departments as $department){ 
		$pageRow="";
		$pageRow.="<td>".h($department['Department']['name'])."</td>";
		$pageRow.="<td>".h($department['Department']['abbreviation'])."</td>";
		$pageRow.="<td>".h($department['Department']['description'])."</td>";
		
		$excelBody.="<tr>".$pageRow."</tr>";

		$pageRow.="<td class='actions'>";
			$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $department['Department']['id']));
			if ($bool_edit_permission){
				$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $department['Department']['id']));
			}
			if ($bool_delete_permission){
				$pageRow.=$this->Form->postLink(__('Delete'), array('action' => 'delete', $department['Department']['id']), array(), __('Está seguro que quiere eliminar el departamento %s?', $department['Department']['name']));
			}
		$pageRow.="</td>";

		$pageBody.="<tr>".$pageRow."</tr>";
	}

	$pageTotalRow="";
	$pageTotalRow.="<tr class='totalrow'>";
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
