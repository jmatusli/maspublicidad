<script>
	$('body').on('change','#EntryEntryDateDay',function(){	
		updateDueDate();
		updateExchangeRate();
	});
	$('body').on('change','#EntryEntryDateMonth',function(){	
		updateDueDate();
		updateExchangeRate();
	});
	$('body').on('change','#EntryEntryDateYear',function(){	
		updateDueDate();
		updateExchangeRate();
	});
	
	function updateDueDate(){
		var entrydateday=$('#EntryEntryDateDay').val();
		var entrydatemonth=$('#EntryEntryDateMonth').val();
		var entrydateyear=$('#EntryEntryDateYear').val();
		var d=new Date(entrydateyear,entrydatemonth,entrydateday);
		d.setDate=(d.getDate()+30);
		$('#EntryDueDateDay').val(('0'+d.getDate()).slice(-2));
		$('#EntryDueDateMonth').val(('0'+(d.getMonth()+1)).slice(-2));
		$('#EntryDueDateYear').val(d.getFullYear());
	}
	function updateExchangeRate(){
		var selectedday=$('#EntryEntryDateDay').children("option").filter(":selected").val();
		var selectedmonth=$('#EntryEntryDateMonth').children("option").filter(":selected").val();
		var selectedyear=$('#EntryEntryDateYear').children("option").filter(":selected").val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>exchange_rates/getexchangerate/',
			data:{"selectedday":selectedday,"selectedmonth":selectedmonth,"selectedyear":selectedyear},
			cache: false,
			type: 'POST',
			success: function (exchangerate) {
				$('#EntryExchangeRate').val(exchangerate);
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	
	$('body').on('change','#EntryBoolAnnulled',function(){	
		if ($(this).is(':checked')){
			displayAllEntryFields(false);
		}
		else {
			displayAllEntryFields(true);
		}
	});	
	
	function displayAllEntryFields(boolShow){
		if (boolShow){
			$('#EntryDueDateDay').parent().removeClass('hidden');
			$('#EntryCurrencyId').parent().removeClass('hidden');
			$('#EntryBoolIva').parent().removeClass('hidden');
			$('#EntryBoolPaid').parent().removeClass('hidden');
			$('#productsForEntry').removeClass('hidden');
			$('#addProduct').removeClass('hidden');
		}
		else {
			$('#EntryDueDateDay').parent().addClass('hidden');
			$('#EntryCurrencyId').parent().addClass('hidden');
			$('#EntryBoolIva').parent().addClass('hidden');
			$('#EntryBoolPaid').parent().addClass('hidden');
			$('#productsForEntry').addClass('hidden');
			$('#addProduct').addClass('hidden');
		}
	}

	$('body').on('change','#EntryCurrencyId',function(){
		var currencyid=$(this).val();
		if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('span.currency').text("US$");
		}
		else if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('span.currency').text("C$");
		}
		// now update all prices
		var exchangerate=parseFloat($('#EntryExchangeRate').val());
		if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('td.productunitcost').each(function(){
				var originalcost= $(this).find('div input').val();
				var newprice=roundToTwo(originalprice/exchangerate);
				$(this).find('div input').val(newprice);
				$(this).find('div input').trigger('change');
			});
		}
		else if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('td.productunitcost').each(function(){
				var originalcost= $(this).find('div input').val();
				var newprice=roundToTwo(originalprice*exchangerate);
				$(this).find('div input').val(newprice);
				$(this).find('div input').trigger('change');
			});
		}
	});
	
	$('body').on('change','.productquantity',function(){
		calculateRow($(this).closest('tr').attr('row'));
		calculateTotal();
	});	
	$('body').on('change','.productunitcost',function(){
		calculateRow($(this).closest('tr').attr('row'));
		calculateTotal();
	});	
	
	function calculateRow(rowid) {    
		var currentrow=$('#productsForEntry').find("[row='" + rowid + "']");
		
		var quantity=parseFloat(currentrow.find('td.productquantity div input').val());
		var unitcost=parseFloat(currentrow.find('td.productunitcost div input').val());
		
		var totalcost=quantity*unitcost;
		
		currentrow.find('td.producttotalcost div input').val(roundToTwo(totalcost));
	}
	
	$('body').on('change','#EntryBoolIva',function(){
		calculateTotal();
	});
	
	function calculateTotal(){
		var booliva=$('#EntryBoolIva').is(':checked');
		var subtotalCost=0;
		var ivaCost=0;
		var totalCost=0;
		$("#productsForEntry tbody tr:not(.hidden)").each(function() {
			var currentProduct = $(this).find('td.producttotalcost div input');
			if (!isNaN(currentProduct.val())){
				var currentCost = parseFloat(currentProduct.val());
				subtotalCost += currentCost;
			}
		});
		$('#EntryCostSubtotal').val(subtotalCost);
		$('tr.totalrow.subtotal td.totalcost span.amount').text(subtotalCost);
		
		if (booliva){
			ivaCost=roundToTwo(0.15*subtotalCost);
		}
		$('#EntryCostIva').val(ivaCost);
		$('tr.totalrow.iva td.totalcost span.amount').text(ivaCost);
		totalCost=subtotalCost + ivaCost;
		$('#EntryCostTotal').val(totalCost);
		$('tr.totalrow.total td.totalcost span.amount').text(totalCost);
		
		
		return false;
	}
	
	$('body').on('click','#addProduct',function(){
		var tableRow=$('#productsForEntry tbody tr.hidden:first');
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
		var currencyid=$('#EntryCurrencyId').children("option").filter(":selected").val();
		if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('span.currency').text('C$ ');
		}
		else if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('span.currency').text('US$ ');			
		}
	}
	
	$(document).ready(function(){
		formatCurrencies();
		
		$('#productsForEntry tr').each(function(){
			var rowid=$(this).attr('row');
			calculateRow(rowid);
		});
		calculateTotal();
		
		updateExchangeRate();
		
		if ($('#EntryBoolAnnulled').is(':checked')){
			displayAllEntryFields(false);
		}
		else {
			displayAllEntryFields(true);
		}
	});
