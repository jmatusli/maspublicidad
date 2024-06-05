<div class="quotations view">
<?php 
	echo "<h2>".__('Quotation')." ".$quotation['Quotation']['quotation_code']."</h2>";
	echo "<div class='container-fluid'>";
		echo "<div class='rows'>";	
			echo "<div class='col-md-6'>";
				echo "<dl>";
					$quotationDate=new DateTime($quotation['Quotation']['quotation_date']);
					$rejectedWarning="";
					if (date('Y-m-d')>$quotation['Quotation']['due_date']){
						$rejectedWarning="<div class='redfatwarning'> Se debe marcar la cotización como caída porque ya venció; para cálculos la cotización se considera como caída</div>";
					}
					$dueDate=new DateTime($quotation['Quotation']['due_date']);
					echo "<dt>".__('Quotation Date')."</dt>";
					echo "<dd>".$quotationDate->format('d-m-Y')."</dd>";
					echo "<dt>".__('Quotation Code')."</dt>";
					echo "<dd>".h($quotation['Quotation']['quotation_code'])."</dd>";		
					echo "<dt>".__('Caída?')."</dt>";
					echo "<dd>".(($quotation['Quotation']['bool_rejected'])?__('Yes'):__('No'))."</dd>";
					if (!empty($rejectedWarning)){
						// echo $rejectedWarning;
					}
					echo "<dt>".__('Ejecutivo de Venta')."</dt>";
					echo "<dd>".$this->Html->link($quotation['User']['username'], array('controller' => 'users', 'action' => 'view', $quotation['User']['id']),array('target'=>'_blank'))."</dd>";
					echo "<dt>".__('Client')."</dt>";
					echo "<dd>".$this->Html->link($quotation['Client']['name'], array('controller' => 'clients', 'action' => 'view', $quotation['Client']['id']),array('target'=>'_blank'))."</dd>";
					echo "<dt>".__('Contact')."</dt>";
					if (!empty($quotation['Contact']['id'])){
						echo "<dd>".$this->Html->link($quotation['Contact']['fullname'], array('controller' => 'contacts', 'action' => 'view', $quotation['Contact']['id']),array('target'=>'_blank'))."</dd>";
					}
					else {
						echo "<dd>-</dd>";
					}
					echo "<dt>".__('Due Date')."</dt>";
					echo "<dd>".$dueDate->format('d-m-Y')."</dd>";
					echo "<dt>".__('Tiempo de Entrega')."</dt>";
					if (!empty($quotation['Quotation']['delivery_time'])){
						echo "<dd>".$quotation['Quotation']['delivery_time']."</dd>";
					}
					else {
						echo "<dd>-</dd>";
					}
					echo "<dt>".__('Forma de Pago')."</dt>";
					if (!empty($quotation['Quotation']['payment_form'])){
						echo "<dd>".$quotation['Quotation']['payment_form']."</dd>";
					}
					else {
						echo "<dd>-</dd>";
					}
					echo "<dt>".__('Bool Iva')."</dt>";
					echo "<dd>".($quotation['Quotation']['bool_iva']?__('Yes'):__('No'))."</dd>";
					echo "<dt>".__('Price Subtotal')."</dt>";
					echo "<dd>".$quotation['Currency']['abbreviation']." ".($quotation['Quotation']['price_subtotal'])."</dd>";
					echo "<dt>".__('Price Iva')."</dt>";
					echo "<dd>".$quotation['Currency']['abbreviation']." ".($quotation['Quotation']['price_iva'])."</dd>";
					echo "<dt>".__('Price Total')."</dt>";
					echo "<dd>".$quotation['Currency']['abbreviation']." ".($quotation['Quotation']['price_total'])."</dd>";
					echo "<dt>".__('Observaciones para pdf')."</dt>";
					if (!empty($quotation['Quotation']['observations'])){
						echo "<dd>".$quotation['Quotation']['observations']."</dd>";
					}
					else {
						echo "<dd>-</dd>";
					}
					echo "<dt>".__('Archivos para Cotización')."</dt>";
					echo "<dd>";
					if (!empty($quotation['QuotationImage'])){
						foreach ($quotation['QuotationImage'] as $quotationImage){
							$url=$quotationImage['url_image'];
							echo "<a href='".($this->Html->url('/').$url)."' target='_blank'>".substr($url,strrpos($url,"/")+1)."</a><br/>";
						}
					}
					else {
						echo "<dd>-</dd>";
					}
					echo "</dd>";
				echo "</dl>";
			echo "</div>";
			echo "<div class='col-md-6'>";
				if (!empty($quotation['QuotationRemark'])){
					echo "<table>";
						echo "<thead>";
							echo "<tr>";
								echo "<th>Fecha</th>";
								echo "<th>Vendedor</th>";
								echo "<th>Remarca</th>";
							echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						foreach ($quotation['QuotationRemark'] as $quotationRemark){
							$remarkDateTime=new DateTime($quotationRemark['remark_datetime']);
							echo "<tr>";
								echo "<td>".$remarkDateTime->format('d-m-Y H:i')."</td>";
								echo "<td>".$quotationRemark['User']['username']."</td>";
								echo "<td>".$quotationRemark['remark_text']."</td>";
							echo "</tr>";
						}
						echo "</tbody>";
					echo "</table>";
				}
			echo "</div>";
		echo "</div>";
	echo "</div>";	
				
