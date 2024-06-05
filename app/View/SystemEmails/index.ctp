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
<div class="systemEmails index">
<?php 
	echo "<h2>".__('System Emails')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015,'maxYear'=>date('Y')));
			echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015,'maxYear'=>date('Y')));
			echo "<br/>";						
			if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
				echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Usuario'),'options'=>$users,'default'=>$userId,'empty'=>array('0'=>__('Todos Usuarios'))));
			}
			else {
				echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Usuario'),'options'=>$users,'default'=>$userId,'class'=>'fixed'));
			}
		echo "</fieldset>";
		echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
		echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
		echo "<br/>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardar'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
	if ($bool_add_permission){
		echo "<li>".$this->Html->link(__('New System Email'), array('action' => 'add'))."</li>";
	}
	echo "</ul>";
?>
</div>
<div>
<?php
	$pageHeader="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('datetime_sent')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('email_from')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('subject')."</th>";
			$pageHeader.="<th>A</th>";
			$pageHeader.="<th>CC</th>";
			$pageHeader.="<th>BCC</th>";
			//$pageHeader.="<th>".$this->Paginator->sort('body')."</th>";
			//$pageHeader.="<th>".$this->Paginator->sort('boundary')."</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader="<thead>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('datetime_sent')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('email_from')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('subject')."</th>";
			$excelHeader.="<th>A</th>";
			$excelHeader.="<th>CC</th>";
			$excelHeader.="<th>BCC</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('body')."</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('boundary')."</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	foreach ($systemEmails as $systemEmail){ 
		$pageRow="";
		$pageRow.="<td>".h($systemEmail['SystemEmail']['datetime_sent'])."</td>";
		$pageRow.="<td>".h($systemEmail['SystemEmail']['email_from'])."</td>";
		$pageRow.="<td>".h($systemEmail['SystemEmail']['subject'])."</td>";
		$pageRow.="<td>";
		if (!empty($systemEmail['SystemEmailRecipient'])){
			foreach ($systemEmail['SystemEmailRecipient'] as $emailRecipient){
				$pageRow.=$emailRecipient['email_recipient']."<br/>";
			}
		}	
		$pageRow.="</td>";
		$pageRow.="<td>";
		if (!empty($systemEmail['SystemEmailCc'])){
			foreach ($systemEmail['SystemEmailCc'] as $emailCc){
				$pageRow.=$emailCc['email_cc']."<br/>";
			}
		}		
		$pageRow.="</td>";
		$pageRow.="<td>";
		if (!empty($systemEmail['SystemEmailBcc'])){
			foreach ($systemEmail['SystemEmailBcc'] as $emailBcc){
				$pageRow.=$emailBcc['email_bcc']."<br/>";
			}
		}			
		$pageRow.="</td>";
		//$pageRow.="<td>".h($systemEmail['SystemEmail']['body'])."</td>";
		//$pageRow.="<td>".h($systemEmail['SystemEmail']['boundary'])."</td>";
			$excelBody.="<tr>".$pageRow."</tr>";

			$pageRow.="<td class='actions'>";
				$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $systemEmail['SystemEmail']['id']));
			$pageRow.="</td>";

		$pageBody.="<tr>".$pageRow."</tr>";
	}

	$pageTotalRow="";
	$pageTotalRow.="<tr class='totalrow'>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
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