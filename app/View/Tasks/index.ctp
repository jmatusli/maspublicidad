<script>
	function formatNumbers(){
		$("td.number span.amountright").each(function(){
			if (Math.abs(parseFloat($(this).text()))<0.001){
				$(this).text("0");
			}
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,2,'.',',');
		});
	}
	
	function formatCSCurrencies(){
		$("td.CScurrency").each(function(){
			
			if (parseFloat($(this).find('.amountright').text())<0){
				$(this).find('.amountright').prepend("-");
			}
			$(this).find('.amountright').number(true,2);
			$(this).find('.currency').text("C$");
		});
	}
	
	function formatUSDCurrencies(){
		$("td.USDcurrency").each(function(){
			
			if (parseFloat($(this).find('.amountright').text())<0){
				$(this).find('.amountright').prepend("-");
			}
			$(this).find('.amountright').number(true,2);
			$(this).find('.currency').text("US$");
		});
	}
	
	$(document).ready(function(){
		formatNumbers();
		formatCSCurrencies();
		formatUSDCurrencies();
	});

</script>
<div class="tasks index">
<?php 
	echo "<h2>".__('Tasks')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate));
			echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate));
		echo "</fieldset>";
		echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
		echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
    echo "<br>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumen'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('New Task'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index'))."</li>";
    echo "<br>";
		echo "<li>".$this->Html->link(__('List Task Types'), array('controller' => 'task_types', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Task Type'), array('controller' => 'task_types', 'action' => 'add'))."</li>";
	echo "</ul>";
?>
</div>
<div>
<?php
	$pageHeader="<thead>";
		$pageHeader.="<tr>";
      $pageHeader.="<th>".$this->Paginator->sort('created','Fecha y hora')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('requesting_user_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('task_type_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('requirements')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('acting_user_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('implementation')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('date_executed')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('receiving_user_id')."</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader="<thead>";
		$excelHeader.="<tr>";
      $excelHeader.="<th>".$this->Paginator->sort('created','Fecha y hora')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('requesting_user_id')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('task_type_id')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('requirements')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('acting_user_id')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('implementation')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('date_executed')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('receiving_user_id')."</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('hours_estimated')."</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('remarks')."</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('priority')."</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('bool_active')."</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	foreach ($tasks as $task){ 
    $requestedDateTime=new DateTime($task['Task']['created']);
    if (!empty($task['Task']['date_executed'])){
      $executedDateTime=new DateTime($task['Task']['date_executed']);
    }
		$pageRow="";
    $pageRow.="<td>".$requestedDateTime->format('d/m/Y H:i')."</td>";
		$pageRow.="<td>".$this->Html->link($task['RequestingUser']['username'], array('controller' => 'users', 'action' => 'view', $task['RequestingUser']['id']))."</td>";
		$pageRow.="<td>".$this->Html->link($task['TaskType']['name'], array('controller' => 'task_types', 'action' => 'view', $task['TaskType']['id']))."</td>";
		$pageRow.="<td>".$this->Html->link($task['Task']['requirements'],['action'=>'view',$task['Task']['id']])."</td>";
		$pageRow.="<td>".$this->Html->link($task['ActingUser']['username'], array('controller' => 'users', 'action' => 'view', $task['ActingUser']['id']))."</td>";
		$pageRow.="<td>".h($task['Task']['implementation'])."</td>";
		$pageRow.="<td>".(!empty($task['Task']['date_executed'])?$executedDateTime->format('d/m/Y H:i'):"")."</td>";
		$pageRow.="<td>".$this->Html->link($task['ReceivingUser']['username'], array('controller' => 'users', 'action' => 'view', $task['ReceivingUser']['id']))."</td>";
		//$pageRow.="<td>".h($task['Task']['hours_estimated'])."</td>";
		//$pageRow.="<td>".h($task['Task']['remarks'])."</td>";
		//$pageRow.="<td>".h($task['Task']['priority'])."</td>";
		//$pageRow.="<td>".h($task['Task']['bool_active'])."</td>";

			$excelBody.="<tr>".$pageRow."</tr>";

			$pageRow.="<td class='actions'>";
				if ($bool_edit_permission){
          $pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $task['Task']['id']));
        }
				//$pageRow.=->postLink(__('Delete'), array('action' => 'delete', $task['Task']['id']), array(), __('Are you sure you want to delete # %s?', $task['Task']['id']));
			$pageRow.="</td>";

		$pageBody.="<tr>".$pageRow."</tr>";
	}

	$pageTotalRow="";
	/*
  $pageTotalRow.="<tr class='totalrow'>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
	$pageTotalRow.="</tr>";
  */
	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	$table_id="tareas";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo $pageOutput;
	$excelOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	$_SESSION['resumen'] = $excelOutput;
?>
</div>