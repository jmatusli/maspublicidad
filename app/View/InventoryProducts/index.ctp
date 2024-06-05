<div class="products index">
<?php 
	echo "<h2>".__('Inventory Products')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo $this->Form->input('Report.currency_id',array('label'=>__('Mostrar precios en '),'options'=>$currencies,'default'=>$currencyId));
		echo "</fieldset>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardar'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Product'), array('action' => 'add'))."</li>";
		}
		echo "<br/>";
		if ($bool_inventoryprovider_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Providers'), array('controller' => 'inventory_providers', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryprovider_index_permission){
			echo "<li>".$this->Html->link(__('New Inventory Provider'), array('controller' => 'inventory_providers', 'action' => 'add'))."</li>";
		}
		if ($bool_inventoryproductline_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Product Lines'), array('controller' => 'product_categories', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryproductline_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Product Line'), array('controller' => 'product_categories', 'action' => 'add'))."</li>";
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
			$pageHeader.="<th>".$this->Paginator->sort('inventory_product_line_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('code')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('name')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('description')."</th>";
			//$pageHeader.="<th>".$this->Paginator->sort('url_image')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('product_unit_price_A','Precio Unitario A')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('product_unit_price_B','Precio Unitario B')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('product_unit_price_C','Precio Unitario C')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('product_unit_cost')."</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader.="<thead>";
		$excelHeader.="<tr><th colspan='7' align='center'>".COMPANY_NAME."</th></tr>";	
		$excelHeader.="<tr><th colspan='7' align='center'>".__('Resumen de Productos')."</th></tr>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('inventory_product_line_id')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('code')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('name')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('description')."</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('url_image')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('product_unit_price_A','Precio Unitario A')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('product_unit_price_B','Precio Unitario B')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('product_unit_price_C','Precio Unitario C')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('product_unit_cost')."</th>";
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";

	$currencyClass="";
		if ($currencyId==CURRENCY_CS){
			$currencyClass=" class='CScurrency'";
		}
		else if ($currencyId==CURRENCY_USD){
			$currencyClass=" class='USDcurrency'";
		}
	foreach ($inventoryProducts as $product){ 
		$pageRow="";
			$pageRow.="<td>".$this->Html->link($product['InventoryProductLine']['name'], array('controller' => 'inventory_product_lines', 'action' => 'view', $product['InventoryProductLine']['id']))."</td>";
			$pageRow.="<td>".h($product['InventoryProduct']['code'])."</td>";
			$pageRow.="<td>".h($product['InventoryProduct']['name'])."</td>";
			$pageRow.="<td>".h($product['InventoryProduct']['description'])."</td>";
			//$pageRow.="<td>".h($product['InventoryProduct']['url_image'])."</td>";
			if ($product['InventoryProduct']['bool_promotion']){
				$pageRow.="<td".$currencyClass."><span class='currency'>".$product['Currency']['abbreviation']."</span><span class='amountright'>".h($product['InventoryProduct']['product_unit_price_A'])."</span></td>";
				$pageRow.="<td".$currencyClass."><span class='currency'>".$product['Currency']['abbreviation']."</span><span class='amountright'>".h($product['InventoryProduct']['product_unit_price_B'])."</span></td>";
				$pageRow.="<td".$currencyClass."><span class='currency'>".$product['Currency']['abbreviation']."</span><span class='amountright'>".h($product['InventoryProduct']['product_unit_price_C'])."</span></td>";
			}
			else {
				$pageRow.="<td>-</td>";
				$pageRow.="<td>-</td>";
				$pageRow.="<td>-</td>";
			}
			$pageRow.="<td".$currencyClass."><span class='currency'>".$product['Currency']['abbreviation']."</span><span class='amountright'>".h($product['InventoryProduct']['product_unit_cost'])."</span></td>";

			$excelBody.="<tr>".$pageRow."</tr>";

			$pageRow.="<td class='actions'>";
				$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $product['InventoryProduct']['id']));
				if ($bool_edit_permission){
					$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $product['InventoryProduct']['id']));
				}
				//$pageRow.=->postLink(__('Delete'), array('action' => 'delete', $product['Product']['id']), array(), __('Are you sure you want to delete # %s?', $product['Product']['id']));
			$pageRow.="</td>";

		$pageBody.="<tr>".$pageRow."</tr>";
	}

	/*
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
		$pageTotalRow.="<td></td>";
	$pageTotalRow.="</tr>";

	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	*/
	$pageBody="<tbody>".$pageBody."</tbody>";
	$table_id="productos";
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