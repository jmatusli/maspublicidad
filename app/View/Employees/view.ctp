<div class="employees view">
<?php
	echo "<h2>".__('Employee')." ".$employee['Employee']['first_name']." ".$employee['Employee']['last_name']."</h2>";
  echo "<div class='container-fluid'>";
    echo "<div class='rows'>";
      echo "<div class='col-md-6'>";
        echo "<dl>";
          echo "<dt>".__('First Name')."</dt>";
          echo "<dd>".$employee['Employee']['first_name']."</dd>";
          echo "<dt>".__('Last Name')."</dt>";
          echo "<dd>".$employee['Employee']['last_name']."</dd>";
          $startingDate= new DateTime($employee['Employee']['starting_date']);
          $endingDate= new DateTime($employee['Employee']['ending_date']);
          echo "<dt>".__('Starting Date')."</dt>";
          echo "<dd>".$startingDate->format('d-m-Y')."</dd>";
          echo "<dt>".__('Ending Date')."</dt>";
          echo "<dd>".$endingDate->format('d-m-Y')."</dd>";
          echo "<dt>".__('Position')."</dt>";
          if (!empty($employee['Employee']['position'])){
            echo "<dd>".$employee['Employee']['position']."</dd>";
          }
          else {
            echo "<dd>-</dd>";
          }
          echo "<dt>".__('Company')."</dt>";
          if (!empty($employee['Company']['name'])){
            echo "<dd>".$employee['Company']['name']."</dd>";
          }
          else {
            echo "<dd>-</dd>";
          }
        echo "</dl>";
        echo "<h4>Seleccione período para ver vacaciones de este empleado</h4>";
          echo $this->Form->create('Report'); 
            echo "<fieldset>";
              echo $this->Form->input('Report.startdate',['type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2010,'maxYear'=>date('Y')]);
              echo $this->Form->input('Report.enddate',['type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2010,'maxYear'=>date('Y')]);
            
            echo "</fieldset>";
            echo "<button id='previousyear' class='yearswitcher'>Año Previo</button>";
            echo "<button id='nextyear' class='yearswitcher'>Año Siguiente</button>";
            echo "<br/>";
          echo $this->Form->end(__('Refresh')); 
          echo $this->Html->link(__('Hoja de Vacaciones'), array('action' => 'verPdfHojaVacaciones','ext'=>'pdf',$startDate,$endDate,$employee['Employee']['id'],$filename),array( 'class' => 'btn btn-primary','target'=>'blank')); 
      echo "</div>";
      echo "<div class='col-md-6'>";
        $earnedTotal=0;
        $takenTotal=0;
        $saldoTotal=0;
      
        $tableHead="<thead>";
          $tableHead.="<tr>";
            $tableHead.="<th class='centered'>Año</th>";
            $tableHead.="<th class='centered'>Acumulado</th>";
            $tableHead.="<th class='centered'>Tomado</th>";
            $tableHead.="<th class='centered'>Saldo</th>";
          $tableHead.="</tr>";
        $tableHead.="</thead>";
        $tableBody="";
        foreach ($yearArray as $year){
          $earnedTotal+=$year['earned'];
          $takenTotal+=$year['taken'];
          $saldoTotal+=$year['earned'];
          $saldoTotal-=$year['taken'];
        
          $tableBody.="<tr>";
            $tableBody.="<td class='centered'>".$year['year']."</td>";
            $tableBody.="<td class='centered'>".$year['earned']."</td>";
            $tableBody.="<td class='centered'>".$year['taken']."</td>";
            $tableBody.="<td class='centered'>".($year['earned']-$year['taken'])."</td>";
          $tableBody.="</tr>";
        }
        $totalRow="";
        $totalRow.="<tr class='totalrow'>";
          $totalRow.="<td>Total</td>";
          $totalRow.="<td class='centered'>".$earnedTotal."</td>";
          $totalRow.="<td class='centered'>".$takenTotal."</td>";
          $totalRow.="<td class='centered'>".$saldoTotal."</td>";
        $totalRow.="</tr>";
        $tableBody="<tbody>".$totalRow.$tableBody.$totalRow."</tbody>";
        echo "<table>".$tableHead.$tableBody."</table>";
        echo "<div class='images'>";
          if (!empty($employee['Employee']['url_image'])){
            $url=$employee['Employee']['url_image'];
            echo "<img src='".$this->Html->url('/').$url."' alt='Employee' class='resize'></img>";
          }
        echo "</div>";
      echo "</div>";
    echo "</div>";
  echo "</div>";  
	
?>
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Employee'), array('action' => 'edit', $employee['Employee']['id']))."</li>";
		}
		echo "<li>".$this->Html->link(__('List Employees'), array('action' => 'index'))."</li>";
		if ($bool_add_permission){
			echo "<li>".$this->Html->link(__('New Employee'), array('action' => 'add'))."</li>";
		}
		if ($bool_employeeholiday_index_permission){
			echo "<br/>";
			echo "<li>".$this->Html->link(__('List Employee Holidays'), array('controller' => 'employee_holidays', 'action' => 'index'))." </li>";
		}
		if ($bool_employeeholiday_add_permission){
			//echo "<li>".$this->Html->link(__('New Employee Holiday'), array('controller' => 'employee_holidays', 'action' => 'add'))." </li>";
		}
	echo "</ul>";
?>
</div>
<div class="related">
<?php 	
	if (!empty($employee['EmployeeHoliday'])){
		echo "<h3>".__('Días de Vacaciones de ')." ".$employee['Employee']['first_name']." ".$employee['Employee']['last_name']."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Holiday Date')."</th>";
					echo "<th>".__('Days Taken')."</th>";
					echo "<th>".__('Holiday Type')."</th>";
					echo "<th class='actions'>".__('Actions')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			$daysTaken=0;
			foreach ($employee['EmployeeHoliday'] as $employeeHoliday){
				$daysTaken+=$employeeHoliday['days_taken'];
				echo "<tr>";
          $employeeHolidayDateTime=new DateTime($employeeHoliday['holiday_date']);
					echo "<td>".$employeeHolidayDateTime->format('d-m-y')."</td>";
					echo "<td>".$employeeHoliday['days_taken']."</td>";
					echo "<td>".$employeeHoliday['HolidayType']['name']."</td>";
					echo "<td class='actions'>";
						echo $this->Html->link(__('View'), array('controller' => 'employee_holidays', 'action' => 'view', $employeeHoliday['id'])); 
						echo $this->Html->link(__('Edit'), array('controller' => 'employee_holidays', 'action' => 'edit', $employeeHoliday['id'])); 
						if ($userrole==ROLE_ADMIN){
							echo $this->Form->postLink(__('Delete'), array('controller' => 'employee_holidays', 'action' => 'delete', $employeeHoliday['id']), array(), __('Are you sure you want to delete # %s?', $employeeHoliday['id'])); 
						}
					echo "</td>";
				echo "</tr>";
			}
				echo "<tr class='totalrow'>";
					echo "<td>Total</td>";
					echo "<td>".$daysTaken."</td>";
					echo "<td></td>";
					echo "<td></td>";
				echo "</tr>";
			echo "</tbody>";
		echo "</table>";
	}
?>
	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Employee Holiday'), array('controller' => 'employee_holidays', 'action' => 'add')); ?> </li>
		</ul>
	</div>
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