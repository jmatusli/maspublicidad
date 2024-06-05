<style>
	#header {
		position:relative;
	}
	
	#header div.imagecontainer {
		width:47%;
		padding-left:3%;
		clear:none;
	}
	
	img.resize {
		height: auto;
		width:150px;
	}
	
	img.smallimage {
		height: auto;
		width:75px;
	}
	
	#header #headertext {
		width:50%;
		position:absolute;
		height:auto;
		vertical-align:bottom;
		bottom:0em;
		right:0em;
		margin-bottom:0;
		text-align:center;
		font-size:0.85em;		
	}
	
	div.separator {
		border-bottom:4px solid #000000;
	}
	
	div.background {
		position:relative;
	}

	div, span {
		font-size:1em;
	}
	.title{
		font-size:2.5em;
	}
	.big{
		font-size:1.5em;
	}
	.small {
		font-size:0.9em;
	}
	
	.centered{
		text-align:center;
	}
	.right{
		text-align:right;
	}
	div.right{
		padding-right:1em;
	}
	
	span {
		margin-left:0.5em;
	}
	.bold{
		font-weight:bold;
	}
	.underline{
		text-decoration:underline;
	}
	
	div.pagecentered {
		width:90%;
		margin-left:auto;
		margin-right:auto;
	}
	
	div.rounded {
		padding:1em;
		border:solid #000000 1px;
		-moz-border-radius: 20px;
		-webkit-border-radius: 20px;
		border-radius: 20px;
	}
	
	div.rounded>div {
		display:block;
		clear:left;
	}
	div.rounded>div:not(:first-child) {
		display:inline-block;
	}
	
	table {
		width:100%;
		border-spacing:0;
	}
	
	table.pagecentered {
		width:90%;
		margin-left:auto;
		margin-right:auto;
	}
	
	table.bordered {
		border-collapse:collapse; 
	}
	.bordered tr th, 
	.bordered tr td
	{
		font-size:0.8em;
		border-width:2px;
		border-style:solid;
		
		border-color:#000000;
		vertical-align:top;
	}
	td.noleftbottomborder {
		border:0px!important;
		border-color:#FFFFFF;
	}
	td span.right
	{
		font-size:1em;
		display:inline-block;
		width:65%;
		float:right;
		margin:0em;
	}
	.totalrow td{
		font-weight:bold;
		background-color:#BFE4FF;
	}	
