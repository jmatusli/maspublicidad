<div class="invoices imprimirVenta fullwidth">
<?php
  function monthOfYear($monthString){
    $monthsOfYear=["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
    return $monthsOfYear[(int)$monthString-1];
  }

	//pr($invoice);
	//$nowDate=date('Y-m-d');
	//$nowDateTime=new DateTime($nowDate);
  $invoiceDateTime=new DateTime($invoice['Invoice']['invoice_date']);
  if ($invoice['Invoice']['bool_credit']){
	//	$dueDate=new DateTime($invoice['Invoice']['due_date']);
  }
	$output='';
  $output.='<div style="float:left;">';    
    $output.='<div id="invoicePrint" style="width:202mm;padding:0mm;margin-top:-8mm;">';  
      $output.='<div class="left_spacer">';
      $output.='</div>';
      $output.='<div class="top_banner">';
        $output.='<div class="top_banner_background noprint" >';
        $output.='</div>';
        $output.='<div class="top_banner_data">';
          $output.='<div class="invoice_code noprint">'.$invoice['Invoice']['invoice_code'].'</div>';
          $output.='<div class="invoice_cash">'.($invoice['Invoice']['bool_credit']?'':'X').'</div>';
          $output.='<div class="invoice_credit">'.($invoice['Invoice']['bool_credit']?'X':'').'</div>';
        $output.='</div>';
      $output.='</div>';
      $output.='<div class="client_details" style="font-size:15pt;">';
        $output.='<div class="client_details_background noprint" >';
        $output.='</div>';
        $output.='<div class="client_name" style="font-size:'.(strlen($invoice['Client']['name'])<35?"15pt":"10pt").';">'.($invoice['Client']['bool_generic']?$invoice['Invoice']['client_name']:$invoice['Client']['name']).'</div>';
        $output.='<div class="client_ruc" >'.(!empty($invoice['Invoice']['client_ruc']) || $invoice['Client']['bool_generic']?$invoice['Invoice']['client_ruc']:$invoice['Client']['ruc']).'</div>';
        $output.='<div class="client_reference">'.$invoice['Invoice']['reference'].'</div>';
      $output.='</div>'; 
      $output.='<div class="invoice_date" style="font-size:15pt;">';
        $output.='<div class="invoice_date_background noprint" >';
        $output.='</div>';
        $output.='<div class="invoice_date_day">'.$invoiceDateTime->format('d').'</div>';
        $output.='<div class="invoice_date_month" >'.$invoiceDateTime->format('m').'</div>';
        $output.='<div class="invoice_date_year">'.$invoiceDateTime->format('Y').'</div>';
      $output.='</div>';        
      
      $totalQuantity=0;
      $totalPrice=0;
          
      $output.='<div class="invoice_products">';
        $output.='<div class="invoice_products_background noprint" >';
        $output.='</div>';
        $output.='<table class="invoice_products_table">';
          $output.='<tbody>';
          for ($i =0;$i<INVOICE_ARTICLES_MAX;$i++){
            if (count($invoice['InvoiceProduct'])>$i){
              $totalQuantity+=$invoice['InvoiceProduct'][$i]['product_quantity'];
              $totalPrice+=$invoice['InvoiceProduct'][$i]['product_total_price'];
              
              $output.='<tr style="font-size:'.$invoiceProductFontSize.'pt;background:none;">';
                $output.='<td class="centered" style="width:32mm;max-width:32mm;">'.number_format($invoice['InvoiceProduct'][$i]['product_quantity'],0,".",",").'</td>';
                $output.='<td style="width:98mm;max-width:98mm;">'.str_replace("\n","<br/>",$invoice['InvoiceProduct'][$i]['product_description']).'</td>';
                $output.='<td class="centered" style="width:30mm;max-width:30mm;"><span class="amountright">'.number_format($invoice['InvoiceProduct'][$i]['product_unit_price'],2,".",",").'</span></td>';
                $output.='<td class="centered" style="width:25mm;max-width:25mm;"><span class="amountright">'.number_format($invoice['InvoiceProduct'][$i]['product_total_price'],2,".",",").'</span></td>';
              $output.='</tr>';
            }
            else {
              if ($i==count($invoice['InvoiceProduct'])){
                $output.='<tr>';
                  $output.='<td class="centered" colspan="4">';
                  $output.='------------------------------------------------- ÚLTIMA LÍNEA -----------------------------------';
                  $output.='</td>';
                $output.='</tr>';
              }
              else {
                 $output.='<tr>';
                  $output.='<td></td>';
                  $output.='<td>&nbsp;</td>';
                  $output.='<td>&nbsp;</td>';
                  $output.='<td>&nbsp;</td>';
                $output.='</tr>';
              }
            }  
          }
          $output.='</tbody>';
        $output.='</table>';
      $output.='</div>';
      
      $output.='<div class="invoice_conditions">';
        $output.='<div class="invoice_conditions_background noprint" >';
        $output.='</div>';
        $output.='<div class="total_cs">'.($invoice['Invoice']['bool_credit']?number_format($invoice['Invoice']['price_total_cs'],2,".",","):"").'</div>';
        $output.='<div class="total_usd" >'.($invoice['Invoice']['bool_credit']?number_format($invoice['Invoice']['price_total_usd'],2,".",","):"").'</div>';
      $output.='</div>';
      
      $output.='<div class="'.($invoice['Invoice']['price_total'] < 1000000?"invoice_totals":"invoice_totals_big").'" style="font-size:13pt;line-height:13pt;font-weight:700;">';
        $output.='<div class="invoice_totals_background noprint" >';
        $output.='</div>';
        $output.='<div class="invoice_subtotal">'.$invoice['Currency']['abbreviation'].' '.number_format($invoice['Invoice']['price_subtotal'],2,".",",").'</div>';
        $output.='<div class="invoice_iva" >'.$invoice['Currency']['abbreviation'].' '.number_format($invoice['Invoice']['price_iva'],2,".",",").'</div>';
        $output.='<div class="invoice_total">'.$invoice['Currency']['abbreviation'].' '.number_format($invoice['Invoice']['price_total'],2,".",",").'</div>';
      $output.='</div>';
      
      $output.='<div class="invoice_signatures noprint">';
        $output.='<div class="invoice_signatures_background noprint" >';
        $output.='</div>';
      $output.='</div>';
    $output.='</div>';
  $output.='</div>';  
  $output.='<div class="noprint" style="float:left;">';  
    $output.='<div class="noprint" style="width:100mm;text-align:center;padding:0mm;">';  
      $output.=$this->Html->link('Detalle Factura',['controller'=>'Invoices','action'=>'view',$invoice['Invoice']['id']],['class'=>'btn btn-primary']);
    $output.="</div>";
  $output.='</div>';
	echo mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');
?>
</div>