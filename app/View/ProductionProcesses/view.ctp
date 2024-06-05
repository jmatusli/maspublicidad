<div class="productionProcesses view fullwidth">
<?php 
	echo "<h2>".__('Production Process')."</h2>";
	$productionProcessDateTime=new DateTime($productionProcess['ProductionProcess']['production_process_date']);
	echo "<div class='container-fluid'>";
		echo "<div class='row'>";
				echo "<div class='col-md-6'>";
					echo "<dl>";
						echo "<dt>".__('Production Process Date')."</dt>";
						echo "<dd>".$productionProcessDateTime->format('d-m-Y')."</dd>";
						echo "<dt>".__('Production Process Code')."</dt>";
						echo "<dd>".h($productionProcess['ProductionProcess']['production_process_code'])."</dd>";
						echo "<dt>".__('Department')."</dt>";
						echo "<dd>".$this->Html->link($productionProcess['Department']['name'], array('controller' => 'departments', 'action' => 'view', $productionProcess['Department']['id']))."</dd>";
						echo "<dt>".__('Bool Annulled')."</dt>";
						echo "<dd>".($productionProcess['ProductionProcess']['bool_annulled']?__('Yes'):__('No'))."</dd>";
					echo "</dl>";
				echo "</div>";
			echo "<div class='col-md-3 actions'>";
				echo "<h3>".__('Actions')."</h3>";
				echo "<ul>";
					echo "<li>".$this->Html->link(__('Guardar como pdf'), array('action' => 'viewPdf','ext'=>'pdf', $productionProcess['ProductionProcess']['id'],$filename),array('target'=>'_blank'))."</li>";
					if ($bool_edit_permission){
						echo "<li>".$this->Html->link(__('Edit Production Process'), array('action' => 'edit', $productionProcess['ProductionProcess']['id']))."</li>";
						echo "<br/>";
					}
					if ($bool_delete_permission){
						echo "<li>".$this->Form->postLink(__('Delete Production Process'), array('action' => 'delete', $productionProcess['ProductionProcess']['id']), array(), __('Está seguro que quiere eliminar el proceso de producción # %s?', $productionProcess['ProductionProcess']['production_process_code']))."</li>";
					}
					if ($bool_annul_permission){
						echo "<li>".$this->Form->postLink(__('Anular Proceso de Producción'), array('action' => 'annul', $productionProcess['ProductionProcess']['id']), array(), __('Está seguro que quiere anular el proceso de producción # %s?', $productionProcess['ProductionProcess']['production_process_code']))."</li>";
					}
					if ($bool_delete_permission||$bool_annul_permission){
						echo "<br/>";
					}
					echo "<li>".$this->Html->link(__('List Production Processes'), array('action' => 'index'))."</li>";
					echo "<li>".$this->Html->link(__('New Production Process'), array('action' => 'add'))."</li>";
					echo "<br/>";
		
					if ($bool_department_index_permission){
						echo "<li>".$this->Html->link(__('List Departments'), array('controller' => 'departments', 'action' => 'index'))." </li>";
					}
					if ($bool_department_add_permission){
						echo "<li>".$this->Html->link(__('New Department'), array('controller' => 'departments', 'action' => 'add'))." </li>";
					}
				echo "</ul>";			
			echo "</div>";				
		echo "</div>";				
	echo "</div>";				
?> 
</div>
<div class="related">
<?php 
	if (!empty($productionProcess['ProductionProcessProduct'])){
		echo "<h3>".__('Productos en el el Proceso de Producción')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Product')."</th>";
				echo "<th>".__('Product Description')."</th>";
				echo "<th>".__('Product Quantity')."</th>";
				echo "<th>".__('Operator')."</th>";
				echo "<th>".__('Machine')."</th>";
				echo "<th>".__('Operation Location')."</th>";
			echo "</tr>";
		foreach ($productionProcess['ProductionProcessProduct'] as $productionProcessProduct){ 
			echo "<tr>";
				echo "<td>".$productionProcessProduct['Product']['name']."</td>";
				echo "<td>".$productionProcessProduct['product_description']."</td>";
				echo "<td>".$productionProcessProduct['product_quantity']."</td>";
				if (!empty($productionProcessProduct['Operator'])){
					echo "<td>".$productionProcessProduct['Operator']['first_name']." ".$productionProcessProduct['Operator']['last_name']."</td>";
				}
				else {
					echo "<td>-</td>";
				}
				if (!empty($productionProcessProduct['Machine'])){
					echo "<td>".$productionProcessProduct['Machine']['name']."</td>";
				}
				else {
					echo "<td>-</td>";
				}
				echo "<td>";
				foreach ($productionProcessProduct['ProductionProcessProductOperationLocation'] as $productOperationLocation){
					if (!empty($productOperationLocation['OperationLocation'])){
						//pr($productOperationLocation['OperationLocation']);
						echo $productOperationLocation['OperationLocation']['name']."<br/>";
					}
				}
				echo "</td>";
				
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
<div class="related">
<?php 
	if (!empty($productionProcess['ProductionProcessRemark'])){
		echo "<h3>".__('Remarcas para este Proceso de Producción')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Vendedor')."</th>";
				echo "<th>".__('Remark Datetime')."</th>";
				echo "<th>".__('Remark Text')."</th>";
				echo "<th>".__('Action Type')."</th>";
				//echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($productionProcess['ProductionProcessRemark'] as $productionProcessRemark){ 
			echo "<tr>";
				$remarkDateTime=new Datetime($productionProcessRemark['remark_datetime']);
				echo "<td>".$productionProcessRemark['User']['first_name']."_".$productionProcessRemark['User']['last_name']."</td>";
				echo "<td>".$remarkDateTime->format('d-m-Y')."</td>";
				echo "<td>".$productionProcessRemark['remark_text']."</td>";
				echo "<td>".$productionProcessRemark['ActionType']['name']."</td>";
				//echo "<td class='actions'>";
				//	echo $this->Html->link(__('View'), array('controller' => 'production_process_remarks', 'action' => 'view', $productionProcessRemark['id']));
				//	echo $this->Html->link(__('Edit'), array('controller' => 'production_process_remarks', 'action' => 'edit', $productionProcessRemark['id']));
				//echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
