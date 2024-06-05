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
<div class="productionOrderStates index">
<?php 
	echo '<h1>'.__('Production Order States').'</h1>';
/*  
	echo $this->Form->create('Report');
		echo '<fieldset>';
			echo $this->Form->input('Report.startdate',['type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2014,'maxYear'=>date('Y')]);
			echo $this->Form->input('Report.enddate',['type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2014,'maxYear'=>date('Y')]);
		echo '</fieldset>';
		echo '<button id="previousmonth" class="monthswitcher">'.__('Previous Month').'</button>';
		echo '<button id="nextmonth" class="monthswitcher">'.__('Next Month').'</button>';
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), ['action' => 'guardarResumen'], ['class' => 'btn btn-primary']);
*/
?> 
</div>
<div class="actions">
<?php 
	echo '<h2>'.__('Actions').'</h2>';
	echo '<ul>';
  if ($bool_add_permission){
    echo '<li>'.$this->Html->link(__('New Production Order State'), ['action' => 'crear']).'</li>';
  }
	echo '</ul>';
?>
</div>
<div>
<?php
	$tableHeader='<thead>';
		$tableHeader.='<tr>';
			$tableHeader.='<th>Nombre</th>';
      $tableHeader.='<th>Orden en la lista</th>';
		$tableHeader.='</tr>';
	$tableHeader.='</thead>';
	$tableBody='';
	foreach ($productionOrderStates as $productionOrderState){ 
		$tableRow='';		$tableRow.='<td>'.$this->Html->link($productionOrderState['ProductionOrderState']['name'],['action' => 'detalle', $productionOrderState['ProductionOrderState']['id']]).'</td>';
    $tableRow.='<td>'.$productionOrderState['ProductionOrderState']['list_order'].'</td>';
    $tableBody.='<tr>'.$tableRow.'</tr>';
	}

	$totalRow='';
/*
    $totalRow.='<tr class="totalrow">';
		$totalRow.='<td></td>';
		$totalRow.='<td></td>';
	$totalRow.='</tr>';
*/
	$tableBody='<tbody>'.$totalRow.$tableBody.$totalRow.'</tbody>';
	$tableId='estados de producción';
	$pageOutput='<table id="'.$tableId.'">'.$tableHeader.$tableBody.'</table>';
	echo $pageOutput;
	
	$_SESSION['resumenEstadosDeProducción'] = $pageOutput;
?>
</div>