</style>
<?php
	$url="img/logo_pdf.jpg";
	$imageurl=$this->App->assetUrl($url,array(),true);
	$currentDateTime=new DateTime();
	
	$header="<div id='header'>";
		$header.="<div class='imagecontainer'>";
			$header.="<img src='".$imageurl."' class='resize'></img>";		
		$header.="</div>";	
		$header.="<div id='headertext'>";
			$header.="<div><span class='right' style='font-size:0.7em;display:inline-block;float:right;'>Pdf generado el ".$currentDateTime->format("d/m/Y H:i:s")."</span></div>";
			$header.="<div><span class='bold'>&nbsp;</span></div>";
			$header.="<div>".COMPANY_URL." &#183; ".COMPANY_MAIL."</div>";
			$header.="<div>Dir:".COMPANY_ADDRESS."</div>";
			$header.="<div>Tel:".COMPANY_PHONE."</div>";
		$header.="</div>";	
	$header.="</div>";		
	$header.="<div class='separator'>&nbsp;</div>";
	$header.="<div><span class='bold '>&nbsp;</span></div>";
	
	$output="";
	$output.=$header;
  
	$output.="<table>";
		$output.="<tr>";
			$output.="<td class='title bold centered'>";
				$output.="<div><span>Orden de Venta</span></div>";
			$output.="</td>";
		$output.="</tr>";
		$output.="<tr>";
			$output.="<td class='centered'>";
				$output.="<div><span>RUC No.: ".COMPANY_RUC."</span></div>";
			$output.="</td>";
		$output.="</tr>";
	$output.="</table>";
  
  if ($salesOrder['SalesOrder']['bool_authorized']){
    $output.='<p style="text-align:center;font-size:1.3em;font-weight:700;">AUTORIZADO</p>';
    
  }
  else {
    $output.='<p style="text-align:center;font-size:1.3em;font-weight:700;color:#ff0000">FALTA AUTORIZACIÓN</p>';
  }
		
	$salesOrderDate=$salesOrder['SalesOrder']['sales_order_date'];
	$salesOrderDateTime=new DateTime($salesOrderDate);
	$nowDate=date('Y-m-d');
	$nowDateTime=new DateTime($nowDate);
	$url="img/logo.jpg";
	$imageurl=$this->App->assetUrl($url,array(),true);
	
	//$output="";
	//$output.="<table>";
	//	$output.="<tr>";
	//		$output.="<td class='bold' style='width:30%;'><img src='".$imageurl."' class='resize'></img></td>";		
	//		$output.="<td class='centered big' style='width:40%;'>".strtoupper(COMPANY_NAME)."<br/>ORDEN DE VENTA<br/>".$salesOrder['SalesOrder']['sales_order_code'].($salesOrder['SalesOrder']['bool_annulled']?" (Anulada)":"")."</td>";
	//		$output.="<td class='bold' style='width:30%;'>MANAGUA, ".$salesOrderDateTime->format('d-m-Y')."</td>";
	//	$output.="</tr>";
	//$output.="</table>";
	/*
	$output.="<table>";
		$output.="<tr>";
			$output.="<td style='width:50%'>";
			$output.="<div>Cliente: <span class='underline'>".$salesOrder['Quotation']['Client']['name']."</span></div>";
			$output.="</td>";
			$output.="<td style='width:50%'>";
			$output.="<div>Contacto: <span class='underline'>".$salesOrder['Quotation']['Contact']['fullname']."</span></div>";
			$output.="</td>";
		$output.="</tr>";
			
		$output.="<tr>";
			$output.="<td style='width:30%'>";
			$output.="<div>Vendedor: <span class='underline'>".$salesOrder['Quotation']['User']['first_name']." ".$salesOrder['Quotation']['User']['last_name']."</span></div>";
			$output.="</td>";
			if (!empty($salesOrder['Quotation']['User']['phone'])){
				$output.="<td style='width:20%'>";
				$output.="<div>Teléfono: <span class='underline'>".$salesOrder['Quotation']['User']['phone']."</span></div>";
				$output.="</td>";
			}
			if (!empty($salesOrder['Quotation']['User']['email'])){
				$output.="<td style='width:50%'>";
				$output.="<div>Correo: <span class='underline'>".$salesOrder['Quotation']['User']['email']."</span></div>";
				$output.="</td>";
			}
		$output.="</tr>";
		
	$output.="</table>";
	*/
	$email="-";
	if (!empty($salesOrder['Quotation']['Contact']['email'])){
		$email=$salesOrder['Quotation']['Contact']['email'];
	}
	
	$phone="-";
	if (!empty($salesOrder['Quotation']['Client']['phone'])){
		$phone=$salesOrder['Quotation']['Client']['phone'];
	}
	elseif (!empty($salesOrder['Quotation']['Contact']['phone'])){
		$phone=$salesOrder['Quotation']['Contact']['phone'];
	}
	
	$cell="-";
	if (!empty($salesOrder['Quotation']['Client']['cell'])){
		$cell=$salesOrder['Quotation']['Client']['cell'];
	}
	elseif (!empty($salesOrder['Quotation']['Contact']['cell'])){
		$cell=$salesOrder['Quotation']['Contact']['cell'];
	}
	
	$ruc="-";
	if (!empty($salesOrder['Quotation']['Client']['ruc'])){
		$ruc=$salesOrder['Quotation']['Client']['ruc'];
	}
	
	$output.="<div class='rounded pagecentered'>";
		$output.="<div style='width:50%;clear:left;'>Fecha: <span class='underline'>".$salesOrderDateTime->format('d-m-Y')."</span></div>";
		$output.="<div style='width:50%;clear:right;display:inline-block;'>Orden No.: <span class='underline'>".$salesOrder['SalesOrder']['sales_order_code']."</span></div>";
		
		if (!empty($salesOrder['Quotation']['Contact']['fullname'])){
			$output.="<div style='width:50%;display:block;'>Cliente: <span class='underline'>".$salesOrder['Quotation']['Client']['name']."</span></div>";
			$output.="<div style='width:50%;'>Contacto: <span class='underline'>".$salesOrder['Quotation']['Contact']['fullname']."</span></div>";
		}
		else {
			$output.="<div style='width:100%;display:block;'>Cliente: <span class='underline'>".$salesOrder['Quotation']['Client']['name']."</span></div>";
		}
		
		$output.="<div style='width:100%;display:block;'>Correo: <span class='underline'>".$email."</span></div>";
		
		$output.="<div style='width:33%;display:block;'>Teléfono: <span class='underline'>".$phone."</span></div>";
		$output.="<div style='width:33%;'>Celular: <span class='underline'>".$cell."</span></div>";
		$output.="<div style='width:33%;'>RUC No.: <span class='underline'>".$ruc."</span></div>";
	$output.="</div>";
	
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	
	$output.="<table class='pagecentered'>";
		$output.="<tr>";
			$output.="<td style='width:50%'>";
				$output.="<div>Ejecutivo: <span class='underline'>".$salesOrder['Quotation']['User']['first_name']." ".$salesOrder['Quotation']['User']['last_name']."</span></div>";
			$output.="</td>";
			$output.="<td style='width:50%'>";
				$output.="<div>Celular: <span class='underline'>".$salesOrder['Quotation']['User']['phone']."</span></div>";
			$output.="</td>";
		$output.="</tr>";
	$output.="</table>";
	
	//$output.="<div><span class='bold '>&nbsp;</span></div>";
  
  if (!empty($salesOrder['SalesOrderRemark'])){
    $output.='<table  style="font-size:80%">';
      $output.="<thead>";
        $output.="<tr>";
          $output.="<th>Fecha</th>";
          $output.="<th>Vendedor</th>";
          $output.="<th>Remarca</th>";
        $output.="</tr>";
      $output.="</thead>";
      $output.="<tbody>";							
      foreach ($salesOrder['SalesOrderRemark'] as $salesOrderRemark){
        //pr($salesOrderRemark);
        $remarkDateTime=new DateTime($salesOrderRemark['remark_datetime']);
        $output.="<tr>";
          $output.="<td>".$remarkDateTime->format('d-m-Y H:i')."</td>";
          $output.="<td>".$salesOrderRemark['User']['first_name']." ".$salesOrderRemark['User']['last_name']."</td>";
          $output.="<td>".$salesOrderRemark['remark_text']."</td>";
        $output.="</tr>";
      }
      $output.="</tbody>";
    $output.="</table>";  
  }
  else {
    $output.="<div><span class='bold '>&nbsp;</span></div>";
  }
  
  
	if (!empty($salesOrder['SalesOrderProduct'])){
		$output.="<h3>".__('Productos de esta Orden de Venta')."</h3>";
		$output.="<table class='bordered'>";
			$output.="<tr>";
				//$output.="<th>".__('Product Id')."</th>";
				$output.="<th style='width:10%;'>".__('Cantidad')."</th>";
				$output.="<th style='width:50%;'>".__('Product Description')."</th>";
				$output.="<th style='width:15%;'>".__('P.Unitario')."</th>";
				$output.="<th style='width:15%;'>".__('Sub-Total')."</th>";
				//$output.="<th style='width:10%;'>".__('Status')."</th>";
				//echo"<th class='actions'>".__('Actions')."</th>";
			$output.="</tr>";
		$totalProductQuantity=0;
		foreach ($salesOrder['SalesOrderProduct'] as $salesOrderProduct){
			$totalProductQuantity+=$salesOrderProduct['product_quantity'];
			if ($salesOrderProduct['currency_id']==CURRENCY_CS){
				$classCurrency=" class='CScurrency'";
			}
			elseif ($salesOrderProduct['currency_id']==CURRENCY_USD){
				$classCurrency=" class='USDcurrency'";
			}
			$output.="<tr>";
				//$output.="<td>".$this->Html->link($salesOrderProduct['Product']['name'],array('controller'=>'products','action'=>'view',$salesOrderProduct['Product']['id']),array('target'=>'_blank'))."</td>";
				$output.="<td class='centered' style='width:10%;'>".number_format($salesOrderProduct['product_quantity'],0,".",",")."</td>";
				$output.="<td style='width:50%;'>".str_replace("\n","<br/>",$salesOrderProduct['product_description'])."</td>";
				$output.="<td  style='width:15%;' ".$classCurrency."><span class='currency'>".$salesOrder['Currency']['abbreviation']."</span><span class='amount right'>".number_format($salesOrderProduct['product_unit_price'],2,".",",")."</span></td>";
				$output.="<td style='width:15%;' ".$classCurrency."><span class='currency'>".$salesOrder['Currency']['abbreviation']."</span><span class='amount right'>".number_format($salesOrderProduct['product_total_price'],2,".",",")."</span></td>";
				//$output.="<td class='centered'>".$salesOrderProduct['SalesOrderProductStatus']['status']."</td>";
			$output.="</tr>";
		}
			$output.="<tr>";
				//$output.="<td class='noleftbottomborder'></td>";
				$output.="<td class='centered bold'>".$totalProductQuantity."</td>";
				
				//if ($quotation['Quotation']['bool_print_images']){
				//	$output.="<td class='noleftbottomborder'></td>";
				//}
				$output.="<td class='noleftbottomborder'></td>";
				//if ($quotation['Quotation']['bool_print_delivery_time']){
				//	$output.="<td class='noleftbottomborder'></td>";
				//}
				$output.="<td><span class='bold'>Subtotal</span></td>";
				$output.="<td><span class='currency'>".$salesOrder['Currency']['abbreviation']."</span><span class='right'>".number_format($salesOrder['SalesOrder']['price_subtotal'],2,".",",")."</span></td>";
				//$output.="<td></td>";
			$output.="</tr>";
			$output.="<tr>";
				$output.="<td class='noleftbottomborder'></td>";
				//if ($quotation['Quotation']['bool_print_images']){
				//	$output.="<td class='noleftbottomborder'></td>";
				//}
				$output.="<td class='noleftbottomborder'></td>";
				//if ($quotation['Quotation']['bool_print_delivery_time']){
				//	$output.="<td class='noleftbottomborder'></td>";
				//}
				$output.="<td><span class='bold'>IVA</span></td>";
				$output.="<td><span class='currency'>".$salesOrder['Currency']['abbreviation']."</span><span class='right'>".number_format($salesOrder['SalesOrder']['price_iva'],2,".",",")."</span></td>";
				//$output.="<td></td>";
			$output.="</tr>";
			$output.="<tr>";
				$output.="<td class='noleftbottomborder'></td>";
				//if ($quotation['Quotation']['bool_print_images']){
				//	$output.="<td class='noleftbottomborder'></td>";
				//}
				$output.="<td class='noleftbottomborder'></td>";
				//if ($quotation['Quotation']['bool_print_delivery_time']){
				//	$output.="<td class='noleftbottomborder'></td>";
				//}
				$output.="<td><span class='bold'>Total</span></td>";
				$output.="<td><span class='currency'>".$salesOrder['Currency']['abbreviation']."</span><span class='right'>".number_format($salesOrder['SalesOrder']['price_total'],2,".",",")."</span></td>";
				//$output.="<td></td>";
			$output.="</tr>";
		$output.="</table>";
		
		$dueDate=new DateTime ($salesOrder['Quotation']['due_date']);
		
		$output.="<div style='padding:2em;'>";
			$output.="<div>Condiciones</div>";
			$output.="<ul>";
				$output.="<li>".$salesOrder['Quotation']['remark_delivery']."</li>";
				$output.="<li>La forma de pago será ".$salesOrder['Quotation']['payment_form']."</li>";
				$output.="<li>".$salesOrder['Quotation']['remark_cheque']."</li>";
				$output.="<li>Validez de la cotización ".$validityQuotation." días (fecha de vencimiento: ".$dueDate->format('d-m-Y').")</li>";
				$output.="<li>".$salesOrder['Quotation']['remark_elaboration']."</li>";
			$output.="</ul>";
		$output.="</div>";
		
		$output.="<table class='pagecentered'>";
			$output.="<tr>";
				$output.="<td style='width:40%;'>".$salesOrder['Quotation']['text_client_signature']."</td>";
				$output.="<td style='width:60%;border-bottom: 1px solid black;'><span> &nbsp; </span></td>";
			$output.="</tr>";
			$output.="<tr>";
				$output.="<td style='width:40%;'>".$salesOrder['Quotation']['text_authorization']."</td>";
				$output.="<td style='width:60%;border-bottom: 1px solid black;'><span>";
				if (!empty($salesOrder['AuthorizingUser']['id'])){
          $output.='Autorizado electrónicamente por <br/>' .$salesOrder['AuthorizingUser']['first_name']." ".$salesOrder['AuthorizingUser']['last_name'];
          switch ($salesOrder['AuthorizingUser']['role_id']){
            case ROLE_ADMIN:
              $output.=", Gerente";
              break;
            case ROLE_ASSISTANT:
              $output.=", Asistente Ejecutiva";
              break;
            case ROLE_DEPARTMENT_SUPERVISOR_SALES:
              $output.=", Supervisor de Ventas";
              break;  
            case ROLE_SALES_EXECUTIVE:
              $output.=", Ejecutivo de Ventas";
              break;
          }
        }
        else {
          $output.="FIRMA DIGITAL PENDIENTE<br/>";
        }
				$output.="</span></td>";
			$output.="</tr>";
			$output.="<tr>";
				$output.="<td style='width:40%;'>".$salesOrder['Quotation']['text_seal']."</td>";
				$output.="<td style='width:60%;'></td>";
			$output.="</tr>";
			$output.="<tr>";
				$output.="<td style='width:40%;'>Observación</td>";
				$output.="<td style='width:60%;'>".$salesOrder['SalesOrder']['observation']."</td>";
			$output.="</tr>";
		$output.="</table>";
	}
	
	echo mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');
?>
	