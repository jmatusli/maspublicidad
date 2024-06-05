<script>
	$('body').on('change','#RemissionRemissionDateDay',function(){	
		updateDueDate();
		updateExchangeRate();
		updateProductsForDate();
	});
	$('body').on('change','#RemissionRemissionDateMonth',function(){	
		updateDueDate();
		updateExchangeRate();
	});
	$('body').on('change','#RemissionRemissionDateYear',function(){	
		updateDueDate();
		updateExchangeRate();
	});
	
	function updateDueDate(){
		var entrydateday=$('#RemissionRemissionDateDay').val();
		var entrydatemonth=$('#RemissionRemissionDateMonth').val();
		var entrydateyear=$('#RemissionRemissionDateYear').val();
		var d=new Date(entrydateyear,entrydatemonth,entrydateday);
		d.setDate=(d.getDate()+30);
		$('#RemissionDueDateDay').val(('0'+d.getDate()).slice(-2));
		$('#RemissionDueDateMonth').val(('0'+(d.getMonth()+1)).slice(-2));
		$('#RemissionDueDateYear').val(d.getFullYear());
	}
	function updateExchangeRate(){
		var selectedday=$('#RemissionRemissionDateDay').children("option").filter(":selected").val();
		var selectedmonth=$('#RemissionRemissionDateMonth').children("option").filter(":selected").val();
		var selectedyear=$('#RemissionRemissionDateYear').children("option").filter(":selected").val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>exchange_rates/getexchangerate/',
			data:{"selectedday":selectedday,"selectedmonth":selectedmonth,"selectedyear":selectedyear},
			cache: false,
			type: 'POST',
			success: function (exchangerate) {
				$('#RemissionExchangeRate').val(exchangerate);
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	
	function updateProductsForDate(){
		var selectedday=$('#RemissionRemissionDateDay').children("option").filter(":selected").val();
		var selectedmonth=$('#RemissionRemissionDateMonth').children("option").filter(":selected").val();
		var selectedyear=$('#RemissionRemissionDateYear').children("option").filter(":selected").val();
		var remissionid=<?php echo $remissionId; ?>;
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>inventory_products/getproductstatsfordate/',
			data:{"selectedday":selectedday,"selectedmonth":selectedmonth,"selectedyear":selectedyear,"remissionid":remissionid},
			cache: false,
			type: 'POST',
			dataType: 'json',
			success: function (data) {
				//alert(data);
				$('#productsForRemission tbody tr:not(.totalrow)').each(function() {
					$(this).find('td.productlineid div select').html(data.productlines);
					$(this).find('td.stockquantity div select').html(data.stockquantities);
					var selectedproductid=parseInt($(this).find('td.productid div select').val());
					var rowid=$(this).attr('row');
					$(this).find('td.productid div select').html(data.products);
					var productidarray=$.map(data.productids, function(el) { return parseInt(el) });
					//var productidarray=jQuery.parseJSON(data.productids);
					//var productidarray=data.productids;
					var inarray=$.inArray(selectedproductid,productidarray);
					if (selectedproductid>0 && inarray>-1){
						$(this).find('td.productid div select').val(selectedproductid);
						productChangeRoutine(selectedproductid,rowid,false);
					}
					else {
						// if the value is not in the list, then set width, height, color, quantity, unit cost to 0					
						$(this).find('td.productid div select').val(0);
						$(this).find('td.stockquantity div select').val(0);
						$(this).find('td.productlineid div select').val(0);
						$(this).find('td.width div input').val(0);
						$(this).find('td.height div input').val(0);
						$(this).find('td.color div input').val(0);
						$(this).find('td.weight div input').val(0);
						$(this).find('td.productquantity div input').val(0);
						$(this).find('td.productunitprice div input').val(0);
						$(this).find('td.producttotalprice div input').val(0);
					}
				});
				calculateTotal();
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	
	$('body').on('change','#RemissionBoolAnnulled',function(){	
		if ($(this).is(':checked')){
			displayAllRemissionFields(false);
		}
		else {
			displayAllRemissionFields(true);
		}
	});	
	
	function displayAllRemissionFields(boolShow){
		if (boolShow){
			$('#RemissionDueDateDay').parent().removeClass('hidden');
			$('#RemissionCurrencyId').parent().removeClass('hidden');
			$('#RemissionBoolIva').parent().removeClass('hidden');
			$('#RemissionBoolPaid').parent().removeClass('hidden');
			$('#productsForRemission').removeClass('hidden');
			$('#addProduct').removeClass('hidden');
		}
		else {
			$('#RemissionDueDateDay').parent().addClass('hidden');
			$('#RemissionCurrencyId').parent().addClass('hidden');
			$('#RemissionBoolIva').parent().addClass('hidden');
			$('#RemissionBoolPaid').parent().addClass('hidden');
			$('#productsForRemission').addClass('hidden');
			$('#addProduct').addClass('hidden');
		}
	}

	$('body').on('change','#RemissionCurrencyId',function(){
		var currencyid=$(this).val();
		if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('span.currency').text("US$");
		}
		else if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('span.currency').text("C$");
		}
		// now update all prices
		var exchangerate=parseFloat($('#RemissionExchangeRate').val());
		if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('td.productunitprice').each(function(){
				var originalprice= $(this).find('div input').val();
				var newprice=roundToTwo(originalprice/exchangerate);
				$(this).find('div input').val(newprice);
				$(this).find('div input').trigger('change');
			});
		}
		else if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('td.productunitprice').each(function(){
				var originalprice= $(this).find('div input').val();
				var newprice=roundToTwo(originalprice*exchangerate);
				$(this).find('div input').val(newprice);
				$(this).find('div input').trigger('change');
			});
		}
	});
	
	$('body').on('change','.productid div select',function(){
		var productid=$(this).val();
		var rowid=$(this).closest('tr').attr('row');
		productChangeRoutine(productid,rowid,true);
	});	
	function productChangeRoutine(productid,rowid,boolmanualchange){
		var currentrow=$('#productsForRemission').find("[row='" + rowid + "']");
		setMeasuringUnit(productid,rowid);
		updateStockQuantity(rowid,productid);
		updateProductLineId(rowid,productid);
		setWidth(productid,rowid);
		setVisibilityPerMeterColumns();
		setVisibilityPaintColumns();
		if (boolmanualchange){
			currentrow.find('.productquantity div input').val(0);
			currentrow.find('.productunitprice div input').val(0);
		}
		calculateRow(rowid);
		calculateTotal();
	}

	function setMeasuringUnit(productid,rowid){
		var currentrow=$('#productsForRemission').find("[row='" + rowid + "']");
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>inventory_products/getproductmeasuringunitid/',
			data:{"productid":productid},
			cache: false,
			type: 'POST',
			success: function (measuringunit) {
				currentrow.find('span.measuringunit').text(measuringunit);
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	function updateStockQuantity(rowid,productid){
		var currentrow=$('#productsForRemission').find("[row='" + rowid + "']");
		currentrow.find('.stockquantity div select').val(productid);
		var lateststockquantity=currentrow.find('.stockquantity div select option:selected').text();
		currentrow.find('.stockquantityvalue div input').val(lateststockquantity);
	}
	function updateProductLineId(rowid,productid){
		var currentrow=$('#productsForRemission').find("[row='" + rowid + "']");
		currentrow.find('.productlineid div select').val(productid);
	}
	function setWidth(productid,rowid){
		var currentrow=$('#productsForRemission').find("[row='" + rowid + "']");
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>inventory_products/getproductwidth/',
			data:{"productid":productid},
			cache: false,
			type: 'POST',
			success: function (productwidth) {
				currentrow.find('td.width div input').val(productwidth);
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	function setVisibilityPerMeterColumns(){
		var boolShow=false;
		$("#productsForRemission tbody tr:not(.hidden)").each(function() {
			var productlineid= $(this).find('.productlineid div select option:selected').text();
			if (productlineid==<?php echo INVENTORY_PRODUCT_LINE_PER_METER; ?>){
				boolShow=true;
			}
		});
		if (boolShow){
			$('.width').removeClass('hidden');
			$('.height').removeClass('hidden');
			$("#productsForRemission tbody tr:not(.hidden)").each(function() {
				var productlineid= $(this).find('.productlineid div select option:selected').text();
				if (productlineid==<?php echo INVENTORY_PRODUCT_LINE_PER_METER; ?>){
					$(this).find('td.width div').removeClass('hidden');
					$(this).find('td.height div').removeClass('hidden');
				}
				else{
					$(this).find('td.width div').addClass('hidden');
					$(this).find('td.height div').addClass('hidden');
				}
			});			
		}
		else {
			$('.width').addClass('hidden');
			$('.height').addClass('hidden');
		}
	}
	
	function setVisibilityPaintColumns(){
		var boolShow=false;
		$("#productsForRemission tbody tr:not(.hidden)").each(function() {
			var productlineid= $(this).find('.productlineid div select option:selected').text();
			if (productlineid==25){
				boolShow=true;
			}
		});
		if (boolShow){
			$('.width').removeClass('hidden');
			$('.height').removeClass('hidden');
			$("#productsForRemission tbody tr:not(.hidden)").each(function() {
				var productlineid= $(this).find('.productlineid div select option:selected').text();
				if (productlineid==25){
					$(this).find('td.color div').removeClass('hidden');
				}
				else{
					$(this).find('td.color div').addClass('hidden');
				}
			});			
		}
		else {
			$('.color').addClass('hidden');
		}
	}
	
	$('body').on('change','.width',function(){
		var rowid=$(this).closest('tr').attr('row')
		setQuantityFromWidthHeight(rowid);
		
	});	
	$('body').on('change','.height',function(){
		if (!$(this).find('div input').val()||isNaN($(this).find('div input').val())){
			$(this).find('div input').val(0);
		}
		var rowid=$(this).closest('tr').attr('row')
		setQuantityFromWidthHeight(rowid);
	});	
	function setQuantityFromWidthHeight(rowid){
		var currentrow=$('#productsForRemission').find("[row='" + rowid + "']");
		var width=parseFloat(currentrow.find('td.width div input').val());
		var height=parseFloat(currentrow.find('td.height div input').val());
		var quantity=roundToTwo(width*height);
		currentrow.find('td.productquantity div input').val(quantity);
		currentrow.find('td.productquantity div input').trigger('change');
	}
	
	
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
		var currentrow=$('#productsForRemission').find("[row='" + rowid + "']");
		
		var quantity=parseFloat(currentrow.find('td.productquantity div input').val());
		var unitprice=parseFloat(currentrow.find('td.productunitprice div input').val());
		
		var totalprice=quantity*unitprice;
		
		currentrow.find('td.producttotalprice div input').val(roundToTwo(totalprice));
	}
	
	$('body').on('change','#RemissionBoolIva',function(){
		calculateTotal();
	});
	
	function calculateTotal(){
		var booliva=$('#RemissionBoolIva').is(':checked');
		var subtotalPrice=0;
		var ivaPrice=0;
		var totalPrice=0;
		$("#productsForRemission tbody tr:not(.hidden)").each(function() {
			var currentProduct = $(this).find('td.producttotalprice div input');
			if (!isNaN(currentProduct.val())){
				var currentPrice = parseFloat(currentProduct.val());
				subtotalPrice += currentPrice;
			}
		});
		$('#RemissionPriceSubtotal').val(subtotalPrice);
		$('tr.totalrow.subtotal td.totalprice span.amount').text(subtotalPrice);
		
		if (booliva){
			ivaPrice=roundToTwo(0.15*subtotalPrice);
		}
		$('#RemissionPriceIva').val(ivaPrice);
		$('tr.totalrow.iva td.totalprice span.amount').text(ivaPrice);
		totalPrice=subtotalPrice + ivaPrice;
		$('#RemissionPriceTotal').val(totalPrice);
		$('tr.totalrow.total td.totalprice span.amount').text(totalPrice);
		
		
		return false;
	}
	
	$('body').on('click','#addProduct',function(){
		var tableRow=$('#productsForRemission tbody tr.hidden:first');
		tableRow.removeClass("hidden");
	});

	$('body').on('click','.removeProduct',function(e){
		e.preventDefault();
		var tableRow=$(this).closest('tr').remove();
		calculateTotal();
	});	
		
	function formatCurrencies(){
		$("td.amount span.amountright").each(function(){
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,2);
		});
		var currencyid=$('#RemissionCurrencyId').children("option").filter(":selected").val();
		if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('span.currency').text('C$ ');
		}
		else if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('span.currency').text('US$ ');			
		}
	}
	
	$(document).ready(function(){
		updateDueDate();
		formatCurrencies();
		setVisibilityPerMeterColumns();
		setVisibilityPaintColumns();
		if ($('#RemissionBoolAnnulled').is(':checked')){
			displayAllRemissionFields(false);
		}
		else {
			displayAllRemissionFields(true);
		}
		updateProductsForDate();
	});
