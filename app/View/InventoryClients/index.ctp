<div class="clients index">
<?php 
	echo "<h2>".__('Clients')."</h2>";
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumen'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Client'), array('action' => 'add'))."</li>";
		}
		echo "<br/>";
		if ($bool_inventorycontact_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Contacts'), array('controller' => 'inventory_contacts', 'action' => 'index'))."</li>";
		}
		if ($bool_inventorycontact_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Contact'), array('controller' => 'inventory_contacts', 'action' => 'add'))."</li>";
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
			$pageHeader.="<th>".$this->Paginator->sort('name')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('ruc')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('address')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('phone')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('cell')."</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader.="<thead>";
		$excelHeader.="<tr><th colspan='4' align='center'>".COMPANY_NAME."</th></tr>";	
		$excelHeader.="<tr><th colspan='4' align='center'>".__('Resumen de Clientes')."</th></tr>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('name')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('ruc')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('address')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('phone')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('cell')."</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	foreach ($inventoryClients as $inventoryClient){ 
		$pageRow="";
			$pageRow.="<td>".h($inventoryClient['InventoryClient']['name']).($inventoryClient['InventoryClient']['bool_active']?"":" (Inactivo)")."</td>";
			$pageRow.="<td>".h($inventoryClient['InventoryClient']['ruc'])."</td>";
			$pageRow.="<td>".h($inventoryClient['InventoryClient']['address'])."</td>";
			$pageRow.="<td>".h($inventoryClient['InventoryClient']['phone'])."</td>";
			$pageRow.="<td>".h($inventoryClient['InventoryClient']['cell'])."</td>";
			if ($inventoryClient['InventoryClient']['bool_active']){
				$excelBody.="<tr>".$pageRow."</tr>";
			}
			else {
				$excelBody.="<tr class='italic'>".$pageRow."</tr>";
			}
			$pageRow.="<td class='actions'>";
				$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $inventoryClient['InventoryClient']['id']));
				if ($bool_edit_permission){
					$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $inventoryClient['InventoryClient']['id']));
				}
				//$pageRow.=->postLink(__('Delete'), array('action' => 'delete', $inventoryClient['InventoryClient']['id']), array(), __('Are you sure you want to delete # %s?', $inventoryClient['InventoryClient']['id']));
			$pageRow.="</td>";

		if ($inventoryClient['InventoryClient']['bool_active']){
			$pageBody.="<tr>".$pageRow."</tr>";
		}
		else {
			$pageBody.="<tr class='italic'>".$pageRow."</tr>";
		}
	}

	$pageBody="<tbody>".$pageBody."</tbody>";
	$table_id="Clientes";
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