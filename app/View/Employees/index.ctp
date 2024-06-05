<div class="employees index">
<?php 	
	echo "<h2>".__('Employees')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			//if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
			echo $this->Form->input('Report.company_id',array('label'=>__('Company'),'default'=>$companyId,'empty'=>array('0'=>__('All Companies'))));
			//}
		echo "</fieldset>";
		echo "<br/>";
	echo $this->Form->end(__('Refresh'));
	//echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumen'), array( 'class' => 'btn btn-primary'));
	
	echo "<table cellpadding='0' cellspacing='0'>";
		echo "<thead>";
		echo "<tr>";
			echo "<th>".$this->Paginator->sort('first_name')."</th>";
			echo "<th>".$this->Paginator->sort('last_name')."</th>";
			echo "<th>".$this->Paginator->sort('position','Cargo')."</th>";
			echo "<th>".$this->Paginator->sort('company_id')."</th>";
			echo "<th>".$this->Paginator->sort('starting_date')."</th>";
			echo "<th>".$this->Paginator->sort('ending_date')."</th>";
			echo "<th class='centered'>Días Acumulados</th>";
			echo "<th class='centered'>Días Descansados</th>";
			echo "<th class='centered'>Saldo</th>";
			echo "<th class='actions'>".__('Actions')."</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		foreach ($employees as $employee){
			$startingDate= new DateTime($employee['Employee']['starting_date']);
			$endingDate= new DateTime($employee['Employee']['ending_date']);			
			echo "<tr>";
				echo "<td>".$this->Html->link($employee['Employee']['first_name'], array('action' => 'view', $employee['Employee']['id']))."</td>";
				echo "<td>".$this->Html->link($employee['Employee']['last_name'], array('action' => 'view', $employee['Employee']['id']))."</td>";
				echo "<td>".h($employee['Employee']['position'])."&nbsp;</td>";
				echo "<td>".h($employee['Company']['name'])."&nbsp;</td>";
				echo "<td>".$startingDate->format('d-m-Y')."&nbsp;</td>";
				echo "<td>".$endingDate->format('d-m-Y')."&nbsp;</td>";
				echo "<td class='centered number'><span class='amount'>".$employee['Employee']['holidays_earned']."</span></td>";
				echo "<td class='centered number'><span class='amount'>".$employee['Employee']['holidays_taken']."</span></td>";
				echo "<td class='centered number'><span class='amount'>".($employee['Employee']['holidays_earned']-$employee['Employee']['holidays_taken'])."</span></td>";
				echo "<td class='actions'>";
					if ($bool_edit_permission){
						echo $this->Html->link(__('Edit'), array('action' => 'edit', $employee['Employee']['id'])); 
					}
					if ($bool_delete_permission){
						//echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $employee['Employee']['id']), array(), __('Está seguro que quiere eliminar el empleado %s?', $employee['Employee']['first_name']." ".$employee['Employee']['last_name'])); 
					}
				echo "</td>";
			echo "</tr>";
		}
		echo "</tbody>";
	echo "</table>";
	//$excelOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	//$_SESSION['resumen'] = $excelOutput;
?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Employee'), array('action' => 'add')); ?></li>
		<br/>
		<li><?php echo $this->Html->link(__('Empleados Desactivados'), array('action' => 'resumenEmpleadosDesactivados')); ?></li>
		<br/>
		<li><?php echo $this->Html->link(__('List Employee Holidays'), array('controller' => 'employee_holidays', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Employee Holiday'), array('controller' => 'employee_holidays', 'action' => 'add')); ?> </li>
	</ul>
</div>
