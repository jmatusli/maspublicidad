<div class="productCategories view">
<?php 
	echo "<h2>".__('Product Category')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($productCategory['ProductCategory']['name'])."</dd>";
		echo "<dt>".__('Description')."</dt>";
		echo "<dd>".h($productCategory['ProductCategory']['description'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Product Category'), array('action' => 'edit', $productCategory['ProductCategory']['id']))."</li>";
		}
		//echo "<li>".$this->Form->postLink(__('Delete Product Category'), array('action' => 'delete', $productCategory['ProductCategory']['id']), array(), __('Are you sure you want to delete # %s?', $productCategory['ProductCategory']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Product Categories'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Product Category'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_product_index_permission){
			echo "<li>".$this->Html->link(__('List Products'), array('controller' => 'products', 'action' => 'index'))."</li>";
		}
		if ($bool_product_add_permission){
			echo "<li>".$this->Html->link(__('New Product'), array('controller' => 'products', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?> 
</div>
<div class="related">
<?php 
	if (!empty($productCategory['Product'])){
		echo "<h3>".__('Related Products')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Code')."</th>";
				echo "<th>".__('Name')."</th>";
				echo "<th>".__('Description')."</th>";
				//echo "<th>".__('Url Image')."</th>";
				echo "<th>".__('Product Unit Price')."</th>";
				
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($productCategory['Product'] as $product){ 
			if ($product['currency_id']==CURRENCY_CS){
				$currencyClass="CScurrency";
			}
			elseif ($product['currency_id']==CURRENCY_USD){
				$currencyClass="USDcurrency";
			}
			echo "<tr>";
				echo "<td>".$product['code']."</td>";
				echo "<td>".$product['name']."</td>";
				echo "<td>".$product['description']."</td>";
				//echo "<td>".$product['url_image']."</td>";
				echo "<td class='".$currencyClass."'><span class='currency'></span><span class='amountright'>".$product['product_unit_price']."</span></td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'products', 'action' => 'view', $product['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'products', 'action' => 'edit', $product['id']));
					//echo $this->Form->postLink(__('Delete'), array('controller' => 'products', 'action' => 'delete', $product['id']), array(), __('Are you sure you want to delete # %s?', $product['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
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