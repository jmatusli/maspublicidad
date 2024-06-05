<div class="employeeHolidays form">
<?php echo $this->Form->create('EmployeeHoliday'); ?>
	<fieldset>
		<legend><?php echo __('Registrar Días Feriados'); ?></legend>
	<?php
		echo $this->Form->input('holiday_date',array('dateFormat'=>'DMY','minYear'=>'2014','maxYear'=>'2025'));
		echo $this->Form->input('days_taken',array('default'=>'1'));
		echo $this->Form->input('holiday_type_id',array('default'=>HOLIDAY_TYPE_FERIADO));
		echo $this->Form->input('observation');
		$e=0;
		foreach ($employees as $key=>$value){
			echo $this->Form->Input('Employee.'.$e.'.employee_id.'.$key,array('type'=>'checkbox','label'=>$value));
			$e++;
		}
		
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('List Employee Holidays'), array('action' => 'index'))."</li>";
		if ($bool_employee_index_permission){
			echo "<br/>";
			echo "<li>".$this->Html->link(__('List Employees'), array('controller' => 'employees', 'action' => 'index'))." </li>";
		}
		if ($bool_employee_add_permission){
			echo "<li>".$this->Html->link(__('New Employee'), array('controller' => 'employees', 'action' => 'add'))." </li>";
		}
	echo "</ul>";
?>
</div>
