<div class="contacts index">
<?php 
	echo "<h2>".__('Contacts')."</h2>";
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumen'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Contact'), array('action' => 'add'))."</li>";
		}
		echo "<br/>";
		if ($bool_inventoryclient_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Clients'), array('controller' => 'inventory_clients', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryclient_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Client'), array('controller' => 'inventory_clients', 'action' => 'add'))."</li>";
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
			$pageHeader.="<th>".$this->Paginator->sort('inventory_client_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('first_name')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('last_name')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('phone')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('cell')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('email')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('department')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('bool_active')."</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader.="<thead>";
		$excelHeader.="<tr><th colspan='7' align='center'>".COMPANY_NAME."</th></tr>";	
		$excelHeader.="<tr><th colspan='7' align='center'>".__('Resumen de Contactos')."</th></tr>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('inventory_client_id')."</th>";
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

	foreach ($inventoryContacts as $inventoryContact){ 
		$pageRow="";
			$pageRow.="<td>".$this->Html->link($inventoryContact['InventoryClient']['name'], array('controller' => 'inventory_clients', 'action' => 'view', $inventoryContact['InventoryClient']['id']))."</td>";
			$pageRow.="<td>".h($inventoryContact['InventoryContact']['first_name'])."</td>";
			$pageRow.="<td>".h($inventoryContact['InventoryContact']['last_name'])."</td>";
			$pageRow.="<td>".h($inventoryContact['InventoryContact']['phone'])."</td>";
			$pageRow.="<td>".h($inventoryContact['InventoryContact']['cell'])."</td>";
			$pageRow.="<td>".$this->Text->autoLinkEmails($inventoryContact['InventoryContact']['email'])."</td>";
			$pageRow.="<td>".h($inventoryContact['InventoryContact']['department'])."</td>";
			$pageRow.="<td>".($inventoryContact['InventoryContact']['bool_active']?__('Yes'):__('No'))."</td>";

			$excelBody.="<tr>".$pageRow."</tr>";

			$pageRow.="<td class='actions'>";
				$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $inventoryContact['InventoryContact']['id']));
				if ($bool_edit_permission){
					$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $inventoryContact['InventoryContact']['id']));
				}
				$pageRow.=$this->Form->postLink(__('Eliminar'), array('action' => 'delete', $inventoryContact['InventoryContact']['id']), array(), __('Está seguro que quiere eliminar %s?', $inventoryContact['InventoryContact']['first_name']." ".$inventoryContact['InventoryContact']['last_name']));
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
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
		$pageTotalRow.="<td></td>";
	$pageTotalRow.="</tr>";

	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	$table_id="Contactos";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo $pageOutput;
	$excelOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
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