</script>
<div class="remissions form fullwidth">
<?php 
	echo $this->Form->create('Remission'); 
	echo "<fieldset>";
		echo "<legend>".__('Edit Remission')."</legend>";
		echo "<div class='container-fluid'>";
			echo "<div class='rows'>";
				echo "<div class='col-md-9'>";
					echo $this->Form->input('remission_date',array('dateFormat'=>'DMY'));
					echo $this->Form->input('exchange_rate',array('default'=>$exchangeRateRemission,'readonly'=>'readonly'));
					echo $this->Form->input('remission_code');
					echo $this->Form->input('bool_annulled');
					echo $this->Form->input('client_id');
					echo $this->Form->input('due_date',array('dateFormat'=>'DMY'));
					echo $this->Form->input('currency_id');
					echo $this->Form->input('bool_iva');
					echo $this->Form->input('bool_paid');
				echo "</div>";
				echo "<div class='col-md-3'>";					
					echo $this->Form->input('price_subtotal',array('readonly'=>'readonly','type'=>'decimal', 'between' => '<span class=\'currency totalbox\'></span>'));
					echo $this->Form->input('price_iva',array('readonly'=>'readonly','type'=>'decimal', 'between' => '<span class=\'currency totalbox\'></span>'));
					echo $this->Form->input('price_total',array('readonly'=>'readonly','type'=>'decimal', 'between' => '<span class=\'currency totalbox\'></span>'));
				echo "</div>";
				
				echo "<div class='col-md-12'>";
					echo $this->Form->Submit(__('Submit')); 
					echo "<table id='productsForRemission'>";
						echo "<thead>";
							echo "<tr>";
								echo "<th>".__('Product')."</th>";
								
								// hidden column for calculating admissibility remission
								echo "<th class='hidden'>".__('Stock Quantity')."</th>";
								echo "<th>".__('Cantidad en Bodega')."</th>";
								
								// hidden column for displaying additional product parameters for remission
								echo "<th class='hidden'>".__('Product Line')."</th>";
								
								// para productos por metro
								echo "<th class='centered width'>".__('Width')."</th>";
								echo "<th class='centered height'>".__('Height')."</th>";
								// para pintura
								echo "<th class='color'>".__('Color')."</th>";
								//echo "<th class='centered weight'>".__('Weight')."</th>";
								
								echo "<th class='centered'>".__('Quantity')."</th>";
								echo "<th>".__('Unidad')."</th>";
								echo "<th>".__('Price')."</th>";
								echo "<th></th>";
							echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						for ($i=0;$i<25;$i++) { 
							if ($i==0||$i<$numberOfProducts){
								echo "<tr row='".$i."'>";
							} 
							else {
								echo "<tr row='".$i."' class='hidden'>";
							}
								echo "<td class='productid'>".$this->Form->input('StockMovement.'.$i.'.inventory_product_id',array('label'=>false,'default'=>'0','empty' =>array(0=>__('Choose a Product'))))."</td>";
								
								echo "<td class='stockquantity hidden'>".$this->Form->input('StockMovement.'.$i.'.stock_quantity',array('label'=>false,'default'=>'0','options'=>$inventoryProductStockQuantities,'empty' =>array(0=>'0')))."</td>";
								echo "<td class='stockquantityvalue'>".$this->Form->input('StockMovement.'.$i.'.stock_quantity_value',array('label'=>false,'default'=>'0','type'=>'decimal','readonly','readonly'))."</td>";
								
								echo "<td class='productlineid hidden'>".$this->Form->input('StockMovement.'.$i.'.inventory_product_line_id',array('label'=>false,'default'=>'0','empty' =>array(0=>'0')))."</td>";
								
								echo "<td class='width'>".$this->Form->input('StockMovement.'.$i.'.width',array('type'=>'decimal','label'=>false,'default'=>'0'))."</td>";
								echo "<td class='height'>".$this->Form->input('StockMovement.'.$i.'.height',array('type'=>'decimal','label'=>false,'default'=>'0'))."</td>";
								
								echo "<td class='color'>".$this->Form->input('StockMovement.'.$i.'.color',array('label'=>false))."</td>";
								//echo "<td class='weight'>".$this->Form->input('StockMovement.'.$i.'.weight',array('type'=>'decimal','label'=>false,'default'=>'0'))."</td>";
								
								echo "<td class='productquantity'>".$this->Form->input('StockMovement.'.$i.'.product_quantity',array('type'=>'decimal','label'=>false,'default'=>'0'))."<span class='measuringunit'></span></td>";
								echo "<td class='productunitprice amount'><span class='currency'></span>".$this->Form->input('StockMovement.'.$i.'.product_unit_price',array('type'=>'decimal','label'=>false,'default'=>'0'))."</td>";
								echo "<td  class='producttotalprice'>".$this->Form->input('StockMovement.'.$i.'.product_total_price',array('type'=>'decimal','label'=>false,'default'=>'0','readonly'=>'readonly'))."</td>";
								echo "<td><button class='removeProduct' type='button'>".__('Remover Producto')."</button></td>";
							echo "</tr>";

						}
							echo "<tr class='totalrow subtotal'>";
								echo "<td>Subtotal</td>";
								echo "<td class='stockquantity'></td>";
								echo "<td class='productlineid'></td>";
								echo "<td class='width'></td>";
								echo "<td class='height'></td>";
								echo "<td class='color'></td>";
								//echo "<td class='weight'></td>";
								echo "<td></td>";
								echo "<td></td>";
								echo "<td class='totalprice amount right'><span class='currency'></span><span class='amount'>0</span></td>";
								echo "<td></td>";
							echo "</tr>";		
							echo "<tr class='totalrow iva'>";
								echo "<td>IVA</td>";
								echo "<td class='stockquantity'></td>";
								echo "<td class='productlineid'></td>";
								echo "<td class='width'></td>";
								echo "<td class='height'></td>";
								echo "<td class='color'></td>";
								//echo "<td class='weight'></td>";
								echo "<td></td>";
								echo "<td></td>";
								echo "<td class='totalprice amount right'><span class='currency'></span><span class='amount'>0</span></td>";
								echo "<td></td>";
							echo "</tr>";		
							echo "<tr class='totalrow total'>";
								echo "<td>Total</td>";
								echo "<td class='stockquantity'></td>";
								echo "<td class='productlineid'></td>";
								echo "<td class='width'></td>";
								echo "<td class='height'></td>";
								echo "<td class='color'></td>";
								//echo "<td class='weight'></td>";
								echo "<td></td>";
								echo "<td></td>";
								echo "<td class='totalprice amount right'><span class='currency'></span><span class='amount'>0</span></td>";
								echo "<td></td>";
							echo "</tr>";	
						echo "</tbody>";
					echo "</table>";
					echo "<button id='addProduct' type='button'>".__('Añadir Producto a Salida')."</button>";
					echo $this->Form->input('observation');
				echo "</div>";
			echo "</div>";
		echo "</div>";	
	echo "</fieldset>"; 
	echo $this->Form->end(); 	
?>
</div>
<!--div class='actions'>
<?php
	/*
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Remission.id')), array(), __('Are you sure you want to delete #%s?',$this->Form->value('Remission.id')))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Remissions'), array('action' => 'index'))."</li>";
		
		echo "<li>".$this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index'))." </li>";
		echo "<li>".$this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add'))." </li>";
	echo "</ul>";
	*/
?>
</div-->
