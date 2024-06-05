<div class="employeeHolidays view">
<h2><?php echo __('Employee Holiday'); ?></h2>
	<dl>
		<dt><?php echo __('Employee'); ?></dt>
		<dd>
			<?php echo $this->Html->link($employeeHoliday['Employee']['fullname'], array('controller' => 'employees', 'action' => 'view', $employeeHoliday['Employee']['id'])); ?>
			&nbsp;
		</dd>
	<?php 
		$holidayDate= new DateTime($employeeHoliday['EmployeeHoliday']['holiday_date']);
		echo "<dt>".__('Holiday Date')."</dt>";
		echo "<dd>".$holidayDate->format('d-m-Y')."</dd>";
	?>
		<dt><?php echo __('Days Taken'); ?></dt>
		<dd>
			<?php echo h($employeeHoliday['EmployeeHoliday']['days_taken']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Holiday Type'); ?></dt>
		<dd>
			<?php echo $this->Html->link($employeeHoliday['HolidayType']['name'], array('controller' => 'employees', 'action' => 'view', $employeeHoliday['HolidayType']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Observation'); ?></dt>
		<dd>
			<?php echo h($employeeHoliday['EmployeeHoliday']['observation']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Employee Holiday'), array('action' => 'edit', $employeeHoliday['EmployeeHoliday']['id']))."</li>";
			echo "<br/>";
		}
		if ($userrole==ROLE_ADMIN){
			echo "<li>".$this->Form->postLink(__('Delete Employee Holiday'), array('action' => 'delete', $employeeHoliday['EmployeeHoliday']['id']), array(), __('Are you sure you want to delete # %s?', $employeeHoliday['EmployeeHoliday']['id']))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Employee Holidays'), array('action' => 'index'))."</li>";
		if ($bool_add_permission){
			echo "<li>".$this->Html->link(__('New Employee Holiday'), array('action' => 'add'))."</li>";
			echo "<li>".$this->Html->link(__('Registrar Día Feriado'), array('action' => 'registrarFeriado'))."</li>";
		}
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
<script>
	function formatNumbers(){
		$("td.number").each(function(){
			$(this).number(true,0);
		});
	}
	
	function formatCurrencies(){
		$("td.currency span").each(function(){
			$(this).number(true,4);
			$(this).parent().append(" C$");
		});
	}
	
	function formatPercentages(){
		$("td.percentage span").each(function(){
			$(this).number(true,2);
			$(this).parent().append(" %");
		});
	}
	
	$(document).ready(function(){
		formatNumbers();
		formatCurrencies();
		formatPercentages();
	});

	$('#previousmonth').click(function(event){
		var thisMonth = parseInt($('#ReportStartdateMonth').val());
		var previousMonth= (thisMonth-1)%12;
		var previousYear=parseInt($('#ReportStartdateYear').val());
		if (previousMonth==0){
			previousMonth=12;
			previousYear-=1;
		}
		if (previousMonth<10){
			previousMonth="0"+previousMonth;
		}
		$('#ReportStartdateDay').val('1');
		$('#ReportStartdateMonth').val(previousMonth);
		$('#ReportStartdateYear').val(previousYear);
		var daysInPreviousMonth=daysInMonth(previousMonth,previousYear);
		$('#ReportEnddateDay').val(daysInPreviousMonth);
		$('#ReportEnddateMonth').val(previousMonth);
		$('#ReportEnddateYear').val(previousYear);
	});
	
	$('#nextmonth').click(function(event){
		var thisMonth = parseInt($('#ReportStartdateMonth').val());
		var nextMonth= (thisMonth+1)%12;
		var nextYear=parseInt($('#ReportStartdateYear').val());
		if (nextMonth==0){
			nextMonth=12;
		}
		if (nextMonth==1){
			nextYear+=1;
		}
		if (nextMonth<10){
			nextMonth="0"+nextMonth;
		}
		$('#ReportStartdateDay').val('1');
		$('#ReportStartdateMonth').val(nextMonth);
		$('#ReportStartdateYear').val(nextYear);
		var daysInNextMonth=daysInMonth(nextMonth,nextYear);
		$('#ReportEnddateDay').val(daysInNextMonth);
		$('#ReportEnddateMonth').val(nextMonth);
		$('#ReportEnddateYear').val(nextYear);
	});
	
	function daysInMonth(month,year) {
		return new Date(year, month, 0).getDate();
	}
</script>