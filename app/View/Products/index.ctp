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
<div class="products index">
<?php 
	echo "<h2>Productos Activos</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo $this->Form->input('Report.currency_id',array('label'=>__('Mostrar precios en '),'options'=>$currencies,'default'=>$currencyId));
		echo "</fieldset>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumen','Resumen Productos Activos'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo ($bool_add_permission?"<li>".$this->Html->link(__('New Product'), array('action' => 'add'))."</li>":"");
		echo "<br/>";
    echo "<li>".$this->Html->link(__('Productos Desactivados'), array('action' => 'resumenProductosDesactivados'))."</li>";
		echo "<br/>";
		echo ($bool_provider_index_permission?"<li>".$this->Html->link(__('List Providers'), array('controller' => 'providers', 'action' => 'index'))."</li>":"");
		echo ($bool_provider_index_permission?"<li>".$this->Html->link(__('New Provider'), array('controller' => 'providers', 'action' => 'add'))."</li>":"");
		echo ($bool_productcategory_index_permission?"<li>".$this->Html->link(__('List Product Categories'), array('controller' => 'product_categories', 'action' => 'index'))."</li>":"");
		echo ($bool_productcategory_add_permission?"<li>".$this->Html->link(__('New Product Category'), array('controller' => 'product_categories', 'action' => 'add'))."</li>":"");
	echo "</ul>";
?>
</div>
<div>
<?php
	$pageHeader="";
	$excelHeader="";
	$pageHeader.="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('product_category_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('code')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('name')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('description')."</th>";
			//$pageHeader.="<th>".$this->Paginator->sort('url_image')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('product_unit_price')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('product_unit_cost')."</th>";
			$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader.="<thead>";
		$excelHeader.="<tr><th colspan='6' align='center'>".COMPANY_NAME."</th></tr>";	
		$excelHeader.="<tr><th colspan='6' align='center'>".__('Resumen de Productos')."</th></tr>";
		$excelHeader.="<tr>";
			$excelHeader.="<th>".$this->Paginator->sort('product_category_id')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('code')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('name')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('description')."</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('url_image')."</th>";
			$excelHeader.="<th>".$this->Paginator->sort('product_unit_price')."</th>";
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
	foreach ($products as $product){ 
		$pageRow="";
			$pageRow.="<td>".$this->Html->link($product['ProductCategory']['name'], array('controller' => 'product_categories', 'action' => 'view', $product['ProductCategory']['id']))."</td>";
			$pageRow.="<td>".h($product['Product']['code'])."</td>";
			$pageRow.="<td>".$this->Html->link($product['Product']['name'].($product['Product']['bool_no_iva']?" (SIN IVA)":""), array('action' => 'view', $product['Product']['id']))."</td>";
			$pageRow.="<td>".h($product['Product']['description'])."</td>";
			//$pageRow.="<td>".h($product['Product']['url_image'])."</td>";
			$pageRow.="<td".$currencyClass."><span class='currency'>".$product['Currency']['abbreviation']."</span><span class='amountright'>".h($product['Product']['product_unit_price'])."</span></td>";
			$pageRow.="<td".$currencyClass."><span class='currency'>".$product['Currency']['abbreviation']."</span><span class='amountright'>".h($product['Product']['product_unit_cost'])."</span></td>";

			$excelBody.="<tr>".$pageRow."</tr>";

			$pageRow.="<td class='actions'>";
				$pageRow.=($bool_edit_permission?$this->Html->link(__('Edit'), array('action' => 'edit', $product['Product']['id'])):"");
				//$pageRow.=->postLink(__('Delete'), array('action' => 'delete', $product['Product']['id']), array(), __('Está seguro que quiere eliminar Producto %s?', $product['Product']['code']."_".$product['Product']['name']));
			$pageRow.="</td>";

		$pageBody.="<tr>".$pageRow."</tr>";
	}

	/*
	$pageTotalRow="";
	$pageTotalRow.="<tr class=\'totalrow\'>";
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
	$table_id="productos_activos";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo $pageOutput;
	$excelOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	$_SESSION['resumen'] = $excelOutput;
?>
</div>
