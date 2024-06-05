<div class="contacts view">
<?php 
	echo "<h2>".__('Contact')."</h2>";
	echo "<dl>";
		echo "<dt>".__('Client')."</dt>";
		echo "<dd>".$this->Html->link($contact['Client']['name'], array('controller' => 'clients', 'action' => 'view', $contact['Client']['id']))."</dd>";
		echo "<dt>".__('First Name')."</dt>";
		echo "<dd>".h($contact['Contact']['first_name'])."</dd>";
		echo "<dt>".__('Last Name')."</dt>";
		echo "<dd>".h($contact['Contact']['last_name'])."</dd>";
		echo "<dt>".__('Phone')."</dt>";
		if (!empty($contact['Contact']['phone'])){
			echo "<dd>".h($contact['Contact']['phone'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		if (!empty($contact['Contact']['cell'])){
			echo "<dd>".h($contact['Contact']['cell'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Email')."</dt>";
		if (!empty($contact['Contact']['email'])){
			echo "<dd>".$this->Text->autoLinkEmails($contact['Contact']['email'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Department')."</dt>";
		if (!empty($contact['Contact']['department'])){
			echo "<dd>".h($contact['Contact']['department'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Bool Active')."</dt>";
		echo "<dd>".h($contact['Contact']['bool_active']?__('Yes'):__('No'))."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Contact'), array('action' => 'edit', $contact['Contact']['id']))."</li>";
		}
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Eliminar Contacto'), array('action' => 'delete', $contact['Contact']['id']), array(), __('Está seguro que quiere eliminar %s?', $contact['Contact']['first_name']." ".$contact['Contact']['last_name']))."</li>";
		}
		echo "<li>".$this->Html->link(__('List Contacts'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Contact'), array('action' => 'add'))."</li>";
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
<div class="related">
<?php 
	if (!empty($contact['Quotation'])){
		echo "<h3>".__('Cotizaciones para este Contacto')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Quotation Date')."</th>";
					echo "<th>".__('Quotation Code')."</th>";
					echo "<th>".__('User')."</th>";
					echo "<th>".__('Client')."</th>";
					echo "<th>".__('Bool Iva')."</th>";
					echo "<th>".__('Price Subtotal')."</th>";
					echo "<th>".__('Price Iva')."</th>";
					echo "<th>".__('Price Total')."</th>";
					echo"<th class='actions'>".__('Actions')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
				$totalSubtotalCS=0;
				$totalIvaCS=0;
				$totalTotalCS=0;
				
				$totalSubtotalUSD=0;
				$totalIvaUSD=0;
				$totalTotalUSD=0;
			
				foreach ($contact['Quotation'] as $quotation){
					$quotationDateTime=new Datetime($quotation['quotation_date']);
							
							if ($quotation['currency_id']==CURRENCY_CS){
								$classCurrency=" class='CScurrency'";
								$totalSubtotalCS+=$quotation['price_subtotal'];
								$totalIvaCS+=$quotation['price_iva'];
								$totalTotalCS+=$quotation['price_total'];
							}
							elseif ($quotation['currency_id']==CURRENCY_USD){
								$classCurrency=" class='USDcurrency'";
								$totalSubtotalUSD+=$quotation['price_subtotal'];
								$totalIvaUSD+=$quotation['price_iva'];
								$totalTotalUSD+=$quotation['price_total'];
							}
					echo "<tr>";
						echo "<td>".$quotationDateTime->format('d-m-Y')."</td>";
						echo "<td>".$this->Html->link($quotation['quotation_code'],array('controller'=>'quotations','action'=>'view',$quotation['id']))."</td>";
						echo "<td>".$this->Html->link($quotation['User']['username'],array('controller'=>'users','action'=>'view',$quotation['User']['id']))."</td>";
						echo "<td>".$this->Html->link($quotation['Client']['name'],array('controller'=>'clients','action'=>'view',$quotation['Client']['id']))."</td>";
						echo "<td>".($quotation['bool_iva']?__('Yes'):__('No'))."</td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$quotation['price_subtotal']."</td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$quotation['price_iva']."</td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$quotation['price_total']."</td>";
						echo "<td class='actions'>";
							echo $this->Html->link(__('View'), array('controller' => 'quotations', 'action' => 'view', $quotation['id']));
							echo $this->Html->link(__('Edit'), array('controller' => 'quotations', 'action' => 'edit', $quotation['id']));
							//echo $this->Form->postLink(__('Delete'), array('controller' => 'quotations', 'action' => 'delete', $quotation['id']), array(), __('Are you sure you want to delete # %s?', $quotation['id']));
						echo "</td>";
					echo "</tr>";
				}
				if ($totalSubtotalCS>0){
					echo "<tr class='totalrow'>";
						echo "<td>Totales C$</td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td class='CScurrency'><span class='currency'></span><span class='amountright'>".number_format($totalSubtotalCS,2,".",",")."</span></td>";
						echo "<td class='CScurrency'><span class='currency'></span><span class='amountright'>".number_format($totalIvaCS,2,".",",")."</span></td>";
						echo "<td class='CScurrency'><span class='currency'></span><span class='amountright'>".number_format($totalTotalCS,2,".",",")."</span></td>";
						echo "<td></td>";
					echo "</tr>";
				}
				if ($totalSubtotalUSD>0){
					echo "<tr class='totalrow'>";
						echo "<td>Totales US$</td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".number_format($totalSubtotalUSD,2,".",",")."</span></td>";
						echo "<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".number_format($totalIvaUSD,2,".",",")."</span></td>";
						echo "<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".number_format($totalTotalUSD,2,".",",")."</span></td>";
						echo "<td></td>";
					echo "</tr>";
				}
			echo "</tbody>";
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
			$(this).number(true,0,'.',',');
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