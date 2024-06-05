<script>
	$('body').on('change','#InvoiceInvoiceDateDay',function(){	
		getNewInvoiceCode();
		updateExchangeRate();
	});
	$('body').on('change','#InvoiceInvoiceDateMonth',function(){	
		getNewInvoiceCode();
		updateExchangeRate();
	});
	$('body').on('change','#InvoiceInvoiceDateYear',function(){	
		getNewInvoiceCode();
		updateExchangeRate();
	});
	function getNewInvoiceCode(){
		var userid=$('#InvoiceUserId').val();
		var invoicedateday=$('#InvoiceInvoiceDateDay').val();
		var invoicedatemonth=$('#InvoiceInvoiceDateMonth').val();
		var invoicedateyear=$('#InvoiceInvoiceDateYear').val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>invoices/getnewinvoicecode/',
			data:{"userid":userid,"invoicedateday":invoicedateday,"invoicedatemonth":invoicedatemonth,"invoicedateyear":invoicedateyear},
			cache: false,
			type: 'POST',
			success: function (invoicecode) {
				$('#InvoiceInvoiceCode').val(invoicecode);
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	function updateExchangeRate(){
		var selectedday=$('#InvoiceInvoiceDateDay').children("option").filter(":selected").val();
		var selectedmonth=$('#InvoiceInvoiceDateMonth').children("option").filter(":selected").val();
		var selectedyear=$('#InvoiceInvoiceDateYear').children("option").filter(":selected").val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>exchange_rates/getexchangerate/',
			data:{"selectedday":selectedday,"selectedmonth":selectedmonth,"selectedyear":selectedyear},
			cache: false,
			type: 'POST',
			success: function (exchangerate) {
				$('#InvoiceExchangeRate').val(exchangerate);
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	
	$('body').on('change','#InvoiceBoolAnnulled',function(){	
		if ($(this).is(':checked')){
			$('#products').empty();
			$('#subtotal span.amountright').text('0');
			$('#iva span.amountright').text('0');
			$('#total span.amountright').text('0');
		}
		else {
			$('#InvoiceSalesOrderId').trigger('change');
		}
	});
	
	$('body').on('change','#InvoiceClientId',function(){	
		var clientid=$(this).children("option").filter(":selected").val();
		//MODIFIED 20160415 AS USER ID SHOULD NOT BE TAKEN INTO ACOUNT IN THIS CASE (PHONE CALL ALEJANDRO)
		//var userid=<?php echo $user_id; ?>;
		var userid=0;
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>sales_orders/getsalesordersforclient/',
			data:{"clientid":clientid,"userid":userid},
			cache: false,
			type: 'POST',
			success: function (salesorders) {
				$('#InvoiceSalesOrderId').html(salesorders);
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
		$('#products').empty();
		$('div.invoice.righttop').empty();
		$('div.invoice.righttop').addClass('hidden');
		//$('#InvoiceInvoiceCode').val('');
	});
	
	$('body').on('change','#InvoiceSalesOrderId',function(){	
		var salesorderid=$(this).children("option").filter(":selected").val();
		if (salesorderid>0){
			loadProductsForSalesOrder(salesorderid);
			loadSalesOrderInfo(salesorderid);
			setSalesOrderCurrency(salesorderid);
		}
	});	
		
	function loadProductsForSalesOrder(salesorderid){
		var boolIVA=$('#InvoiceBoolIva').is(':checked');
		<?php 
			if ($role_id==ROLE_ADMIN){ 
				echo "var editpermissiondenied=0;";
			}
			else {
				echo "var editpermissiondenied=1;";
			}
		?>
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>sales_orders/getsalesorderproducts/',
			data:{"salesorderid":salesorderid,"boolIVA":boolIVA,"editpermissiondenied":editpermissiondenied},
			cache: false,
			type: 'POST',
			success: function (products) {
				$('#products').html(products);
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	function loadSalesOrderInfo(salesorderid){
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>sales_orders/getsalesorderinfo/',
			data:{"salesorderid":salesorderid},
			cache: false,
			type: 'POST',
			success: function (salesorderinfo) {
				$('div.righttop.salesorder').html(salesorderinfo);
				$('div.righttop.salesorder').removeClass('hidden');
			},
			error: function(e){
				$('div.righttop').html(e.responseText);
				console.log(e);
			}
		});
	}
	function setSalesOrderCurrency(salesorderid){
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>sales_orders/getsalesordercurrencyid/',
			data:{"salesorderid":salesorderid},
			cache: false,
			type: 'POST',
			success: function (salesordercurrencyid) {
				$('#InvoiceCurrencyId').val(salesordercurrencyid);
				updateCurrencies();
			},
			error: function(e){
				alert(e.responseText);
				console.log(e);
			}
			
		});
	}

	$('body').on('click','.removeItem',function(){	
		var tableRow=$(this).parent().parent().remove();
		calculateTotal();
	});	
	$('body').on('click','.addItem',function(){	
		var tableRow=$('#productos tbody tr.hidden:first');
		tableRow.removeClass("hidden");
	});	
	
	$('body').on('change','.productid',function(){	
		calculateRow($(this).closest('tr').attr('row'));
		calculateTotal();
	});	
	$('body').on('change','.productquantity',function(){	
		if (!$(this).find('div input').val()||isNaN($(this).find('div input').val())){
			$(this).find('div input').val(0);
		}
		calculateRow($(this).closest('tr').attr('row'));
		calculateTotal();
	});	
	$('body').on('change','.productunitprice',function(){	
		if (!$(this).find('div input').val()||isNaN($(this).find('div input').val())){
			$(this).find('div input').val(0);
		}
		calculateRow($(this).closest('tr').attr('row'));
		calculateTotal();
	});	
	
	function calculateRow(rowid) {    
		var currentrow=$('#productos').find("[row='" + rowid + "']");
		
		var quantity=parseFloat(currentrow.find('td.productquantity div input').val());
		var unitprice=parseFloat(currentrow.find('td.productunitprice div input').val());
		
		var totalprice=quantity*unitprice;
		
		currentrow.find('td.producttotalprice div input').val(roundToTwo(totalprice));
	}
	
	$('body').on('change','#InvoiceBoolIva',function(){
		calculateTotal();
	});
	
	function calculateTotal(){
		var booliva=$('#InvoiceBoolIva').is(':checked');
		var subtotalPrice=0;
		var ivaPrice=0
		var totalPrice=0
		$("#productos tbody tr:not(.hidden)").each(function() {
			var currentProduct = $(this).find('td.producttotalprice div input');
			if (!isNaN(currentProduct.val())){
				var currentPrice = parseFloat(currentProduct.val());
				subtotalPrice += currentPrice;
			}
		});
		$('#subtotal span.amount').text(subtotalPrice);
		$('tr.totalrow.subtotal td.totalprice div input').val(subtotalPrice);
		
		if (booliva){
			ivaPrice=roundToTwo(0.15*subtotalPrice);
		}
		$('#iva span.amount').text(ivaPrice);
		$('tr.totalrow.iva td.totalprice div input').val(ivaPrice);
		totalPrice=subtotalPrice + ivaPrice;
		$('#total span.amount').text(totalPrice);
		$('tr.totalrow.total td.totalprice div input').val(totalPrice);
		
		return false;
	}
	
	function updateCurrencies(){
		var currencyid=$('#InvoiceCurrencyId').val();
		if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('span.currency').text("US$");
		}
		else if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('span.currency').text("C$");
		}
	}
	
	$('body').on('change','#InvoiceCurrencyId',function(){	
		var currencyid=$(this).val();
		if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('span.currency').text("US$");
		}
		else if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('span.currency').text("C$");
		}
		// now update all prices
		var exchangerate=parseFloat($('#InvoiceExchangeRate').val());
		if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('td.productunitprice').each(function(){
				var originalprice= $(this).find('div input').val();
				var newprice=roundToTwo(originalprice/exchangerate);
				$(this).find('div input').val(newprice);
				//$(this).find('div input').trigger('change');
				//$(this).trigger('change');
				calculateRow($(this).closest('tr').attr('row'));
			});
		}
		else if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('td.productunitprice').each(function(){
				var originalprice= $(this).find('div input').val();
				var newprice=roundToTwo(originalprice*exchangerate);
				$(this).find('div input').val(newprice);
				//$(this).find('div input').trigger('change');
				//$(this).trigger('change');
				calculateRow($(this).closest('tr').attr('row'));
			});
		}
		calculateTotal();
	});	
	
	function formatCurrencies(){
		$("td.amount span.amountright").each(function(){
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,2);
		});
		var currencyid=$('#InvoiceCurrencyId').children("option").filter(":selected").val();
		if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('span.currency').text('C$ ');
		}
		else if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('span.currency').text('US$ ');			
		}
	}
	
	$('body').on('change','#InvoiceBoolCredit',function(){	
		displayCashReceiptCode();
	});
	
	function displayCashReceiptCode(){
		if ($('#InvoiceBoolCredit').is(':checked')){
			$('#InvoiceCashReceiptCode').parent().removeClass('hidden');
		}
		else {
			$('#InvoiceCashReceiptCode').parent().addClass('hidden');
		}
	}
	
	$(document).ready(function(){
		formatCurrencies();
		if ($('#InvoiceSalesOrderId').val()==0){
			$('div.righttop.invoice').addClass('hidden');
		}
		else {
			$('#InvoiceSalesOrderId').trigger('change');	
		}
		getNewInvoiceCode();
		updateExchangeRate();
		displayCashReceiptCode();
	});
