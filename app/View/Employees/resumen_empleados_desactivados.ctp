<div class="employees index">
<?php 
	echo "<h2>Empleados Desactivados</h2>";
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
				echo "<th>". $this->Paginator->sort('first_name')."</th>";
				echo "<th>". $this->Paginator->sort('last_name')."</th>";
				echo "<th>". $this->Paginator->sort('starting_date')."</th>";
				echo "<th>". $this->Paginator->sort('company_id')."</th>";
				echo "<th>".$this->Paginator->sort('ending_date')."</th>";
				echo "<th class='centered'>Días Acumulados</th>";
				echo "<th class='centered'>Días Descansados</th>";
				echo "<th class='centered'>Saldo</th>";
				echo "<th class='actions'>". __('Actions')."</th>";
			echo "</tr>";
		echo "</thead>";
		echo "<tbody>";

		foreach ($employees as $employee){
			$startingDate= new DateTime($employee['Employee']['starting_date']);
			$endingDate= new DateTime($employee['Employee']['ending_date']);
			
			echo "<tr class='italic'>";
				echo "<td>".$this->Html->link($employee['Employee']['first_name'], array('action' => 'view', $employee['Employee']['id']))."&nbsp;</td>";
				echo "<td>".$this->Html->link($employee['Employee']['last_name'], array('action' => 'view', $employee['Employee']['id']))." (Desactivado)</td>";
				echo "<td>".$startingDate->format('d-m-Y')."&nbsp;</td>";
				echo "<td>".h($employee['Company']['name'])."&nbsp;</td>";
				echo "<td>".$endingDate->format('d-m-Y')."&nbsp;</td>";
				echo "<td class='centered'>".number_format($employee['Employee']['holidays_earned'],1,".",",")."&nbsp;</td>";
				echo "<td class='centered'>".number_format($employee['Employee']['holidays_taken'],1,".",",")."&nbsp;</td>";
				echo "<td class='centered'>".number_format(($employee['Employee']['holidays_earned']-$employee['Employee']['holidays_taken']),1,".",",")."&nbsp;</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('Edit'), array('action' => 'edit', $employee['Employee']['id'])); 
					//echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $employee['Employee']['id']), array(), __('Are you sure you want to delete # %s?', $employee['Employee']['id'])); 
				echo "</td>";
			echo "</tr>";		
		}
		echo "</tbody>";
	echo "</table>";
	//$excelOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	//$_SESSION['resumen'] = $excelOutput;
?>
</div>
<div class='actions'>
<?php
	echo "<h3>". __('Actions')."</h3>";
	echo "<ul>";
		echo "<li>". $this->Html->link(__('New Employee'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>". $this->Html->link(__('Empleados Activos'), array('action' => 'index'))."</li>";
		echo "<br/>";
		echo "<li>". $this->Html->link(__('List Employee Holidays'), array('controller' => 'employee_holidays', 'action' => 'index'))." </li>";
		echo "<li>". $this->Html->link(__('New Employee Holiday'), array('controller' => 'employee_holidays', 'action' => 'add'))." </li>";
	echo "</ul>";
?>
</div>
