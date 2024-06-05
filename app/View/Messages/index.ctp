<div class="messages index fullwidth">
<?php 
	echo "<h2>".__('Messages')."</h2>";
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
<!--div class='actions'-->
<?php 
	/*
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('New Message'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New From User'), array('controller' => 'users', 'action' => 'add'))."</li>";
		echo "<li>".$this->Html->link(__('List Message Recipients'), array('controller' => 'message_recipients', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Message Recipient'), array('controller' => 'message_recipients', 'action' => 'add'))."</li>";
	echo "</ul>";
	
	*/
?>
<!--/div -->
<div>
<?php
	$excelOutput="";

	$pageHeader="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('created','Fecha')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('from_user_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('subject')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('body_text')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('url_image','Archivo Adjunto')."</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader="<thead>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('created','Fecha')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('from_user_id')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('subject')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('body_text')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('url_image','Archivo Adjunto')."</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	foreach ($receivedMessages as $message){ 
		$messageDateTime=new DateTime($message['Message']['created']);
		$url=$message['Message']['url_image'];
		$pageRow=""	;	
			$pageRow.="<td>".$messageDateTime->format('d-m-Y H:i')."</td>";
			$pageRow.="<td>".$this->Html->link($message['FromUser']['username'], array('controller' => 'users', 'action' => 'view', $message['FromUser']['id']))."</td>";
			$pageRow.="<td>".h($message['Message']['subject'])."</td>";
			$pageRow.="<td>".h($message['Message']['body_text'])."</td>";
			$pageRow.="<td><a href='".$this->Html->url('/').$url."' target='_blank'>".$url."</a></td>";
			
		$excelBody.="<tr>".$pageRow."</tr>";
			$pageRow.="<td class='actions'>";
				$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $message['Message']['id']));
				//$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $message['Message']['id']));
				//$pageRow.=->postLink(__('Delete'), array('action' => 'delete', $message['Message']['id']), array(), __('Are you sure you want to delete # %s?', $message['Message']['id']));
			$pageRow.="</td>";
		if ($message['MessageRecipient'][0]['bool_read']){
			$pageBody.="<tr>".$pageRow."</tr>";
		}
		else {
			$pageBody.="<tr class='bold'>".$pageRow."</tr>";
		}
	}

	$pageTotalRow="";

	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	$table_id="recibidos";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo "<h2>Mensajes Recibidos</h2>";
	echo $pageOutput;
	$excelOutput.="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	
	$pageHeader="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('created','Fecha')."</th>";
			//$pageHeader.="<th>".$this->Paginator->sort('from_user_id')."</th>";
			$pageHeader.="<th>Destinatarios</th>";
			$pageHeader.="<th>".$this->Paginator->sort('subject')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('body_text')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('url_image','Archivo Adjunto')."</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader="<thead>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('created','Fecha')."</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('from_user_id')."</th>";
			$excelHeader.="<th>Destinatarios</th>";
			$excelHeader.="<th>".$this->Paginator->sort('subject')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('body_text')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('url_image','Archivo Adjunto')."</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	foreach ($sentMessages as $message){ 
		$messageDateTime=new DateTime($message['Message']['created']);
		$url=$message['Message']['url_image'];
		$pageRow=""	;	
			$pageRow.="<td>".$messageDateTime->format('d-m-Y H:i')."</td>";
			//$pageRow.="<td>".$this->Html->link($message['FromUser']['username'], array('controller' => 'users', 'action' => 'view', $message['FromUser']['id']))."</td>";
			$pageRow.="<td>";
				foreach ($message['MessageRecipient'] as $recipient){
					if (!empty($recipient['RecipientUser'])){
						$pageRow.=$recipient['RecipientUser']['username']."<br/>";
					}
				}
			$pageRow.="</td>";
			$pageRow.="<td>".h($message['Message']['subject'])."</td>";
			$pageRow.="<td>".h($message['Message']['body_text'])."</td>";
			
			$pageRow.="<td><a href='".$this->Html->url('/').$url."' target='_blank'>".$url."</a></td>";
			
		$excelBody.="<tr>".$pageRow."</tr>";
			$pageRow.="<td class='actions'>";
				$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $message['Message']['id']));
				//$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $message['Message']['id']));
				//$pageRow.=->postLink(__('Delete'), array('action' => 'delete', $message['Message']['id']), array(), __('Are you sure you want to delete # %s?', $message['Message']['id']));
			$pageRow.="</td>";
		$pageBody.="<tr>".$pageRow."</tr>";
	}

	$pageTotalRow="";
	
	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	$table_id="enviados";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo "<h2>Mensajes Enviados</h2>";
	echo $pageOutput;
	$excelOutput.="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	
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