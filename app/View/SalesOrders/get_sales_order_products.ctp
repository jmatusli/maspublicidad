<?php
	if (!empty($productsForSalesOrders)){
		$productTableHead='';
    $productTableHead.='<thead>';
      $productTableHead.='<tr>';
        $productTableHead.='<th>'.__('Product').'</th>';
        $productTableHead.='<th>'.__('Description').'</th>';
        $productTableHead.='<th style="text-align:center;">Cant. Pendiente</th>';
        $productTableHead.='<th style="text-align:center;">Cant. Producto</th>';
        $productTableHead.='<th style="text-align:center;">'.__('Product Unit Price').'</th>';
        $productTableHead.='<th style="text-align:center;">'.__('Product Total Price').'</th>';
        $productTableHead.='<th class="hidden">'.__('Without IVA').'</th>';
        $productTableHead.='<th>'.__('IVA?').'</th>';
      $productTableHead.='</tr>';
    $productTableHead.='</thead>';
			
    $i=0;
    $subtotal=0;
    $ivatotal=0;
    $productTableRows='';
    foreach ($productsForSalesOrders as $product){
      $currentProductArray=[$product['SalesOrderProduct']['product_id']=>$products[$product['SalesOrderProduct']['product_id']]];
      
      //pr($product);
      $subtotal+=round($product['SalesOrderProduct']['product_quantity_pending']*$product['SalesOrderProduct']['product_unit_price'],2);
      if($product['SalesOrderProduct']['bool_iva']){
        $ivatotal+=round(0.15*$product['SalesOrderProduct']['product_quantity_pending']*$product['SalesOrderProduct']['product_unit_price'],2);	
      }
      if ($product['SalesOrderProduct']['currency_id']==$currencyId){
        $unitPrice=$product['SalesOrderProduct']['product_unit_price'];
      }
      else {
        if ($currencyId==CURRENCY_USD){
          $unitPrice=round($product['SalesOrderProduct']['product_unit_price']/$exchangeRate,2);
        }
        else {
          $unitPrice=round($product['SalesOrderProduct']['product_unit_price']*$exchangeRate,2);
        }
      }
      $totalPrice=round($product['SalesOrderProduct']['product_quantity_pending']*$unitPrice,2);
        
      $productTableRows.='<tr row="'.$i.'">';
        $productTableRows.='<td class="productid">';
          $productTableRows.=$this->Form->input('InvoiceProduct.'.$i.'.product_id',['label'=>false,'class'=>'fixed','default'=>$product['SalesOrderProduct']['product_id'],'options'=>$currentProductArray]);
          $productTableRows.=$this->Form->input('InvoiceProduct.'.$i.'.sales_order_product_id',['label'=>false,'type'=>'hidden','value'=>$product['SalesOrderProduct']['id']]);
          $productTableRows.=$this->Form->input('InvoiceProduct.'.$i.'.currency_id',['label'=>false,'type'=>'hidden','value'=>$product['SalesOrderProduct']['currency_id']]);
        $productTableRows.='</td>';
        $productTableRows.='<td class="productdescription" style="min-width:180px;">'.$this->Form->textarea('InvoiceProduct.'.$i.'.product_description',['label'=>false,'rows'=>10,'default'=>$product['SalesOrderProduct']['product_description'],'readonly'=>true]).'</td>';
        $productTableRows.='<td class="pendingquantity amount">'.$this->Form->input('InvoiceProduct.'.$i.'.product_quantity_pending',['label'=>false,'value'=>$product['SalesOrderProduct']['product_quantity_pending'],'type'=>'numeric','readonly'=>true]).'</td>';
        $productTableRows.='<td class="productquantity amount">'.$this->Form->input('InvoiceProduct.'.$i.'.product_quantity',['label'=>false,'default'=>$product['SalesOrderProduct']['product_quantity'],'type'=>'numeric','readonly'=>!$canSavePartialInvoice]).'</td>';
        $productTableRows.='<td class="productunitprice amount"><span class="currency">'.($currencyId == CURRENCY_USD?'US$':'C$').'</span><span class="amount right">'.$this->Form->input('InvoiceProduct.'.$i.'.product_unit_price',['label'=>false,'default'=>$unitPrice,'type'=>'decimal','readonly'=>true]).'</span></td>';
        $productTableRows.='<td class="producttotalprice amount"><span class="currency">'.($currencyId == CURRENCY_USD?'US$':'C$').'</span><span class="amount right">'.$this->Form->input('InvoiceProduct.'.$i.'.product_total_price',['label'=>false,'default'=>$totalPrice,'type'=>'decimal','readonly'=>true]).'</span></td>';
        $productTableRows.='<td class="boolnoiva hidden">'.$this->Form->input('InvoiceProduct.'.$i.'.bool_no_iva',['label'=>false,'type'=>'checkbox','default'=>$product['Product']['bool_no_iva'],'onclick'=>'return false']).'</td>';
        $productTableRows.='<td class="booliva">'.$this->Form->input('InvoiceProduct.'.$i.'.bool_iva',['label'=>false,'default'=>$product['SalesOrderProduct']['bool_iva'],'onclick'=>'return false']).'</td>';
      $productTableRows.='</tr>';
      $i++;
    }
  
    $total=$subtotal+$ivatotal;
    
    $totalRows='';
    $totalRows.='<tr class="totalrow subtotal">';
      $totalRows.='<td>Subtotal</td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td class="productquantity amount centered"><span></span></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td class="totalprice amount right"><span class="currency">'.($currencyId == CURRENCY_USD?'US$':'C$').'</span>'.$this->Form->input('Invoice.price_subtotal',['label'=>false,'type'=>'decimal','readonly'=>true,'default'=>$subtotal]).'</td>';
      $totalRows.='<td></td>';
    $totalRows.='</tr>';		
    $totalRows.='<tr class="totalrow iva">';
      $totalRows.='<td>IVA</td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td class="totalprice amount right"><span class="currency">'.($currencyId == CURRENCY_USD?'US$':'C$').'</span>'.$this->Form->input('Invoice.price_iva',['label'=>false,'type'=>'decimal','readonly'=>true,'default'=>$ivatotal]).'</td>';
      $totalRows.='<td></td>';
    $totalRows.='</tr>';		
    $totalRows.='<tr class="totalrow total">';
      $totalRows.='<td>Total</td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td></td>';
      $totalRows.='<td class="totalprice amount right"><span class="currency">'.($currencyId == CURRENCY_USD?'US$':'C$').'</span>'.$this->Form->input('Invoice.price_total',['label'=>false,'type'=>'decimal','readonly'=>true,'default'=>$total]).'</td>';
      $totalRows.='<td></td>';
    $totalRows.='</tr>';	
    
    $productTableBody='<tbody>'.$productTableRows.$totalRows.'</tbody>';
		echo '<table id="productos">'.$productTableHead.$productTableBody.'</table>';
	}
	else {
		echo '<h2>No hay productos para esta orden de venta</h2>';
	}
?>
<script>
	$(document).ajaxComplete(function() {	
		$('td.productid option:not(:selected)').attr('disabled', true);
	});
</script>