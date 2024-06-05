<div class="invoices view">
<?php 
	echo "<h2>".__('Invoice')." ".$invoice['Invoice']['invoice_code'].($invoice['Invoice']['bool_annulled']?__(' (Anulada)'):"")."</h2>";
	echo "<dl>";
		$invoiceDateTime=new DateTime($invoice['Invoice']['invoice_date']);
		$lastPaymentDate=new DateTime($invoice['Invoice']['last_payment_date']);
		
		echo "<dt>".__('Invoice Date')."</dt>";
		echo "<dd>".$invoiceDateTime->format('d-m-Y')."</dd>";
		echo "<dt>".__('Invoice Code')."</dt>";
		echo "<dd>".h($invoice['Invoice']['invoice_code'])."</dd>";
    echo "<dt>".__('Reference')."</dt>";
		echo "<dd>".(empty($invoice['Invoice']['reference'])?'-':$invoice['Invoice']['reference'])."</dd>";
		echo "<dt>".__('Bool Annulled')."</dt>";
		echo "<dd>".h($invoice['Invoice']['bool_annulled']?__('Yes'):__('No'))."</dd>";
		echo "<dt>".__('Bool Iva')."</dt>";
		echo "<dd>".h($invoice['Invoice']['bool_iva']?__('Yes'):__('No'))."</dd>";
		echo "<dt>".__('Price Subtotal')."</dt>";
		echo "<dd>".$invoice['Currency']['abbreviation']." ".number_format($invoice['Invoice']['price_subtotal'],2,".",",")."</dd>";
		echo "<dt>".__('Price Iva')."</dt>";
		echo "<dd>".$invoice['Currency']['abbreviation']." ".number_format($invoice['Invoice']['price_iva'],2,".",",")."</dd>";
		echo "<dt>".__('Price Total')."</dt>";
		echo "<dd>".$invoice['Currency']['abbreviation']." ".number_format($invoice['Invoice']['price_total'],2,".",",")."</dd>";
		//echo "<dt>".__('Currency')."</dt>";
		//echo "<dd>".$this->Html->link($invoice['Currency']['abbreviation'], array('controller' => 'currencies', 'action' => 'view', $invoice['Currency']['id']))."</dd>";
		echo "<dt>".__('Exchange Rate')."</dt>";
		echo "<dd>".number_format($invoice['Invoice']['exchange_rate'],4,".",",")."</dd>";
		echo "<dt>".__('Crédito o Contado')."</dt>";
		echo "<dd>".h($invoice['Invoice']['bool_credit']?__('Crédito'):__('Contado'))."</dd>";
		echo "<dt>".__('Cash Receipt Code')."</dt>";
		if (!empty($invoice['Invoice']['cash_receipt_code'])){
			echo "<dd>".h($invoice['Invoice']['cash_receipt_code'])."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('Monto Pagado')."</dt>";
		echo "<dd>".$invoice['Currency']['abbreviation']." ".number_format($invoice['Invoice']['amount_paid'],2,".",",")."</dd>";
		echo "<dt>".__('Last Payment Date')."</dt>";
		echo "<dd>".$lastPaymentDate->format('d-m-Y')."</dd>";
		echo "<dt>".__('Pagado')."</dt>";
		echo "<dd>".h($invoice['Invoice']['bool_paid']?__('Yes'):__('No'))."</dd>";
		
		echo "<dt>".__('Quotation')."</dt>";
		if (!empty($invoice['InvoiceSalesOrder'])){
			//pr($invoice['InvoiceSalesOrder']);
			echo "<dd>";
			foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
				echo $this->Html->link($invoiceSalesOrder['SalesOrder']['Quotation']['quotation_code'], array('controller' => 'quotations', 'action' => 'view', $invoiceSalesOrder['SalesOrder']['Quotation']['id']))."<br/>";
			}
			echo "</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		
		echo "<dt>".__('Client')."</dt>";
		if (!empty($invoice['Client']['name'])){
			echo "<dd>".$this->Html->link($invoice['Client']['name'], array('controller' => 'clients', 'action' => 'view', $invoice['Client']['id']))."</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		echo "<dt>".__('User')."</dt>";
		if (!empty($invoice['InvoiceSalesOrder'])){
			//pr($invoice['InvoiceSalesOrder']);
			echo "<dd>";
			$userArray=array();
			foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
				if (!in_array($invoiceSalesOrder['User']['id'],$userArray)){
					$userArray[]=$invoiceSalesOrder['User']['id'];
					echo $this->Html->link($invoiceSalesOrder['User']['username'], array('controller' => 'users', 'action' => 'view', $invoiceSalesOrder['User']['id']))."<br/>";
				}
			}
			echo "</dd>";
		}
		else {
			echo "<dd>-</dd>";
		}
		
		echo "<dt>".__('Percentage Commission')."</dt>";
		echo "<dd>".number_format($invoice['Invoice']['percentage_commission'],2,".",",")." %</dd>";
		echo "<dt>".__('Amount Commission')."</dt>";
		echo "<dd>C$ ".number_format($invoice['Invoice']['amount_commission'],2,".",",")."</dd>";
	echo "</dl>";
?>
	<div class="related">
	<?php 
		if (!empty($invoice['InvoiceProduct'])){
			echo "<h3>".__('Productos en esta Factura')."</h3>";
			echo "<table cellpadding = '0' cellspacing = '0'>";
				echo "<tr>";
					echo "<th>".__('Product Id')."</th>";
					echo "<th>".__('Product Description')."</th>";
					echo "<th class='centered'>".__('Product Quantity')."</th>";
					echo "<th>".__('Product Unit Price')."</th>";
					echo "<th>".__('Product Total Price')."</th>";
					//echo "<th class='actions'>".__('Actions')."</th>";
				echo "</tr>";
				
			$currencyClass="";
			if ($invoice['Currency']['id']==CURRENCY_CS){
				$currencyClass="CScurrency";
			}
			elseif ($invoice['Currency']['id']==CURRENCY_USD){
				$currencyClass="USDcurrency";
			}
			$totalProductQuantity=0;
			foreach ($invoice['InvoiceProduct'] as $invoiceProduct){
				$totalProductQuantity+=$invoiceProduct['product_quantity'];
				echo "<tr>";
					echo "<td>".$this->Html->link($invoiceProduct['Product']['name'],['controller'=>'products','action'=>'view',$invoiceProduct['Product']['id']],['target'=>'_blank'])."</td>";
					echo "<td>".str_replace("\n","<br/>",$invoiceProduct['product_description'])."</td>";
					echo "<td class='amount centered'><span class='amount'>".$invoiceProduct['product_quantity']."</span></td>";
					echo "<td class='amount ".$currencyClass."'><span class='currency'></span><span class='amountright'>".$invoiceProduct['product_unit_price']."</td>";
					echo "<td class='amount ".$currencyClass."'><span class='currency'></span><span class='amountright'>".$invoiceProduct['product_total_price']."</span></td>";
					//echo "<td class='actions'>";
						//echo $this->Html->link(__('View'), array('controller' => 'invoice_products', 'action' => 'view', $invoiceProduct['id']));
						//echo $this->Html->link(__('Edit'), array('controller' => 'invoice_products', 'action' => 'edit', $invoiceProduct['id']));
						//echo $this->Form->postLink(__('Delete'), array('controller' => 'invoice_products', 'action' => 'delete', $invoiceProduct['id']), array(), __('Are you sure you want to delete # %s?', $invoiceProduct['id']));
					//echo "</td>";
				echo "</tr>";
			}
				echo "<tr class='totalrow'>";
					echo "<td>Subtotal</td>";
					echo "<td></td>";
					echo "<td class='centered'>".$totalProductQuantity."</td>";
					echo "<td></td>";
					echo "<td class='amount ".$currencyClass."'><span class='currency'></span><span class='amountright'>".$invoice['Invoice']['price_subtotal']."</span></td>";
					echo "<td></td>";
				echo "</tr>";
				echo "<tr class='totalrow'>";
					echo "<td>IVA</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td class='amount ".$currencyClass."'><span class='currency'></span><span class='amountright'>".$invoice['Invoice']['price_iva']."</span></td>";
					echo "<td></td>";
				echo "</tr>";
				echo "<tr class='totalrow'>";
					echo "<td>Total</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td class='amount ".$currencyClass."'><span class='currency'></span><span class='amountright'>".$invoice['Invoice']['price_total']."</span></td>";
					echo "<td></td>";
				echo "</tr>";
			echo "</table>";
		}
	?>
	</div> 
	<div class="related">
	<?php 
		if (!empty($invoice['VendorCommissionPayment'])){
			echo "<h3>".__('Pagos de Comisión para esta Factura')."</h3>";
			echo "<table cellpadding = '0' cellspacing = '0'>";
				echo "<tr>";
					echo "<th>".__('Payment Date')."</th>";
					echo "<th>".__('Commission Paid')."</th>";
				echo "</tr>";
				
			$currencyClass="CScurrency";
			$totalPayment=0;
			foreach ($invoice['VendorCommissionPayment'] as $payment){
				$totalPayment+=$payment['commission_paid'];
				echo "<tr>";
					$paymentDateTime=new DateTime($payment['payment_date']);
					echo "<td>".$paymentDateTime->format('d-m-Y')."</td>";
					echo "<td class='amount ".$currencyClass."'><span class='currency'></span><span class='amountright'>".$payment['commission_paid']."</td>";
				echo "</tr>";
			}
				echo "<tr class='totalrow'>";
					echo "<td>Subtotal</td>";
					echo "<td class='amount ".$currencyClass."'><span class='currency'></span><span class='amountright'>".$totalPayment."</span></td>";
				echo "</tr>";
			echo "</table>";
		}
	?>
	</div> 
</div>
<div class="actions">
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link('Imprimir', ['action' => 'imprimirVenta', $invoice['Invoice']['id']])."</li>";
			echo "<br/>";echo "<li>".$this->Html->link('Guardar como pdf', ['action' => 'viewPdf','ext'=>'pdf', $invoice['Invoice']['id'],$filename],['target'=>'_blank'])."</li>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Invoice'), ['action' => 'edit', $invoice['Invoice']['id']])."</li>";
			echo "<br/>";
		}
    if ($bool_invoice_editarReferencia_permission){
			echo "<li>".$this->Html->link('Editar Referencia Factura', ['action' => 'editarReferencia', $invoice['Invoice']['id']])."</li>";
			echo "<br/>";
		}
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink('Eliminar Factura', ['action' => 'delete', $invoice['Invoice']['id']], [], __('Está seguro que quiere eliminar factura #%s?', $invoice['Invoice']['invoice_code']))."</li>";
			echo "<br/>";
		}
		
		echo "<li>".$this->Html->link(__('List Invoices'), ['action' => 'index'])."</li>";
		echo "<li>".$this->Html->link(__('New Invoice'), ['action' => 'add'])."</li>";
		echo "<br/>";
		if ($bool_salesorder_index_permission){
			echo "<li>".$this->Html->link(__('List Sales Orders'),['controller' => 'sales_orders','action' => 'index'])."</li>";
		}
		if ($bool_salesorder_add_permission){
			echo "<li>".$this->Html->link(__('New Sales Order'), array('controller' => 'sales_orders', 'action' => 'add'))."</li>";
		}
		if ($bool_quotation_index_permission){
			echo "<li>".$this->Html->link(__('List Quotations'), array('controller' => 'quotations', 'action' => 'index'))."</li>";
		}
		if ($bool_quotation_add_permission){
			echo "<li>".$this->Html->link(__('New Quotation'), array('controller' => 'quotations', 'action' => 'add'))."</li>";
		}
		if ($bool_client_index_permission){
			echo "<li>".$this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index'))."</li>";
		}
		if ($bool_client_add_permission){
			echo "<li>".$this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add'))."</li>";
		}
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
		
		//var pdfUrl="<?php echo $this->Html->url(array('action'=>'viewPdf','ext'=>'pdf',$invoice['Invoice']['id'],$filename),true); ?>";
		//window.open(pdfUrl,'_blank');
	});
</script>