</script>
<div class="entries form fullwidth">
<?php 
	echo $this->Form->create('Entry'); 
	echo "<fieldset>"; 
		echo "<legend>".__('Edit Entry')."</legend>";
		echo "<div class='container-fluid'>";
			echo "<div class='rows'>";
				echo "<div class='col-md-9'>";
					echo $this->Form->input('entry_date',array('dateFormat'=>'DMY'));
					echo $this->Form->input('exchange_rate',array('readonly'=>'readonly'));
					echo $this->Form->input('entry_code');
					echo $this->Form->input('bool_annulled');
					echo $this->Form->input('provider_id');
					echo $this->Form->input('due_date',array('dateFormat'=>'DMY'));
					echo $this->Form->input('currency_id');
					echo $this->Form->input('bool_iva');
					echo $this->Form->input('bool_paid');
				echo "</div>";
				echo "<div class='col-md-3'>";					
					echo $this->Form->input('cost_subtotal',array('readonly'=>'readonly','type'=>'decimal','default'=>'0', 'between' => '<span class=\'currency totalbox\'></span>'));
					echo $this->Form->input('cost_iva',array('readonly'=>'readonly','type'=>'decimal','default'=>'0', 'between' => '<span class=\'currency totalbox\'></span>'));
					echo $this->Form->input('cost_total',array('readonly'=>'readonly','type'=>'decimal','default'=>'0', 'between' => '<span class=\'currency totalbox\'></span>'));
				echo "</div>";
				
				echo "<div class='col-md-12'>";
					echo $this->Form->Submit(__('Submit')); 
					echo "<table id='productsForEntry'>";
						echo "<thead>";
							echo "<tr>";
								echo "<th>".__('Product')."</th>";
								echo "<th>".__('Quantity')."</th>";
								echo "<th>".__('Unidad')."</th>";
								echo "<th>".__('Cost')."</th>";
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
								echo "<td class='productid'>".$this->Form->input('StockMovement.'.$i.'.product_id',array('label'=>false,'default'=>'0','empty' =>array(0=>__('Choose a Product'))))."</td>";
								echo "<td class='productquantity'>".$this->Form->input('StockMovement.'.$i.'.product_quantity',array('type'=>'decimal','label'=>false,'default'=>'0'))."</td>";
								echo "<td class='productunitcost amount'><span class='currency'></span>".$this->Form->input('StockMovement.'.$i.'.product_unit_cost',array('type'=>'decimal','label'=>false,'default'=>'0'))."</td>";
								echo "<td  class='producttotalcost'>".$this->Form->input('StockMovement.'.$i.'.product_total_cost',array('type'=>'decimal','label'=>false,'default'=>'0','readonly'=>'readonly'))."</td>";
								echo "<td><button class='removeProduct'>".__('Remover Producto')."</button></td>";
							echo "</tr>";

						}
							echo "<tr class='totalrow subtotal'>";
								echo "<td>Subtotal</td>";
								echo "<td></td>";
								echo "<td></td>";
								echo "<td class='totalcost amount right'><span class='currency'></span><span class='amount'>0</span></td>";
								echo "<td></td>";
							echo "</tr>";		
							echo "<tr class='totalrow iva'>";
								echo "<td>IVA</td>";
								echo "<td></td>";
								echo "<td></td>";
								echo "<td class='totalcost amount right'><span class='currency'></span><span class='amount'>0</span></td>";
								echo "<td></td>";
							echo "</tr>";		
							echo "<tr class='totalrow total'>";
								echo "<td>Total</td>";
								echo "<td></td>";
								echo "<td></td>";
								echo "<td class='totalcost amount right'><span class='currency'></span><span class='amount'>0</span></td>";
								echo "<td></td>";
							echo "</tr>";	
						echo "</tbody>";
					echo "</table>";
					echo "<button id='addProduct' type='button'>".__('Añadir Producto a Entrada')."</button>";
					echo $this->Form->input('observation');
				echo "</div>";
			echo "</div>";
		echo "</div>";	
	echo "</fieldset>"; 
	echo $this->Form->end(); 
?>
</div>
<div class='actions'>
<?php
	/*
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('List Entries'), array('action' => 'index'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Providers'), array('controller' => 'providers', 'action' => 'index'))." </li>";
		echo "<li>".$this->Html->link(__('New Provider'), array('controller' => 'providers', 'action' => 'add'))." </li>";
	echo "</ul>";
	*/
?>
</div>
