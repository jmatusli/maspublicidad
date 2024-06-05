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
		//var userid=$('#InvoiceUserId').val();
		//var invoicedateday=$('#InvoiceInvoiceDateDay').val();
		//var invoicedatemonth=$('#InvoiceInvoiceDateMonth').val();
		//var invoicedateyear=$('#InvoiceInvoiceDateYear').val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>invoices/getNewInvoiceCode/',
			//data:{"userid":userid,"invoicedateday":invoicedateday,"invoicedatemonth":invoicedatemonth,"invoicedateyear":invoicedateyear},
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
				loadSalesOrder();
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	
	$('body').on('change','#InvoiceExchangeRate',function(){	
		loadSalesOrder();
	});
	
	$('body').on('change','#InvoiceBoolAnnulled',function(){	
		if ($(this).is(':checked')){
			$('#products').empty();
			$('#subtotal span.amountright').text('0');
			$('#iva span.amountright').text('0');
			$('#total span.amountright').text('0');
			$('#InvoiceAmountPaid').val('0');
			$('#InvoiceBoolPaid').prop('checked',false);
		}
		else {
			$('#InvoiceSalesOrderId').trigger('change');
		}
	});
	
	$('body').on('change','#InvoiceClientId',function(){	
		var clientid=$(this).children("option").filter(":selected").val();
		
		//MODIFIED 20160415 AS USER ID SHOULD NOT BE TAKEN INTO ACOUNT IN THIS CASE (PHONE CALL ALEJANDRO)
		//var userid=<?php echo $loggedUserId; ?>;
		// MODIFIED 20160807 TO INCORPORATE THE CHECK IF PRODUCTS SHOULD BE CHECKED ON READINESS
		var boolskipproductchecks=$('#InvoiceBoolSkipProductChecks').val();
		// MODIFIED 20160807 TO INCORPORATE THE CHECK IF PRODUCTS SHOULD BE CHECKED ON READINESS
		var invoiceid=0;
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>sales_orders/getsalesordersforclient/',
			data:{"clientid":clientid,"boolskipproductchecks":boolskipproductchecks,"invoiceid":invoiceid},
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
		$('div.invoice.salesorder').empty();
		$('div.invoice.salesorder').addClass('hidden');
	});
	
	$('body').on('change','#InvoiceSalesOrderId',function(){	
		loadSalesOrder();
	});

	function loadSalesOrder(){
		var salesorderid=$('#InvoiceSalesOrderId').children("option").filter(":selected").val();
		var selectedSalesOrders = [];
		$('#InvoiceSalesOrderId').children("option").filter(":selected").each(function() {
			selectedSalesOrders.push($(this).val());
        });
		if (salesorderid>0){
			loadProductsForSalesOrder(selectedSalesOrders);
			loadSalesOrderInfo(selectedSalesOrders);
			//setSalesOrderCurrency(selectedSalesOrders);
		}
	}
		
	function loadProductsForSalesOrder(selectedSalesOrders){
		var boolIVA=$('#InvoiceBoolIva').is(':checked');
		var boolskipproductchecks=$('#InvoiceBoolSkipProductChecks').val();
		var currencyid=$('#InvoiceCurrencyId').val();
		var invoiceid=0;
		var exchangerate=$('#InvoiceExchangeRate').val();
    var canSavePartialInvoice=<?php echo $canSavePartialInvoice?"1":"0"; ?>;
    $.ajax({
			url: '<?php echo $this->Html->url('/'); ?>salesOrders/getSalesOrderProducts/',
			data:{"selectedSalesOrders":selectedSalesOrders,"boolIVA":boolIVA,"boolskipproductchecks":boolskipproductchecks,"currencyid":currencyid,"exchangerate":exchangerate,"invoiceid":invoiceid,"canSavePartialInvoice":canSavePartialInvoice},
			cache: false,
			type: 'POST',
			success: function (products) {
				$('#products').html(products);
				calculateTotal();
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	function loadSalesOrderInfo(selectedSalesOrders){
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>sales_orders/getsalesorderinfo/',
			data:{"selectedSalesOrders":selectedSalesOrders},
			cache: false,
			type: 'POST',
			success: function (salesorderinfo) {
				$('div.salesorder').empty();
				$('div.salesorder').html(salesorderinfo);
				$('div.salesorder').removeClass('hidden');
				calculateTotal();
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	/*
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
				console.log(e);
				alert(e.responseText);
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
		var rowid=$(this).closest('tr').attr('row');
		loadProductPriceAndIva(rowid);
		var productname =$(this).find('div select option:selected').text();
		$(this).closest('tr').find('td.productdescription textarea').val(productname);
		calculateRow(rowid);
		calculateTotal();
	});	
	
	function loadProductPriceAndIva(rowid) {    
		var currentrow=$('#productos').find("[row='" + rowid + "']");
		var productid=currentrow.find('td.productid div select').val();
		var currencyid=$('#InvoiceCurrencyId').val();
		var dateday=$('#InvoiceInvoiceDateDay').val();
		var datemonth=$('#InvoiceInvoiceDateMonth').val();
		var dateyear=$('#InvoiceInvoiceDateYear').val();
		if (productid>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>products/getproductinfo/',
				data:{"productid":productid,"currencyid":currencyid,"dateday":dateday,"datemonth":datemonth,"dateyear":dateyear},
				cache: false,
				dataType:'json',
				type: 'POST',
				success: function (product) {
					currentrow.find('td.productunitprice div input').val(product['Product']['calculated_unit_price']);
					if (product['Product']['bool_no_iva']){
						currentrow.find('td.boolnoiva div input').prop('checked',true);
						currentrow.find('td.booliva div input').prop('checked',false);
						currentrow.find('td.booliva div input[type=checkbox]').attr('onclick','return false');
					}
					else {
						currentrow.find('td.boolnoiva div input').prop('checked',$('#QuotationBoolIva').is(':checked'));
					}
				},
				error: function(e){
					alert(e.responseText);
					console.log(e);
				}
			});
		}
		else {
			currentrow.find('td.productimage').empty();
		}
	}
	*/
	$('body').on('change','.productquantity',function(){	
		if (!$(this).find('div input').val()||isNaN($(this).find('div input').val())){
			$(this).find('div input').val(0);
		}
		else {
			var roundedValue=Math.round($(this).find('div input').val());
			var pendingQuantity=$(this).closest('tr').find('td.pendingquantity div input').val();
			if (roundedValue>pendingQuantity){
				$(this).find('div input').val(pendingQuantity);
				alert("La cantidad entregada no puede estar mayor que la cantidad pendiente");
			}
			else {
				$(this).find('div input').val(roundedValue);
			}
		}
		calculateRow($(this).closest('tr').attr('row'));
		calculateTotal();
	});	
	/*
	$('body').on('change','.productunitprice',function(){	
		if (!$(this).find('div input').val()||isNaN($(this).find('div input').val())){
			$(this).find('div input').val(0);
		}
		else {
			var roundedValue=roundToTwo($(this).find('div input').val());
			$(this).find('div input').val(roundedValue);
		}
		calculateRow($(this).closest('tr').attr('row'));
		calculateTotal();
	});	
	*/
	function calculateRow(rowid) {    
		var currentrow=$('#productos').find("[row='" + rowid + "']");
		
		var quantity=parseFloat(currentrow.find('td.productquantity div input').val());
		var unitprice=parseFloat(currentrow.find('td.productunitprice div input').val());
		
		var totalprice=quantity*unitprice;
		
		currentrow.find('td.producttotalprice div input').val(roundToTwo(totalprice));
	}
	
	$('body').on('change','#InvoiceBoolIva',function(){
		updateProductsForIva();
		calculateTotal();
	});
	
	function updateProductsForIva(){
		if ($('#InvoiceBoolIva').is(':checked')){
			$('#productos tr').each(function(){
				if (!$(this).find('td.boolnoiva div input[type=checkbox]').is(':checked')){
					$(this).find('td.booliva div input[type=checkbox]').prop('checked',true);
				}
			});
		}
		else {
			$('#productos td.booliva div input[type=checkbox]').prop('checked',false);
		}
	}
	/*
	$('body').on('change','td.booliva div input',function(){
		calculateTotal();
	});
	*/
	function calculateTotal(){
		var totalProductQuantity=0;
		var subtotalPrice=0;
		var ivaPrice=0
		var totalPrice=0
		$("#productos tbody tr:not(.hidden)").each(function() {
			var currentProductQuantity = $(this).find('td.productquantity div input');
			if (!isNaN(currentProductQuantity.val())){
				var currentQuantity = parseFloat(currentProductQuantity.val());
				totalProductQuantity += currentQuantity;
			}
			
			var currentProduct = $(this).find('td.producttotalprice div input');
			var currentPrice=0;
			if (!isNaN(currentProduct.val())){
				var currentPrice = parseFloat(currentProduct.val());
				subtotalPrice += currentPrice;
			}
			
			if ($(this).find('td.booliva div input').is(':checked')){
				$(this).find('td.iva div input').val(roundToTwo(0.15*currentPrice));
				ivaPrice+=roundToTwo(0.15*currentPrice);
			}
			else {
				$(this).find('td.iva div input').val(0);
			}
			
		});
		
		$('tr.totalrow.subtotal td.productquantity span').text(totalProductQuantity.toFixed(0));
		
		$('#subtotal span.amountright').text(subtotalPrice);
		$('tr.totalrow.subtotal td.totalprice div input').val(subtotalPrice.toFixed(2));
		
		$('#iva span.amountright').text(ivaPrice);
		$('tr.totalrow.iva td.totalprice div input').val(ivaPrice.toFixed(2));
		totalPrice=subtotalPrice + ivaPrice;
		$('#total span.amountright').text(totalPrice);
		$('tr.totalrow.total td.totalprice div input').val(totalPrice.toFixed(2));
		
		if (!$('#InvoiceBoolCredit').is(':checked')){
			$('#InvoiceAmountPaid').val(totalPrice);
			$('#InvoiceBoolPaid').prop('checked',true);
		}
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
		
		loadSalesOrder();
		/*
		// 20160807 CALL SALES ORDERS CHANGE RIGHT AWAY AND LET THEM HANDLE IT
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
		*/
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
		//displayCashReceiptCode();
		if ($(this).is (':checked')){
			//$('#InvoiceAmountPaid').val(0);
			$('#InvoiceBoolPaid').prop('checked',false);
		}
		else {
			//var totalamount=$('#InvoicePriceTotal').val();
			//$('#InvoiceAmountPaid').val(totalamount);
			$('#InvoiceBoolPaid').prop('checked',true);
		}
	});
	
	function displayCashReceiptCode(){
		if ($('#InvoiceBoolCredit').is(':checked')){
			$('#InvoiceCashReceiptCode').parent().removeClass('hidden');
		}
		else {
			$('#InvoiceCashReceiptCode').parent().addClass('hidden');
		}
	}
	
	$('body').on('change','#InvoiceAmountPaid',function(){	
		var totalamount=$('#InvoicePriceTotal').val();
		var amountpaid=$('#InvoiceAmountPaid').val();
		if (totalamount<=amountpaid){
			$('#InvoiceBoolPaid').prop('checked',true);
		}		
		else {
			$('#InvoiceBoolPaid').prop('checked',false);
		}
	});
	
	$(document).ready(function(){
		formatCurrencies();
		if (!$('#InvoiceSalesOrderId').val()==0){
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
	echo '<fieldset>';
		echo '<legend>'.__('Add Invoice').'</legend>';
		echo $this->Form->input('bool_skip_product_checks',['label'=>'Control de estado de productos', 'options'=>$productChecks,'default'=>$boolSkipProductChecks]); 
		echo $this->Form->submit(__('Refresh'),['name'=>'refresh', 'id'=>'refresh']); 
		echo '<div class="container-fluid">';
			echo '<div class="row">';
				echo '<div class="col-sm-8">';
					if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) { 
						echo $this->Form->input('user_id',['label'=>__('Ejecutivo de Ventas'),'default'=>$loggedUserId]);
					}
					else {
						echo $this->Form->input('user_id',['label'=>__('Ejecutivo de Ventas'),'default'=>$loggedUserId,'type'=>'hidden']);
					}
					echo $this->Form->input('client_id',['default'=>'0','empty'=>['0'=>'Seleccione Cliente']]);
					echo $this->Form->input('sales_order_id',['default'=>0,'multiple'=>true,'lines'=>5,'empty'=>['0'=>'Seleccione Orden de Venta']]);
					echo $this->Form->input('invoice_date',['dateFormat'=>'DMY','minYear'=>2014,'maxYear'=>date('Y')]);
          echo $this->Form->input('reference',['rows'=>2]);
					echo $this->Form->input('exchange_rate',['default'=>$exchangeRateInvoice]);
					echo $this->Form->input('invoice_code',['readonly'=>'readonly']);
					echo $this->Form->input('bool_annulled',['label'=>'Anulada','default'=>false]);			
					echo $this->Form->input('bool_iva',['default'=>true]);
					echo $this->Form->input('currency_id');
					echo $this->Form->input('bool_credit',['label'=>'Crédito?','default'=>false]);
					echo $this->Form->input('bool_paid',['label'=>'Pagada?','default'=>false]);
					echo $this->Form->input('InvoiceRemark.user_id',['label'=>'Vendedor','value'=>$loggedUserId,'type'=>'hidden']);
					echo $this->Form->input('InvoiceRemark.remark_text',['rows'=>2,'default'=>'Orden de Venta creada']);
					echo $this->Form->input('InvoiceRemark.action_type_id',['default'=>ACTION_TYPE_OTHER]);
				echo '</div>';
				echo '<div class="col-sm-4">';
					echo '<div class="salesorder">';
						echo '<dl style="width:100%;">';
							echo '<dt>Subtotal</dt>';
							echo '<dd id="subtotal"><span class="currency"></span><span class="amountright">0</span></dd>';
							echo '<dt>IVA</dt>';
							echo '<dd id="iva"><span class="currency"></span><span class="amountright">0</span></dd>';
							echo '<dt>Total</dt>';
							echo '<dd id="total"><span class="currency"></span><span class="amountright">0</span></dd>';
						echo '</dl>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</fieldset>';
	
	echo '<div id="products" style="font-size:0.9em;">';
	echo '</div>';
	
	//echo '<button id="addItem" type="button">'.__('Add Item').'</button>';
	echo $this->Form->submit(__('Submit'),['name'=>'submit', 'id'=>'submit','div'=>['class'=>'submit submission']]); 
	echo $this->Form->submit('Guardar y siguiente Factura',['name'=>'saveandnext', 'id'=>'saveandnext','div'=>['class'=>'submit saveandnext']]); 
	echo $this->Form->end(); 

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