<script>	
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

	function displayCashReceiptCode(){
		if ($('#InvoiceBoolCredit').is(':checked')){
			$('#InvoiceCashReceiptCode').parent().removeClass('hidden');
		}
		else {
			$('#InvoiceCashReceiptCode').parent().addClass('hidden');
		}
	}
	
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
  
	$(document).ready(function(){
		formatCurrencies();
		displayCashReceiptCode();
    calculateTotal();
    
    $('#InvoiceInvoiceDateDay').addClass('fixed');
    $('#InvoiceInvoiceDateMonth').addClass('fixed');
    $('#InvoiceInvoiceDateYear').addClass('fixed');
		
		$('select.fixed option:not(:selected)').attr('disabled', true);
	});
</script>
<div class="invoices form fullwidth">
<?php 
	echo $this->Form->create('Invoice'); 
	echo "<fieldset>";
		echo '<legend>Editar Referencia de Factura '.$this->request->data['Invoice']['invoice_code'].'</legend>';
		echo "<div class='container-fluid'>";
			echo "<div class='row'>";
				echo "<div class='col-sm-8'>";
					echo $this->Form->input('id');
          if ($userRoleId == ROLE_ADMIN || $userRoleId == ROLE_ASSISTANT) { 
						echo $this->Form->input('user_id',['label'=>'Ejecutivo de Ventas']);
					}
					else {
						echo $this->Form->input('user_id',['label'=>'Ejecutivo de Ventas','type'=>'hidden']);
					}
          //pr($this->request->data['Invoice']);
					echo $this->Form->input('client_id',['value'=>$this->request->data['Invoice']['client_id'],'class'=>'fixed']);
					echo $this->Form->input('sales_order_id',[
            'multiple'=>true,
            'lines'=>2,
            'value'=>$selectedSalesOrderArray,
            'class'=>'fixed',
          ]);
					echo $this->Form->input('invoice_date',['dateFormat'=>'DMY']);
					echo $this->Form->input('reference');
          echo $this->Form->input('exchange_rate',['readonly'=>true]);
					echo $this->Form->input('invoice_code',['readonly'=>true]);
					echo $this->Form->input('bool_annulled',['onclick'=>'return false']);
					echo $this->Form->input('bool_iva',['onclick'=>'return false']);
					echo $this->Form->input('currency_id',['class'=>'fixed']);
					echo $this->Form->input('bool_credit',['label'=>'Crédito?','onclick'=>'return false']);
					echo $this->Form->input('bool_paid',['label'=>'Pagada?','onclick'=>'return false']);
				echo "</div>";
				echo "<div class='col-md-4'>";
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
	
	echo "<div id='products' style='font-size:0.9em;'>";
		echo "<table id='productos'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Product')."</th>";
					echo "<th>".__('Description')."</th>";
					echo "<th>".__('Product Quantity')."</th>";
					echo "<th>".__('Unit Price')."</th>";
					echo "<th>".__('Total Price')."</th>";
					echo "<th>".__('IVA ?')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			$i=0;
			$subtotal=0;
			$ivatotal=0;
			$currencyId=$this->request->data['Invoice']['currency_id'];
			if (!empty($productsForInvoice)){
				for ($i=0;$i<count($productsForInvoice);$i++){
					//pr($productsForInvoice[$i]);
					
					$subtotal+=round($productsForInvoice[$i]['product_quantity']*$productsForInvoice[$i]['product_unit_price'],2);
					if($productsForInvoice[$i]['bool_iva']){
						$ivatotal+=round(0.15*$productsForInvoice[$i]['product_quantity']*$productsForInvoice[$i]['product_unit_price'],2);	
					}
          if ($productsForInvoice[$i]['currency_id']==$currencyId){
            $unitPrice=$productsForInvoice[$i]['product_unit_price'];
          }
          else {
            if ($currencyId==CURRENCY_USD){
              $unitPrice=round($productsForInvoice[$i]['product_unit_price']/$exchangeRate,2);
            }
            else {
              $unitPrice=round($productsForInvoice[$i]['product_unit_price']*$exchangeRate,2);
            }
          }
          $totalPrice=round($productsForInvoice[$i]['product_quantity']*$unitPrice,2);
						
					echo "<tr row='".$i."'>";
						echo "<td class='productid'>";
							echo $this->Form->input('InvoiceProduct.'.$i.'.product_id',[
                'label'=>false,'value'=>$productsForInvoice[$i]['product_id'],
                'class'=>'fixed',
              ]);
						echo "</td>";
						
						echo "<td class='productdescription'>".$this->Form->textarea('InvoiceProduct.'.$i.'.product_description',[
              'label'=>false,'rows'=>10,'value'=>$productsForInvoice[$i]['product_description'],
              'readonly'=>true,
            ])."</td>";					
						echo "<td class='productquantity amount'>".$this->Form->input('InvoiceProduct.'.$i.'.product_quantity',[
              'label'=>false,'value'=>$productsForInvoice[$i]['product_quantity'],'type'=>'numeric',
              'readonly'=>true,
            ])."</td>";
						echo "<td class='productunitprice amount'><span class='currency'>".($currencyId==CURRENCY_USD?"US$":"C$")."</span><span class='amount right'>".$this->Form->input('InvoiceProduct.'.$i.'.product_unit_price',[
              'label'=>false,'value'=>$unitPrice,'type'=>'decimal',
              'readonly'=>true,
            ])."</span></td>";
						echo "<td class='producttotalprice amount'><span class='currency'>".($currencyId==CURRENCY_USD?"US$":"C$")."</span><span class='amount right'>".$this->Form->input('InvoiceProduct.'.$i.'.product_total_price',[
              'label'=>false,'value'=>$totalPrice,'type'=>'decimal',
              'readonly'=>true
            ])."</span></td>";
						echo "<td class='booliva'>".$this->Form->input('InvoiceProduct.'.$i.'.bool_iva',[
              'label'=>false,'value'=>$productsForInvoice[$i]['bool_iva'],'onclick'=>'return false'
            ])."</td>";
						
					echo "</tr>";
				}
			}
        $total=$subtotal+$ivatotal;
				echo "<tr class='totalrow subtotal'>";
					echo "<td>Subtotal</td>";
					echo "<td></td>";
					echo "<td class='productquantity amount centered'><span style='text-align:center;'></span></td>";
					echo "<td></td>";
					echo "<td class='totalprice amount right'><span class='currency'></span>".$this->Form->input('price_subtotal',array('label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>'0'))."</td>";
					echo "<td></td>";
				echo "</tr>";	
				echo "<tr class='totalrow iva'>";
					echo "<td>IVA</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td class='totalprice amount right'><span class='currency'></span>".$this->Form->input('price_iva',array('label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>'0'))."</td>";
					echo "<td></td>";
				echo "</tr>";	
				echo "<tr class='totalrow total'>";
					echo "<td>Total</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td class='totalprice amount right'><span class='currency'></span>".$this->Form->input('price_total',array('label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>'0'))."</td>";
					echo "<td></td>";
				echo "</tr>";	
			echo "</tbody>";
		echo "</table>";
	echo "</div>";
	
	echo $this->Form->submit(__('Submit'),['name'=>'submit', 'id'=>'submit','div'=>['class'=>'submit submission']]); 
	echo $this->Form->end(); 
?>
</div>