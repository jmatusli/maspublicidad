<script>
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
		loadClientData(clientid);
		
    var boolskipproductchecks=$('#InvoiceBoolSkipProductChecks').val();
		var invoiceid=0;
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>sales_orders/getSalesOrdersForClient/',
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
  
  var jsClientDataByClient = <?php echo json_encode($clientDataByClient); ?>;
  function loadClientData(clientId){
    if (clientId == 0){
        $('#InvoiceClientGeneric').val(0);
        $('#InvoiceClientName').val('');
        $('#InvoiceClientPhone').val('');
        $('#InvoiceClientCell').val('');
        $('#InvoiceClientEmail').val('');
        $('#InvoiceClientRuc').val('');
        $('#InvoiceClientAddress').val('');
    }
    else {
      var selectedClient=jsClientDataByClient[clientId];
      if (selectedClient['bool_generic']){
        $('#InvoiceClientGeneric').val(1);
        $('#InvoiceClientName').val('');
        $('#InvoiceClientPhone').val('');
        $('#InvoiceClientCell').val('');
        $('#InvoiceClientEmail').val('');
        $('#InvoiceClientRuc').val('');
        $('#InvoiceClientAddress').val('');
        
      }
      else {
        $('#InvoiceClientGeneric').val(0);
        $('#InvoiceClientName').val(selectedClient.name);
        $('#InvoiceClientPhone').val(selectedClient.phone);
        $('#InvoiceClientCell').val(selectedClient.cell);
        $('#InvoiceClientEmail').val(selectedClient.email);
        $('#InvoiceClientRuc').val(selectedClient.ruc);
        $('#InvoiceClientAddress').val(selectedClient.address);
      }
    }
  }
		
	function loadProductsForSalesOrder(selectedSalesOrders){
		var boolIVA=$('#InvoiceBoolIva').is(':checked');
		var boolskipproductchecks=$('#InvoiceBoolSkipProductChecks').val();
		var currencyid=$('#InvoiceCurrencyId').val();
		var invoiceid=<?php echo $invoiceid; ?>;
    var currentInvoiceId=<?php echo $invoiceid; ?>;
		var exchangerate=$('#InvoiceExchangeRate').val();
    var canSavePartialInvoice=<?php echo $canSavePartialInvoice?"1":"0"; ?>;
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>salesOrders/getSalesOrderProducts/',
			data:{"selectedSalesOrders":selectedSalesOrders,"boolIVA":boolIVA,"boolskipproductchecks":boolskipproductchecks,"currencyid":currencyid,"exchangerate":exchangerate,"invoiceid":invoiceid,"currentInvoiceId":currentInvoiceId,"canSavePartialInvoice":canSavePartialInvoice},
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
			url: '<?php echo $this->Html->url('/'); ?>salesOrders/getSalesOrderInfo/',
			data:{"selectedSalesOrders":selectedSalesOrders,"currentInvoiceId":<?php echo $this->request->data['Invoice']['id']; ?>},
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
	*/
	/*
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
	*/
	/*
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
			var totalamount=$('#InvoicePriceTotal').val();
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
  
	var jsGenericClientIds=<?php echo json_encode($genericClientIds); ?>; 
	
	$(document).ready(function(){
		formatCurrencies();
		displayCashReceiptCode();
		
		var salesorderid=$('#InvoiceSalesOrderId').children("option").filter(":selected").val();
		var selectedSalesOrders = [];
		$('#InvoiceSalesOrderId').children("option").filter(":selected").each(function() {
			selectedSalesOrders.push($(this).val());
        });
		if (salesorderid>0){
      loadSalesOrderInfo(selectedSalesOrders);
		}
    
    var clientId=$('#InvoiceClientId').val();
    if (jQuery.inArray(clientId,Object.values(jsGenericClientIds)) != -1){
      $('#InvoiceClientGeneric').val(1);
    }
    else {
      $('#InvoiceClientGeneric').val(0);
    }
		
		$('select.fixed option:not(:selected)').attr('disabled', true);
	});
</script>
<div class="invoices form fullwidth">
<?php 
	echo $this->Form->create('Invoice'); 
	echo "<fieldset>";
		echo '<legend>'.__('Edit Invoice').' '.$this->request->data['Invoice']['invoice_code'].'</legend>';
		echo $this->Form->input('bool_skip_product_checks',['label'=>'Control de estado de productos', 'options'=>$productChecks,'onclick'=>'return false']); 
		echo $this->Form->submit(__('Refresh'),['name'=>'refresh', 'id'=>'refresh']); 
		echo "<div class='container-fluid'>";
			echo "<div class='row'>";
				echo "<div class='col-sm-4'>";
					echo $this->Form->input('id');
					if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) {
						echo $this->Form->input('user_id',['label'=>'Ejecutivo de Ventas']);
					}
					else {
						echo $this->Form->input('user_id',['label'=>'Ejecutivo de Ventas','type'=>'hidden']);
					}
					echo $this->Form->input('sales_order_id',array('multiple'=>true,'lines'=>5,'value'=>$selectedSalesOrderArray,'empty'=>array('0'=>'Seleccione Orden de Venta')));
					echo $this->Form->input('invoice_date',['dateFormat'=>'DMY','minYear'=>2014,'maxYear'=>date('Y')]);
					echo $this->Form->input('reference',['rows'=>2]);
          echo $this->Form->input('exchange_rate');
					echo $this->Form->input('invoice_code');
					echo $this->Form->input('bool_annulled');
					echo $this->Form->input('bool_iva');
					echo $this->Form->input('currency_id');
					echo $this->Form->input('bool_credit',array('label'=>'Crédito?'));
					echo $this->Form->input('bool_paid',array('label'=>'Pagada?'));
					echo $this->Form->input('InvoiceRemark.user_id',array('label'=>'Vendedor','value'=>$loggedUserId,'type'=>'hidden'));
					echo $this->Form->input('InvoiceRemark.remark_text',array('rows'=>2,'default'=>''));
					echo $this->Form->input('InvoiceRemark.action_type_id',array('default'=>ACTION_TYPE_OTHER));
				echo "</div>";
        echo '<div class="col-sm-4">';
          echo '<h2>Cliente</h2>';
          echo $this->Form->input('client_id',['label'=>'Cliente registrado','empty'=>['0'=>'-- Cliente --']]);
          
          echo $this->Form->input('client_generic',['type'=>'hidden','default'=>0]);
          echo $this->Form->input('client_name',['label'=>'Nombre Cliente']);
          echo '<p>Especifica su teléfono o su correo para registrar la venta</p>';
          echo $this->Form->input('client_phone',['label'=>'Teléfono','type'=>'phone']);
          echo $this->Form->input('client_cell',['label'=>'Celular','type'=>'phone']);
          echo $this->Form->input('client_email',['label'=>'Correo','type'=>'email']);
          echo $this->Form->input('client_ruc',['label'=>'RUC']);
          //echo $this->Form->input('client_type_id',['default'=>0,'empty'=>[0=>'-- Tipo de Cliente --']]);
          //echo $this->Form->input('zone_id',['default'=>0,'empty'=>[0=>'-- Zona --']]);
          echo $this->Form->textarea('client_address',['label'=>'Dirección','rows'=>2]);
          //echo $this->Form->input('delivery_address',['label'=>'Dirección de entrega', 'type'=>'textarea','div'=>['class'=>'input textarea d-none']]);
				echo '</div>';
				echo "<div class='col-sm-4'>";
					echo "<div class='salesorder'>";
						echo "<dl style='width:100%;'>";
							echo "<dt>Subtotal</dt>";
							echo "<dd id='subtotal'><span class='currency'></span><span class='amountright'>0</span></dd>";
							echo "<dt>IVA</dt>";
							echo "<dd id='iva'><span class='currency'></span><span class='amountright'>0</span></dd>";
							echo "<dt>Total</dt>";
							echo "<dd id='total'><span class='currency'></span><span class='amountright'>0</span></dd>";
						echo "</dl>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	echo "</fieldset>";
	
	echo '<div id="products" style="font-size:0.9em;">';
    $productTableHead='';
    $productTableHead.='<thead>';
      $productTableHead.='<tr>';
        $productTableHead.='<th>'.__('Product').'</th>';
        $productTableHead.='<th>'.__('Description').'</th>';
        $productTableHead.='<th style="text-align:center;">Cant. Pendiente</th>';
        $productTableHead.='<th style="text-align:center;">Cant. Producto</th>';
        $productTableHead.='<th style="text-align:center;">'.__('Unit Price').'</th>';
        $productTableHead.='<th style="text-align:center;">'.__('Total Price').'</th>';
        $productTableHead.='<th class="hidden">'.__('Without IVA').'</th>';
        $productTableHead.='<th>'.__('IVA?').'</th>';
      $productTableHead.='</tr>';
    $productTableHead.='</thead>';
		
		$i=0;
    $subtotal=0;
    $ivatotal=0;
    $productTableRows='';
		if (!empty($productsForInvoice)){
			for ($i=0;$i<count($productsForInvoice);$i++){
        //pr($productsForInvoice[$i]);
        $currentProductArray=[$productsForInvoice[$i]['product_id']=>$products[$productsForInvoice[$i]['product_id']]];
        
        $subtotal+=round($productsForInvoice[$i]['product_quantity']*$productsForInvoice[$i]['product_unit_price'],2);
        if($productsForInvoice[$i]['bool_iva']){
          $ivatotal+=round(0.15*$productsForInvoice[$i]['product_quantity']*$productsForInvoice[$i]['product_unit_price'],2);	
        }
				if ($productsForInvoice[$i]['currency_id'] == $currencyId){
          $unitPrice=$productsForInvoice[$i]['product_unit_price'];
        }
        else {
          if ($currencyId == CURRENCY_USD){
            $unitPrice=round($productsForInvoice[$i]['product_unit_price']/$exchangeRateInvoice,2);
          }
          else {
            $unitPrice=round($productsForInvoice[$i]['product_unit_price']*$exchangeRateInvoice,2);
          }
        }
        $totalPrice=round($productsForInvoice[$i]['product_quantity']*$unitPrice,2);	
        
        
				$productTableRows.='<tr row="'.$i.'">';
        $productTableRows.='<td class="productid">';
          $productTableRows.=$this->Form->input('InvoiceProduct.'.$i.'.product_id',['label'=>false,'class'=>'fixed','default'=>$productsForInvoice[$i]['product_id'],'options'=>$currentProductArray]);
          $productTableRows.=$this->Form->input('InvoiceProduct.'.$i.'.sales_order_product_id',['label'=>false,'type'=>'hidden','value'=>$productsForInvoice[$i]['sales_order_product_id']]);
          $productTableRows.=$this->Form->input('InvoiceProduct.'.$i.'.currency_id',['label'=>false,'type'=>'hidden','value'=>$productsForInvoice[$i]['currency_id']]);
        $productTableRows.='</td>';
        $productTableRows.='<td class="productdescription" style="min-width:180px;">'.$this->Form->textarea('InvoiceProduct.'.$i.'.product_description',['label'=>false,'rows'=>10,'default'=>$productsForInvoice[$i]['product_description'],'readonly'=>true]).'</td>';
        $productTableRows.='<td class="pendingquantity amount">'.$this->Form->input('InvoiceProduct.'.$i.'.product_quantity_pending',['label'=>false,'value'=>$productsForInvoice[$i]['product_quantity_pending'],'type'=>'numeric','readonly'=>true]).'</td>';
        $productTableRows.='<td class="productquantity amount">'.$this->Form->input('InvoiceProduct.'.$i.'.product_quantity',['label'=>false,'default'=>$productsForInvoice[$i]['product_quantity'],'type'=>'numeric','readonly'=>!$canSavePartialInvoice]).'</td>';
        $productTableRows.='<td class="productunitprice amount"><span class="currency">'.($currencyId == CURRENCY_USD?'US$':'C$').'</span><span class="amount right">'.$this->Form->input('InvoiceProduct.'.$i.'.product_unit_price',['label'=>false,'default'=>$unitPrice,'type'=>'decimal','readonly'=>true]).'</span></td>';
        $productTableRows.='<td class="producttotalprice amount"><span class="currency">'.($currencyId == CURRENCY_USD?'US$':'C$').'</span><span class="amount right">'.$this->Form->input('InvoiceProduct.'.$i.'.product_total_price',['label'=>false,'default'=>$totalPrice,'type'=>'decimal','readonly'=>true]).'</span></td>';
        $productTableRows.='<td class="boolnoiva hidden">'.$this->Form->input('InvoiceProduct.'.$i.'.bool_no_iva',['label'=>false,'type'=>'checkbox','default'=>$productsForInvoice[$i]['Product']['bool_no_iva'],'onclick'=>'return false']).'</td>';
        $productTableRows.='<td class="booliva">'.$this->Form->input('InvoiceProduct.'.$i.'.bool_iva',['label'=>false,'default'=>$productsForInvoice[$i]['bool_iva'],'onclick'=>'return false']).'</td>';
      $productTableRows.='</tr>';
      }
    }
    $total=$subtotal+$ivatotal;
    
    $totalRows='';
    $totalRows.='<tr class="totalrow subtotal">';
      $totalRows.='<td>Subtotal</td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td class="productquantity amount centered"><span style="text-align:center;"></span></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td class="totalprice amount right"><span class="currency"></span>'.$this->Form->input('price_subtotal',['label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>0]).'</td>';
      $totalRows.='<td></td>';
    $totalRows.='</tr>';	
    $totalRows.='<tr class="totalrow iva">';
      $totalRows.='<td>IVA</td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td class="totalprice amount right"><span class="currency"></span>'.$this->Form->input('price_iva',['label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>0]).'</td>';
      $totalRows.='<td></td>';
    $totalRows.='</tr>';	
    $totalRows.='<tr class="totalrow total">';
      $totalRows.='<td>Total</td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td class="totalprice amount right"><span class="currency"></span>'.$this->Form->input('price_total',['label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>0]).'</td>';
      $totalRows.='<td></td>';
    $totalRows.='</tr>';	
		
		$productTableBody='<tbody>'.$productTableRows.$totalRows.'</tbody>';
		echo '<table id="productos">'.$productTableHead.$productTableBody.'</table>';
	echo "</div>";
	
	//echo "<button id='addItem' type='button'>".__('Add Item')."</button>";
	echo $this->Form->submit(__('Submit'),['name'=>'submit', 'id'=>'submit','div'=>['class'=>'submit submission']]); 
	echo $this->Form->submit('Guardar y siguiente Factura',['name'=>'saveandnext', 'id'=>'saveandnext','div'=>['class'=>'submit saveandnext']]); 
	echo $this->Form->end(); 
?>
</div>