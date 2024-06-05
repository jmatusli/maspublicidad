<div class="productionProcesses index">
<?php 
	echo "<h2>".__('Production Processes')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate));
			echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate));
		echo "</fieldset>";
		echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
		echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
	echo "<br/>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumen'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('New Production Process'), array('action' => 'add'))."</li>";
	echo "</ul>";
?>
</div>
<div>
<?php
	$pageHeader="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('production_process_date')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('production_process_code')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('department_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('bool_annulled')."</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader="<thead>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('production_process_date')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('production_process_code')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('department_id')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('bool_annulled')."</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	foreach ($productionProcesses as $productionProcess){ 
		$productionProcessDateTime=new DateTime($productionProcess['ProductionProcess']['production_process_date']);
		$pageRow="";
			$pageRow.="<td>".$productionProcessDateTime->format('d-m-Y')."</td>";
			$pageRow.="<td>".h($productionProcess['ProductionProcess']['production_process_code'])."</td>";
			$pageRow.="<td>".$this->Html->link($productionProcess['Department']['name'], array('controller' => 'departments', 'action' => 'view', $productionProcess['Department']['id']))."</td>";
			$pageRow.="<td>".($productionProcess['ProductionProcess']['bool_annulled']?__('Yes'):__('No'))."</td>";

		$excelBody.="<tr>".$pageRow."</tr>";
			$pageRow.="<td class='actions'>";
				$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $productionProcess['ProductionProcess']['id']));
				if ($bool_edit_permission){
					$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $productionProcess['ProductionProcess']['id']));
				}
				if ($bool_delete_permission){
					$pageRow.=$this->Form->postLink(__('Delete'), array('action' => 'delete', $productionProcess['ProductionProcess']['id']), array(), __('Está seguro que quiere eliminar proceso de producción # %s?', $productionProcess['ProductionProcess']['production_process_code']));
				}
				if ($bool_annul_permission){
					$pageRow.=$this->Form->postLink(__('Anular'), array('action' => 'annul', $productionProcess['ProductionProcess']['id']), array(), __('Está seguro que quiere anular proceso de producción # %s?', $productionProcess['ProductionProcess']['production_process_code']));
				}
			$pageRow.="</td>";
		if (!$productionProcess['ProductionProcess']['bool_annulled']){
			$pageBody.="<tr>".$pageRow."</tr>";
		}
		else {
			$pageBody.="<tr class='italic'>".$pageRow."</tr>";
		}
	}

	$pageTotalRow="";
	//$pageTotalRow.="<tr class='totalrow'>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//	$pageTotalRow.="<td></td>";
	//$pageTotalRow.="</tr>";

	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	$table_id="procesos_produccion";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo $pageOutput;
	$excelOutput="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	$_SESSION['resumen'] = $excelOutput;
?>
</div>
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