</script>
<div class="invoices form fullwidth">
<?php
	echo $this->Form->create('Invoice'); 
	echo "<fieldset>";
		echo "<legend>".__('Add Invoice')."</legend>";
		echo "<div class='col-md-4'>";
		echo "</div>";
		echo $this->Form->input('user_id',array('label'=>__('Ejecutivo de Ventas'),'default'=>$this->Session->read('User.id'),'type'=>'hidden'));
		echo $this->Form->input('client_id',array('default'=>'0','empty'=>array('0'=>'Seleccione Cliente')));
		echo $this->Form->input('sales_order_id',array('default'=>'0','empty'=>array('0'=>'Seleccione Orden de Venta')));
		echo $this->Form->input('invoice_date',array('dateFormat'=>'DMY'));
		echo $this->Form->input('exchange_rate',array('default'=>$exchangeRateInvoice,'readonly'=>'readonly'));
		echo $this->Form->input('invoice_code',array('readonly'=>'readonly'));
		echo $this->Form->input('bool_annulled',array('checked'=>false));
		
		echo $this->Form->input('bool_iva',array('checked'=>true));
		echo $this->Form->input('currency_id');
		
		echo $this->Form->input('bool_credit',array('label'=>'Crédito?','checked'=>false));
		echo $this->Form->input('cash_receipt_code');
		echo $this->Form->input('amount_paid',array('label'=>'Monto Pagado (Abonos)'));
		echo $this->Form->input('bool_paid',array('label'=>'Pagada?','checked'=>false));
	echo "</fieldset>";
	
	echo "<div class='righttop'>";
		echo "<dl>";
			echo "<dt>Subtotal</dt>";
			echo "<dd id='subtotal'><span class='currency'></span><span class='amountright'>0</span></dd>";
			echo "<dt>IVA</dt>";
			echo "<dd id='iva'><span class='currency'></span><span class='amountright'>0</span></dd>";
			echo "<dt>Total</dt>";
			echo "<dd id='total'><span class='currency'></span><span class='amountright'>0</span></dd>";
		echo "</dl>";
	echo "</div>";
	echo "<div class='righttop salesorder'>";
	echo "</div>";
	
	echo "<div id='products' style='font-size:0.9em;'>";
	echo "</div>";
	
	//echo "<button id='addItem' type='button'>".__('Add Item')."</button>";
	
	echo $this->Form->end(__('Submit')); 

?>
</div>
<?php
/*
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Invoices'), array('action' => 'index')); ?></li>
		<br/>
	<?php
		if ($bool_salesorder_index_permission){
			echo "<li>".$this->Html->link(__('List Sales Orders'), array('controller' => 'sales_orders','action' => 'index'))."</li>";
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
	?>
	</ul>
</div>
*/
?>