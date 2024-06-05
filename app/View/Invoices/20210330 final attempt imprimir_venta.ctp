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
  //$output.='<div class="container-fluid">';
    //$output.='<div class="row">';  
      //$output.='<div class="col-sm-10">'; 
      $output.='<div style="float:left;">';    
        $output.='<div id="invoicePrint" style="width:202mm;height:270mm;padding:0mm;">';  
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
          $output.='<div class="client_details">';
            $output.='<table style="font-size:15pt;">';
              $output.='<tr>';
                $output.='<td class="left noprint" style="width:25%;">CLIENTE:</td>';
                $output.='<td class="left" style="font-size:'.(strlen($invoice['Client']['name'])<35?"15pt":"10pt").';">'.$invoice['Client']['name'].'</td>';
              $output.='</tr>';
              $output.='<tr>';
                $output.='<td class="left noprint">RUC:</td>';
                $output.='<td class="left">'.$invoice['Client']['ruc'].'</td>';
              $output.='</tr>';
              $output.='<tr>';
                $output.='<td class="left noprint">REFERENCIA:</td>';
                $output.='<td class="left" style="font-size:12pt">'.$invoice['Invoice']['reference'].'</td>';
              $output.='</tr>';
                     
            $output.='</table>';
          $output.='</div>';
          $output.='<div class="invoice_date">';
            $output.='<table style="font-size:15pt;width:100%;">';
              $output.='<thead style="min-height:14mm;height:14mm;">';
                $output.='<tr>';
                  $output.='<th class="centered"> <span class="noprint">DIA</span></th>';
                  $output.='<th class="centered"> <span class="noprint">MES</span></th>';
                  $output.='<th class="centered"> <span class="noprint">AÑO</span></th>';
                $output.='</tr>';
              $output.='</thead>'; 
              $output.='<tbody>';
                $output.='<tr>';            
                  $output.='<td class="centered">'.$invoiceDateTime->format('d').'</td>';
                  $output.='<td class="centered">'.$invoiceDateTime->format('m').'</td>';
                  $output.='<td class="centered">'.$invoiceDateTime->format('Y').'</td>';
              $output.='</tr>';
              $output.='</tbody>';
            $output.='</table>';
          $output.='</div>';
          
          $output.='<div class="invoice_products">';
            $output.='<table>';
              $output.='<thead style="min-height:14mm;height:14mm;font-size:15pt;">';
                $output.='<tr>';
                  $output.='<th class="centered" style="width:35mm;"> <span class="noprint">Cantidad </th>';
                  $output.='<th style="width:102mm;"> <span class="noprint">Descripción</span></th>';
                  $output.='<th class="centered" style="width:32mm;"> <span class="noprint">P. Unitario</span></th>';
                  $output.='<th class="centered" style="width:27mm;"> <span class="noprint">Total</span></th>';
                $output.='</tr>';
              $output.='</thead>';
              
              $totalQuantity=0;
              $totalPrice=0;
              $output.='<tbody>';
              for ($i =0;$i<INVOICE_ARTICLES_MAX;$i++){
                if (count($invoice['InvoiceProduct'])>$i){
                  $totalQuantity+=$invoice['InvoiceProduct'][$i]['product_quantity'];
                  $totalPrice+=$invoice['InvoiceProduct'][$i]['product_total_price'];
                  //$output.='<tr style="font-size:12pt;">';
                  $output.='<tr style="font-size:'.$invoiceProductFontSize.'pt;">';
                    $output.='<td class="centered">'.number_format($invoice['InvoiceProduct'][$i]['product_quantity'],0,".",",").'</td>';
                    $output.='<td>'.str_replace("\n","<br/>",$invoice['InvoiceProduct'][$i]['product_description']).'</td>';
                    //$output.='<td class="centered"><span class="currency">'.$invoice['InvoiceProduct'][$i]['Currency']['abbreviation'].'</span><span class="amountright">'.number_format($invoice['InvoiceProduct'][$i]['product_unit_price'],4,".",",").'</span></td>';
                    $output.='<td class="centered"><span class="amountright">'.number_format($invoice['InvoiceProduct'][$i]['product_unit_price'],2,".",",").'</span></td>';
                    $output.='<td class="centered"><span class="amountright">'.number_format($invoice['InvoiceProduct'][$i]['product_total_price'],2,".",",").'</span></td>';
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
            $output.='<div class="noprint" style="font-size:12pt">FAVOR EMITIR CHEQUE A NOMBRE DE MAS PUBLICIDAD S.A.</div>';
            $output.='<div style="font-size:9.2pt:position:relative;">';
              $output.='<span class="left noprint" style="text-align:justify;">Pagaré a la orden de MAS PUBLICIDAD en la ciudad de Managua por la cantidad de esta <br/> factura C$ </span>';
              $output.='<span class="left bold" style="font-size:10pt; width:15mm;position:absolute;top:13mm;left:13mm;">'.number_format($invoice['Invoice']['price_total_cs'],2,".",",").'</span>';
              $output.='<span class="left noprint" style="text-align:justify;"> o US$ </span>';
              $output.='<span class="left bold" style="font-size:10pt;width:15mm;position:absolute;top:13mm;left:48mm;">'.number_format($invoice['Invoice']['price_total_usd'],2,".",",").'</span>';
              $output.='<span class="noprint" style="text-align:justify;">. Que le adeudamos por igual valor recibido a nuestra entera satisfacción. En caso de falta de pago en la fecha indicada incurriré y pagaré al acreedor interes del 2% mensual sobre lo adeudado.  Renuncio a mi domilio sujetándome al que eliga mi acreedor.  Me obligo a cancelar dicha factura al tipo de cambio respecto al dólar de los Estados Unidos de Norteamérica.</span>';
            $output.='</div>';
          $output.='</div>';
          $output.='<div class="invoice_totals">';
            $output.='<div style="min-width:33mm;width:33mm;height:35mm;float:left;font-size:15pt;line-height:15pt;font-weight:700;">
              <span class="noprint" style="width:100%;min-height:12mm;height:12mm;display:block;">SUB-TOTAL</span>
              <span class="noprint" style="width:100%;min-height:12mm;height:12mm;display:block;">I.V.A. 15%</span>
              <span class="noprint" style="width:100%;min-height:12mm;height:12mm;display:block;">TOTAL</span>
            </div>';
            $output.='<div style="width:33mm;height:35mm;float:left;font-size:12pt;line-height:15pt;font-weight:700;">  
              <span class="currency" style="width:20%;height:12mm;">'.$invoice['Currency']['abbreviation'].'</span><span class="amountright" style="width:80%;height:12mm;">'.number_format($invoice['Invoice']['price_subtotal'],2,".",",").'</span>
              <span class="currency" style="width:20%;height:12mm;">'.$invoice['Currency']['abbreviation'].'</span><span class="amountright" style="width:80%;height:12mm;">'.number_format($invoice['Invoice']['price_iva'],2,".",",").'</span>
              <span class="currency" style="width:20%;height:12mm;">'.$invoice['Currency']['abbreviation'].'</span><span class="amountright" style="width:80%;height:12mm;">'.number_format($invoice['Invoice']['price_total'],2,".",",").'</span>
            </div>';
          $output.='</div>';
          $output.='<div class="invoice_signatures noprint">';
          
            $output.='<table class="noprint" style="height:14mm;font-size:0.9rem;">';
            $output.="<tr>";
              $output.="<td class='centered' style='width:49mm;'>
                <div style='height:7mm;'>
                  <span class='centered'>_______________________________</span>
                </div>
                <div style='height:7mm;'>
                  <span class='centered'>ENTREGUÉ CONFORME</span>
                </div>        
              </td>";
              $output.="<td class='centered' style='width:98mm;'>
                     
              </td>";
              
              $output.="<td class='centered'style='width:50mm;'>
                <div style='height:7mm;'>
                  <span class='centered'>_______________________________</span>
                </div>
                <div class='centered'>  
                  <span class='centered'>RECIBÍ CONFORME</span>
                </div>        
              </td>";
           $output.="</tbody>";
          $output.="</table>";  
          
          $output.='</div>';
        $output.='</div>';
      $output.='</div>';  
      //$output.='<div class="col-sm-2 noprint">';  
      $output.='<div class="noprint" style="float:left;">';  
        //$output.='<div class="noprint" style="width:100mm;float:left;padding:0mm;">';  
        $output.='<div class="noprint" style="width:100mm;text-align:center;padding:0mm;">';  
          $output.=$this->Html->link('Detalle Factura',['controller'=>'Invoices','action'=>'view',$invoice['Invoice']['id']],['class'=>'btn btn-primary']);
        $output.="</div>";
      $output.='</div>';
    //$output.='</div>';
  //$output.='</div>';  
	echo mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');
?>
</div>