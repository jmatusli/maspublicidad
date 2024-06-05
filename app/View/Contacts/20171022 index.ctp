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
<div class="contacts index">
<?php 
	echo "<h2>".__('Contacts')."</h2>";
  echo $this->Form->create('Report'); 
		echo "<fieldset>"; 
		echo "<div class='container-fluid'>";
			echo "<div class='rows'>";
				echo "<div class='col-md-6'>";	
				if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
					echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Cliente asociado con Usuario'),'options'=>$users,'default'=>$userId,'empty'=>array('0'=>__('Todos Usuarios'))));
				}
				else {
					echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Cliente asociado con Usuario'),'options'=>$users,'default'=>$userId,'type'=>'hidden'));
				}												
					echo $this->Form->input('Report.active_display_option_id',array('label'=>__('Clientes Activos'),'default'=>$activeDisplayOptionId));					
				echo "</div>";
			echo "</div>";
		echo "</div>";
		echo  "</fieldset>";
	echo $this->Form->end(__('Refresh')); 
	echo "</br>";
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumen'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_add_permission){
			echo "<li>".$this->Html->link(__('New Contact'), array('action' => 'add'))."</li>";
		}
		echo "<br/>";
		if ($bool_client_index_permission){
			echo "<li>".$this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index'))."</li>";
		}
		if ($bool_client_add_permission){
			echo "<li>".$this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add'))."</li>";
		}
		if ($bool_quotation_index_permission){
			echo "<li>".$this->Html->link(__('List Quotations'), array('controller' => 'quotations', 'action' => 'index'))."</li>";
		}
		if ($bool_quotation_add_permission){
			echo "<li>".$this->Html->link(__('New Quotation'), array('controller' => 'quotations', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
<div>
<?php
	$pageHeader="";
	$excelHeader="";
	$pageHeader.="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('client_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('first_name')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('last_name')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('phone')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('cell')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('email')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('department')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('bool_active')."</th>";
			//$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader.="<thead>";
		$excelHeader.="<tr><th colspan='7' align='center'>".COMPANY_NAME."</th></tr>";	
		$excelHeader.="<tr><th colspan='7' align='center'>".__('Resumen de Contactos')."</th></tr>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('client_id')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('first_name')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('last_name')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('phone')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('cell')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('email')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('department')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('bool_active')."</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	foreach ($contacts as $contact){ 
		$pageRow="";
			$pageRow.="<td>".$this->Html->link($contact['Client']['name'], array('controller' => 'clients', 'action' => 'view', $contact['Client']['id']))."</td>";
			$pageRow.="<td>".$this->Html->link($contact['Contact']['first_name'],array('action'=>'view',$contact['Contact']['id']))."</td>";
			$pageRow.="<td>".$this->Html->link($contact['Contact']['last_name'],array('action'=>'view',$contact['Contact']['id']))."</td>";
			$pageRow.="<td>".h($contact['Contact']['phone'])."</td>";
			$pageRow.="<td>".h($contact['Contact']['cell'])."</td>";
			$pageRow.="<td>".$this->Text->autoLinkEmails($contact['Contact']['email'])."</td>";
			$pageRow.="<td>".h($contact['Contact']['department'])."</td>";
			$pageRow.="<td>".($contact['Contact']['bool_active']?__('Yes'):__('No'))."</td>";

			$excelBody.="<tr>".$pageRow."</tr>";

			//$pageRow.="<td class='actions'>";
			//	$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $contact['Contact']['id']));
			//	if ($bool_edit_permission){
			//		$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $contact['Contact']['id']));
			//	}
			//	$pageRow.=$this->Form->postLink(__('Eliminar'), array('action' => 'delete', $contact['Contact']['id']), array(), __('Está seguro que quiere eliminar %s?', $contact['Contact']['first_name']." ".$contact['Contact']['last_name']));
			//$pageRow.="</td>";

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
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		//$pageTotalRow.="<td></td>";
	$pageTotalRow.="</tr>";

	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	$table_id="Contactos";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo $pageOutput;
	$excelOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	$_SESSION['resumen'] = $excelOutput;
?>
</div>