?> 
</div>
<div class='actions'>
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('Guardar como pdf'), array('action' => 'viewPdf','ext'=>'pdf', $quotation['Quotation']['id'],$fileName),array('target'=>'_blank','class'=>'pdflink'))."</li>";
		echo "<li>".$this->Html->link(__('Enviar Cotización'), array('controller'=>'system_emails','action' => 'add', $quotation['Quotation']['id']),array('target'=>'_blank'))."</li>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Quotation'), array('action' => 'edit', $quotation['Quotation']['id']))."</li>";
			echo "<br/>";
		}
		else {
			if ($bool_edit_forbidden_because_salesorder_authorized){
				echo "<p class='comment'>No se puede editar porque la orden de venta correspondiente ya está autorizada.  Para editar la cotización, marque la orden de venta como no autorizada.</p>";
			}
		}
		
		//
		echo "<li>".$this->Html->link(__('List Quotations'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Quotation'), array('action' => 'add'))."</li>";
		echo "<br/>";
		if (!$quotation['Quotation']['bool_sales_order_present']){
			echo "<li>".$this->Html->link(__('Generar Orden de Venta'), array('controller'=>'sales_orders','action' => 'add', $quotation['Quotation']['id']))."</li>";
			echo "<br/>";
		}
		if ($bool_client_index_permission){
			echo "<li>".$this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index'))."</li>";
		}
		if ($bool_client_add_permission){
			echo "<li>".$this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add'))."</li>";
		}
		if ($bool_salesorder_index_permission){
			echo "<li>".$this->Html->link(__('List Sales Orders'), array('controller' => 'sales_orders', 'action' => 'index'))."</li>";
		}
		if ($bool_salesorder_add_permission){
			echo "<li>".$this->Html->link(__('New Sales Order'), array('controller' => 'sales_orders', 'action' => 'add'))."</li>";
		}
		if ($bool_invoice_index_permission){
			echo "<li>".$this->Html->link(__('List Invoices'), ['controller' => 'invoices', 'action' => 'resumen'])."</li>";
		}
		if ($bool_invoice_add_permission){
			echo "<li>".$this->Html->link(__('New Invoice'), ['controller' => 'invoices', 'action' => 'crear'])."</li>";
		}
    
    if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Eliminar Cotización'), array('action' => 'delete', $quotation['Quotation']['id']), array(), __('Está seguro que quiere eliminar la cotización #%s?', $quotation['Quotation']['quotation_code']))."</li>";
			echo "<br/>";
		}
	echo "</ul>";
?>
</div>
<div class="related">
<?php 
	if (!empty($quotation['QuotationProduct'])){
		echo "<h3>".__('Related Quotation Products')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th style='width:10%;'>".__('Product Id')."</th>";
				echo "<th>".__('Imagen')."</th>";
				echo "<th>".__('Observación')."</th>";
				echo "<th>".__('T.de Entrega')."</th>";
				echo "<th style='width:10%;'>".__('Product Quantity')."</th>";
				echo "<th style='width:10%;'>".__('Product Unit Price')."</th>";
				echo "<th style='width:10%;'>".__('Product Total Price')."</th>";
				echo "<th>".__('IVA?')."</th>";
				//echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		$totalProductQuantity=0;	
		foreach ($quotation['QuotationProduct'] as $quotationProduct){
			$totalProductQuantity+=$quotationProduct['product_quantity'];
			if ($quotationProduct['currency_id']==CURRENCY_CS){
				$classCurrency=" class='CScurrency'";
			}
			elseif ($quotationProduct['currency_id']==CURRENCY_USD){
				$classCurrency=" class='USDcurrency'";
			}
			echo "<tr>";
				echo "<td>".$this->Html->link($quotationProduct['Product']['name'],array('controller'=>'products','action'=>'view',$quotationProduct['Product']['id']),array('target'=>'blank'))."</td>";
				if (!empty($quotationProduct['Product']['url_image'])){
					$url=$quotationProduct['Product']['url_image'];
					$productimage=$this->App->assetUrl($url);
					echo "<td><img src='".$productimage."' class='smallimage'></img></td>";
				}
				else {
					echo "<td></td>";
				}
				echo "<td>".str_replace("\n","<br/>",$quotationProduct['product_description'])."</td>";
				echo "<td>".$quotationProduct['delivery_time']."</td>";
				echo "<td><span class='amountright'>".$quotationProduct['product_quantity']."</span></td>";
				echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$quotationProduct['product_unit_price']."</span></td>";
				echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$quotationProduct['product_total_price']."</span></td>";
				echo "<td>".($quotationProduct['bool_iva']?__('Yes'):__('No'))."</td>";
				//echo "<td class='actions'>";
				//	echo $this->Html->link(__('View'), array('controller' => 'quotation_products', 'action' => 'view', $quotationProduct['id']));
				//	echo $this->Html->link(__('Edit'), array('controller' => 'quotation_products', 'action' => 'edit', $quotationProduct['id']));
					//echo $this->Form->postLink(__('Delete'), array('controller' => 'quotation_products', 'action' => 'delete', $quotationProduct['id']), array(), __('Are you sure you want to delete # %s?', $quotationProduct['id']));
				//echo "</td>";
			echo "</tr>";
		}
			echo "<tr class='totalrow'>";
				echo "<td>Subtotal</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td><span class='amountright'>".$totalProductQuantity."</span></td>";
				echo "<td></td>";
				echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".number_format($quotation['Quotation']['price_subtotal'],2,".",",")."</span></td>";
				echo "<td></td>";
			echo "</tr>";
			echo "<tr'>";
				echo "<td>IVA</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".number_format($quotation['Quotation']['price_iva'],2,".",",")."</span></td>";
				echo "<td></td>";
			echo "</tr>";
			echo "<tr class='totalrow'>";
				echo "<td>Total</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".number_format($quotation['Quotation']['price_total'],2,".",",")."</span></td>";
				echo "<td></td>";
			echo "</tr>";
		echo "</table>";
	}
?>
</div>
<div class="related">
<?php 
	if (!empty($quotation['SalesOrder'])){
		echo "<h3>".__('Ordenes de Venta para esta Cotización')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Sales Order Date')."</th>";
					echo "<th>".__('Sales Order Code')."</th>";
					echo "<th>".__('Subtotal')."</th>";
					echo"<th class='actions'>".__('Actions')."</th>";
				echo "</tr>";
			echo "</thead>";
				
			echo "<tbody>";
				$totalSubtotalCS=0;
				$totalSubtotalUSD=0;
				foreach ($quotation['SalesOrder'] as $salesOrder){
					$salesOrderDateTime= new DateTime($salesOrder['sales_order_date']);
					
					if ($salesOrder['currency_id']==CURRENCY_CS){
						$classCurrency=" class='CScurrency'";
						$totalSubtotalCS+=$salesOrder['price_subtotal'];
					}
					elseif ($salesOrder['currency_id']==CURRENCY_USD){
						$classCurrency=" class='USDcurrency'";
						$totalSubtotalUSD+=$salesOrder['price_subtotal'];
					}
					
					if ($salesOrder['bool_annulled']){
						echo "<tr class='italic'>";
					}
					else {
						echo "<tr>";
					}
						echo "<td>".$salesOrderDateTime->format('d-m-Y')."</td>";
						echo "<td>".$this->Html->Link($salesOrder['sales_order_code'].($salesOrder['bool_annulled']?' (Anulada)':''),array('controller'=>'sales_orders','action'=>'view',$salesOrder['id']))."</td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$salesOrder['price_subtotal']."</td>";
					
						echo "<td class='actions'>";
							echo $this->Html->link(__('View'), array('controller' => 'sales_orders', 'action' => 'view', $salesOrder['id']));
							echo $this->Html->link(__('Edit'), array('controller' => 'sales_orders', 'action' => 'edit', $salesOrder['id']));
							//echo $this->Form->postLink(__('Delete'), array('controller' => 'sales_orders', 'action' => 'delete', $salesOrder['id']), array(), __('Are you sure you want to delete # %s?', $salesOrder['id']));
						echo "</td>";
					echo "</tr>";
				}
				if ($totalSubtotalCS>0){
					echo "<tr class='totalrow'>";
						echo "<td>Totales C$</td>";
						echo "<td></td>";
						echo "<td class='CScurrency'><span class='currency'></span><span class='amountright'>".number_format($totalSubtotalCS,2,".",",")."</span></td>";
						echo "<td></td>";
					echo "</tr>";
				}
				if ($totalSubtotalUSD>0){
					echo "<tr class='totalrow'>";
						echo "<td>Totales US$</td>";
						echo "<td></td>";
						echo "<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".number_format($totalSubtotalUSD,2,".",",")."</span></td>";
						echo "<td></td>";
					echo "</tr>";
				}
			echo "<tbody>";
		echo "</table>";
	}
?>
</div>
<div class="related">
<?php 
	//pr($quotation['SalesOrder']);
	if (!empty($quotation['SalesOrder'][0]['InvoiceSalesOrder'])){
		echo "<h3>".__('Facturas para esta Cotización')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Invoice Date')."</th>";
					echo "<th>".__('Invoice Code')."</th>";
					echo "<th>".__('IVA')."</th>";
					echo "<th>".__('Price Subtotal')."</th>";
					echo "<th>".__('Price Iva')."</th>";
					echo "<th>".__('Price Total')."</th>";
					//echo "<th>".__('Bool Annulled')."</th>";
					//echo "<th>".__('Client Id')."</th>";
					//echo "<th>".__('User Id')."</th>";
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
				foreach ($quotation['SalesOrder'][0]['InvoiceSalesOrder'] as $invoiceSalesOrder){
					$invoiceDateTime=new DateTime($invoiceSalesOrder['Invoice']['invoice_date']);
					
					if ($invoiceSalesOrder['Invoice']['currency_id']==CURRENCY_CS){
						$classCurrency=" class='CScurrency'";
						$totalSubtotalCS+=$invoiceSalesOrder['Invoice']['price_subtotal'];
						$totalIvaCS+=$invoiceSalesOrder['Invoice']['price_iva'];
						$totalTotalCS+=$invoiceSalesOrder['Invoice']['price_total'];
					}
					elseif ($invoiceSalesOrder['Invoice']['currency_id']==CURRENCY_USD){
						$classCurrency=" class='USDcurrency'";
						$totalSubtotalUSD+=$invoiceSalesOrder['Invoice']['price_subtotal'];
						$totalIvaUSD+=$invoiceSalesOrder['Invoice']['price_iva'];
						$totalTotalUSD+=$invoiceSalesOrder['Invoice']['price_total'];
					}
					if ($invoiceSalesOrder['Invoice']['bool_annulled']){
						echo "<tr class='italic'>";
					}
					else {
						echo "<tr>";
					}
						echo "<td>".$invoiceDateTime->format('d-m-Y')."</td>";
						echo "<td>".$this->Html->link($invoiceSalesOrder['Invoice']['invoice_code'].($invoiceSalesOrder['Invoice']['bool_annulled']?' (Anulada)':''),['controller'=>'invoices','action'=>'detalle',$invoiceSalesOrder['Invoice']['id']])."</td>";
						echo "<td>".($invoiceSalesOrder['Invoice']['bool_iva']?__('Yes'):__('No'))."</td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$invoiceSalesOrder['Invoice']['price_subtotal']."</td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$invoiceSalesOrder['Invoice']['price_iva']."</td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$invoiceSalesOrder['Invoice']['price_total']."</td>";
						//echo "<td>".$invoiceSalesOrder['Invoice']['bool_annulled']."</td>";
						//echo "<td>".$invoiceSalesOrder['Invoice']['client_id']."</td>";
						//echo "<td>".$invoiceSalesOrder['Invoice']['user_id']."</td>";
						
						
						echo "<td class='actions'>";
							echo $this->Html->link(__('Edit'), ['controller' => 'invoices', 'action' => 'editar', $invoiceSalesOrder['Invoice']['id']]);
						echo "</td>";
					echo "</tr>";
				}
				if ($totalSubtotalCS>0){
					echo "<tr class='totalrow'>";
						echo "<td>Totales C$</td>";
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
<div class='related'>
<?php
	echo "<dl>";
		echo "<dt>".__('Imagenes en Pdf?')."</dt>";
		echo "<dd>".($quotation['Quotation']['bool_print_images']?"Si":"No")."</dd>";
		echo "<dt>".__('Tiempos de entrega en Pdf?')."</dt>";
		echo "<dd>".($quotation['Quotation']['bool_print_delivery_time']?"Si":"No")."</dd>";
		
		echo "<dt>".__('Remarca sobre entrega')."</dt>";
		if (!empty($quotation['Quotation']['remark_delivery'])){
			echo "<dd>".$quotation['Quotation']['remark_delivery']."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Remarca sobre cheque')."</dt>";
		if (!empty($quotation['Quotation']['remark_cheque'])){
			echo "<dd>".$quotation['Quotation']['remark_cheque']."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Remarca sobre elaboración')."</dt>";
		if (!empty($quotation['Quotation']['remark_elaboration'])){
			echo "<dd>".$quotation['Quotation']['remark_elaboration']."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		
		echo "<dt>".__('Persona quien autoriza')."</dt>";
		if (!empty($quotation['Quotation']['authorization'])){
			echo "<dd>".$quotation['Quotation']['authorization']."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		
	echo "</dl>";
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
		
		//var pdfUrl="<?php echo $this->Html->url(array('action'=>'viewPdf','ext'=>'pdf',$quotation['Quotation']['id'],$fileName),true); ?>";
		//window.open(pdfUrl,'_blank');
	});
</script>