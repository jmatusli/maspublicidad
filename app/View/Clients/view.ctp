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
<div class="clients view">
<?php 
	echo "<h2>".__('Client')." ".$client['Client']['name']."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($client['Client']['name'])."</dd>";
		echo "<dt>".__('RUC')."</dt>";
		if (!empty($client['Client']['ruc'])){
			echo "<dd>".h($client['Client']['ruc'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Address')."</dt>";
		if (!empty($client['Client']['address'])){
			echo "<dd>".h($client['Client']['address'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Phone')."</dt>";
		if (!empty($client['Client']['phone'])){
			echo "<dd>".h($client['Client']['phone'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Cell')."</dt>";
		if (!empty($client['Client']['cell'])){
			echo "<dd>".h($client['Client']['cell'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Bool Active')."</dt>";
		echo "<dd>".($client['Client']['bool_active']?__('Yes'):__('No'))."</dd>";
    echo "<dt>".__('Bool Generic')."</dt>";
		echo "<dd>".($client['Client']['bool_generic']?__('Yes'):__('No'))."</dd>";
		echo "<dt>".__('Bool Vip')."</dt>";
		echo "<dd>".($client['Client']['bool_vip']?__('Yes'):__('No'))."</dd>";
		echo "<dt>".__('Creado Por')."</dt>";
		//pr($client);
		if (!empty($client['CreatingUser']['id'])){
			echo "<dd>".h($client['CreatingUser']['first_name']." ".$client['CreatingUser']['last_name'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Client'), array('action' => 'edit', $client['Client']['id']))."</li>";
		}
		//echo "<li>".$this->Form->postLink(__('Delete Client'), array('action' => 'delete', $client['Client']['id']), array(), __('Are you sure you want to delete # %s?', $client['Client']['id']))."</li>";
		echo "<li>".$this->Html->link(__('List Clients'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Client'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if ($bool_contact_index_permission){
			echo "<li>".$this->Html->link(__('List Contacts'), array('controller' => 'contacts', 'action' => 'index'))."</li>";
		}
		if ($bool_contact_add_permission){
			echo "<li>".$this->Html->link(__('New Contact'), array('controller' => 'contacts', 'action' => 'add'))."</li>";
		}
		if ($bool_invoice_index_permission){
			echo "<li>".$this->Html->link(__('List Invoices'), ['controller' => 'invoices', 'action' => 'resumen'])."</li>";
		}
		if ($bool_invoice_add_permission){
			echo "<li>".$this->Html->link(__('New Invoice'), ['controller' => 'invoices', 'action' => 'crear'])."</li>";
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
	if (!empty($client['Contact'])){
		echo "<h3>".__('Contactos para este Cliente')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('First Name')."</th>";
				echo "<th>".__('Last Name')."</th>";
				echo "<th>".__('Phone')."</th>";
				echo "<th>".__('Cell')."</th>";
				echo "<th>".__('Email')."</th>";
				echo "<th>".__('Department')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($client['Contact'] as $contact){
			if ($contact['bool_active']){
				echo "<tr>";
			}
			else {
				echo "<tr class='italic'>";
			}
				echo "<td>".$contact['first_name']."</td>";
				echo "<td>".$contact['last_name'].($contact['bool_active']?"":" (Inactivo)")."</td>";
				echo "<td>".$contact['phone']."</td>";
				echo "<td>".$contact['cell']."</td>";
				echo "<td>".$contact['email']."</td>";
				echo "<td>".$contact['department']."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'contacts', 'action' => 'view', $contact['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'contacts', 'action' => 'edit', $contact['id']));
					//echo $this->Form->postLink(__('Delete'), array('controller' => 'contacts', 'action' => 'delete', $contact['id']), array(), __('Are you sure you want to delete # %s?', $contact['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
<div class="related">
<?php 
	if (!empty($client['Invoice'])){
		echo "<h3>".__('Facturas para este Cliente')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Invoice Date')."</th>";
					echo "<th>".__('Invoice Code')."</th>";
					echo "<th>".__('Quotation')."</th>";
					echo "<th>".__('User')."</th>";
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
			
				foreach ($client['Invoice'] as $invoice){
					$invoiceDateTime=new Datetime($invoice['invoice_date']);
					
					if ($invoice['currency_id']==CURRENCY_CS){
						$classCurrency=" class='CScurrency'";
						$totalSubtotalCS+=$invoice['price_subtotal'];
						$totalIvaCS+=$invoice['price_iva'];
						$totalTotalCS+=$invoice['price_total'];
					}
					elseif ($invoice['currency_id']==CURRENCY_USD){
						$classCurrency=" class='USDcurrency'";
						$totalSubtotalUSD+=$invoice['price_subtotal'];
						$totalIvaUSD+=$invoice['price_iva'];
						$totalTotalUSD+=$invoice['price_total'];
					}
					
					if ($invoice['bool_annulled']){
						echo "<tr class='italic'>";
					}
					else {
						echo "<tr>";
					}
						echo "<td>".$invoiceDateTime->format('d-m-Y')."</td>";
						echo "<td>".$this->Html->link($invoice['invoice_code'].($invoice['bool_annulled']?' (Anulada)':''),['controller'=>'invoices','action'=>'detalle',$invoice['id']])."</td>";
						echo "<td>";
						if (!empty($invoice['InvoiceSalesOrder'])){
							foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
								if (!empty($invoiceSalesOrder['SalesOrder']['Quotation'])){
									echo $this->Html->link($invoiceSalesOrder['SalesOrder']['Quotation']['quotation_code'],array('controller'=>'quotations','action'=>'view',$invoiceSalesOrder['SalesOrder']['Quotation']['id']))."<br/>";
								}
							}
						}
						echo "</td>";
						echo "<td>";
						foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
								if (!empty($invoiceSalesOrder['User'])){
									echo $this->Html->link($invoiceSalesOrder['User']['username'],array('controller'=>'users','action'=>'view',$invoiceSalesOrder['User']['id']))."<br/>";
								}
							}
						echo "</td>";
						echo "<td>".($invoice['bool_iva']?__('Yes'):__('No'))."</td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$invoice['price_subtotal']."</td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$invoice['price_iva']."</td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$invoice['price_total']."</td>";
						echo "<td class='actions'>";
							echo $this->Html->link(__('View'), array('controller' => 'invoices', 'action' => 'view', $invoice['id']));
							echo $this->Html->link(__('Edit'), array('controller' => 'invoices', 'action' => 'edit', $invoice['id']));
							//echo $this->Form->postLink(__('Delete'), array('controller' => 'invoices', 'action' => 'delete', $invoice['id']), array(), __('Are you sure you want to delete # %s?', $invoice['id']));
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
<div class="related">
<?php 
	if (!empty($client['Quotation'])){
		echo "<h3>".__('Cotizaciones para este Cliente')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Quotation Date')."</th>";
					echo "<th>".__('Quotation Code')."</th>";
					echo "<th>".__('User')."</th>";
					echo "<th>".__('Contact')."</th>";
					
					echo "<th>".__('Bool Active')."</th>";
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
			
				foreach ($client['Quotation'] as $quotation){
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
						if (!empty($quotation['Contact'])){
							echo "<td>".$this->Html->link($quotation['Contact']['fullname'],array('controller'=>'contacts','action'=>'view',$quotation['Contact']['id']))."</td>";
						}
						else {
							echo "<td>-</td>";
						}
						echo "<td>".((!$quotation['bool_rejected']&&empty($quotation['SalesOrder']))?__('Yes'):__('No'))."</td>";
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
<div class="related">
<?php 
	if(!empty($client['ClientUser'])){
		echo "<h3>".__('Vendedores asociados con este Cliente')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
      $tableHeader="";
      $tableHeader.="<thead>";
        $tableHeader.="<tr>";
          $tableHeader.="<th>".__('Username')."</th>";
          $tableHeader.="<th>".__('First Name')."</th>";
          $tableHeader.="<th>".__('Last Name')."</th>";
          $tableHeader.="<th>".__('Email')."</th>";
          $tableHeader.="<th>".__('Phone')."</th>";
          $tableHeader.="<th style='width:15%;'>Historial de Asignaciones</th>";
          $tableHeader.="<th class='actions'>".__('Actions')."</th>";
        $tableHeader.="</tr>";
      $tableHeader.="</thead>";
      echo $tableHeader;
      $tableBody="";
      $tableBody.="<tbody>";
      foreach ($uniqueUsers as $user){
        //pr($clientUser);
        $tableBody.=($user['ClientUser'][0]['bool_assigned']?"<tr>":"<tr class='italic'>");
          $tableBody.="<td>".$user['User']['username']."</td>";
          $tableBody.="<td>".$user['User']['first_name']."</td>";
          $tableBody.="<td>".$user['User']['last_name']."</td>";
          $tableBody.="<td>".$user['User']['email']."</td>";
          $tableBody.="<td>".$user['User']['phone']."</td>";
          $tableBody.="<td>";
          foreach ($user['ClientUser'] as $clientUser){
            //pr($clientUser);
            $assignmentDateTime=new DateTime($clientUser['assignment_datetime']);
            $tableBody.=($clientUser['bool_assigned']?"Asignado":"Desasignado")." el ".($assignmentDateTime->format('d-m-Y H:i:s'))."<br>";
          }  
          $tableBody.="</td>";
          $tableBody.="<td class='actions'>";
            $tableBody.=$this->Html->link(__('View'), array('controller' => 'users', 'action' => 'view', $user['User']['id']));
            $tableBody.=($bool_user_edit_permission?$this->Html->link(__('Edit'), array('controller' => 'users', 'action' => 'edit', $user['User']['id'])):"");
          $tableBody.="</td>";
        $tableBody.="</tr>";
      }
      $tableBody.="</tbody>";
      echo $tableBody;
		echo "</table>";
	}
?>
</div>