<div class="entries view">
<?php 
	echo "<h2>".__('Entry')." ".$entry['Entry']['entry_code'].(($entry['Entry']['bool_annulled'])?" (Anulada)":"")."</h2>";
	echo "<dl>";
		$entryDateTime=new DateTime($entry['Entry']['entry_date']);
		$dueDateTime=new DateTime($entry['Entry']['due_date']);
		echo "<dt>".__('Entry Date')."</dt>";
		echo "<dd>".$entryDateTime->format('d-m-Y')."</dd>";
		echo "<dt>".__('Entry Code')."</dt>";
		echo "<dd>".h($entry['Entry']['entry_code']).(($entry['Entry']['bool_annulled'])?" (Anulada)":"")."</dd>";
		echo "<dt>".__('Provider')."</dt>";
		echo "<dd>".$this->Html->link($entry['Provider']['name'], array('controller' => 'providers', 'action' => 'view', $entry['Provider']['id']))."</dd>";
		echo "<dt>".__('Due Date')."</dt>";
		echo "<dd>".$dueDateTime->format('d-m-Y')."</dd>";
		echo "<dt>".__('Bool Iva')."</dt>";
		echo "<dd>".(($entry['Entry']['bool_iva'])?__("Yes"):__("No"))."</dd>";
		echo "<dt>".__('Cost Subtotal')."</dt>";
		echo "<dd>".$entry['Currency']['abbreviation']." ".$entry['Entry']['cost_subtotal']."</dd>";
		echo "<dt>".__('Cost Iva')."</dt>";
		echo "<dd>".$entry['Currency']['abbreviation']." ".$entry['Entry']['cost_iva']."</dd>";
		echo "<dt>".__('Cost Total')."</dt>";
		echo "<dd>".$entry['Currency']['abbreviation']." ".$entry['Entry']['cost_total']."</dd>";
		echo "<dt>".__('Bool Paid')."</dt>";
		echo "<dd>".(($entry['Entry']['bool_paid'])?__("Yes"):__("No"))."</dd>";
		echo "<dt>".__('Observation')."</dt>";
		if (!empty($entry['Entry']['observation'])){
			echo "<dd>".h()."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
	echo "</dl>";

	if (!empty($entry['StockMovement'])){
		if ($entry['Currency']['id']==CURRENCY_USD){
			$currencyClass="USDcurrency";
		}
		else {
			$currencyClass="CScurrency";
		}
		echo "<h3>".__('Related Stock Movements')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Product Id')."</th>";
				echo "<th>".__('Product Quantity')."</th>";
				echo "<th>".__('Product Unit Cost')."</th>";
				echo "<th>".__('Product Total Cost')."</th>";
			echo "</tr>";
		foreach ($entry['StockMovement'] as $stockMovement){ 
			echo "<tr>";
				echo "<td>".$stockMovement['product_id']."</td>";
				echo "<td>".$stockMovement['product_quantity']."</td>";
				echo "<td class='".$currencyClass."'><span class='currency'>".$entry['Currency']['abbreviation']."</span><span class='amountright'>".$stockMovement['product_unit_cost']."</span></td>";
				echo "<td class='".$currencyClass."'><span class='currency'>".$entry['Currency']['abbreviation']."</span><span class='amountright'>".$stockMovement['product_quantity']*$stockMovement['product_unit_cost']."</span></td>";
			echo "</tr>";
		}
			echo "<tr class='totalrow'>";
				echo "<td>Total</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td class='".$currencyClass."'><span class='currency'>".$entry['Currency']['abbreviation']."</span><span class='amountright'>".$entry['Entry']['cost_subtotal']."</span></td>";
			echo "</tr>";	
		echo "</table>";
	}
?>
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Guardar como pdf'), array('action' => 'viewPdf','ext'=>'pdf',$entry['Entry']['id'],$filename),array("target"=>'_blank'))."</li>";
		echo "<br/>";
		if ($boolEditable){
			echo "<li>".$this->Html->link(__('Edit Entry'), array('action' => 'edit', $entry['Entry']['id']))."</li>";
			echo "<br/>";		
			echo "<li>".$this->Form->postLink(__('Delete Entry'), array('action' => 'delete', $entry['Entry']['id']), array(), __('Are you sure you want to delete # %s?', $entry['Entry']['id']))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Entries'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Entry'), array('action' => 'add'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Providers'), array('controller' => 'providers', 'action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Provider'), array('controller' => 'providers', 'action' => 'add'))."</li>";
	echo "</ul>";
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