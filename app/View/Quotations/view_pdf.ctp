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
		width:300px;
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
	.verysmall {
		font-size:0.8em;
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
	$imageurl=$this->App->assetUrl($url);
	
	$header="<div id='header'>";
		$header.="<div class='imagecontainer'>";
			$header.="<img src='".$imageurl."' class='resize'></img>";		
		$header.="</div>";	
		$header.="<div id='headertext'>";
			$header.="<div><span class='bold'>&nbsp;</span></div>";
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

	
	$url="img/logo_watermark2.jpg";
	$imageurl=$this->App->assetUrl($url);
	//$output.="<div>".$imageurl."</div>";
	$output.="<div class='background'>";
		//$output.="<img src='".$url."'/>";
	$output.="</div>";
	
	$output.="<table>";
		$output.="<tr>";
			$output.="<td class='title bold centered'>";
				$output.="<div><span>Cotización</span></div>";
			$output.="</td>";
		$output.="</tr>";
		$output.="<tr>";
			$output.="<td class='centered'>";
				$output.="<div><span>RUC No.: ".COMPANY_RUC."</span></div>";
			$output.="</td>";
		$output.="</tr>";
	$output.="</table>";
	
	$quotationDate=date("Y-m-d",strtotime($quotation['Quotation']['quotation_date']));
	$quotationDateTime=new DateTime($quotationDate);
	$dueDate=date("Y-m-d",strtotime($quotation['Quotation']['due_date']));
	$dueDateTime=new DateTime($dueDate);
	$nowDate=date('Y-m-d');
	$nowDateTime=new DateTime($nowDate);
	
	$email="-";
	if (!empty($quotation['Contact']['email'])){
		$email=$quotation['Contact']['email'];
	}
	
	$phone="-";
	if (!empty($quotation['Client']['phone'])){
		$phone=$quotation['Client']['phone'];
	}
	elseif (!empty($quotation['Contact']['phone'])){
		$phone=$quotation['Contact']['phone'];
	}
	
	$cell="-";
	if (!empty($quotation['Client']['cell'])){
		$cell=$quotation['Client']['cell'];
	}
	elseif (!empty($quotation['Contact']['cell'])){
		$cell=$quotation['Contact']['cell'];
	}
	
	$ruc="-";
	if (!empty($quotation['Client']['ruc'])){
		$ruc=$quotation['Client']['ruc'];
	}
	
	$output.="<div class='rounded pagecentered'>";
		$output.="<div style='width:50%;clear:left;'>Fecha: <span class='underline'>".$quotationDateTime->format('d-m-Y')."</span></div>";
		$output.="<div style='width:50%;clear:right;display:inline-block;'>Cot. No.: <span class='underline'>".$quotation['Quotation']['quotation_code']."</span></div>";
		
		if (!empty($quotation['Contact']['fullname'])){
			$output.="<div style='width:50%;display:block;'>Cliente: <span class='underline'>".$quotation['Client']['name']."</span></div>";
			$output.="<div style='width:50%;'>Contacto: <span class='underline'>".$quotation['Contact']['fullname']."</span></div>";
		}
		else {
			$output.="<div style='width:100%;display:block;'>Cliente: <span class='underline'>".$quotation['Client']['name']."</span></div>";
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
				$output.="<div>Ejecutivo: <span class='underline'>".$quotation['User']['first_name']." ".$quotation['User']['last_name']."</span></div>";
			$output.="</td>";
			$output.="<td style='width:50%'>";
				$output.="<div>Celular: <span class='underline'>".$quotation['User']['phone']."</span></div>";
			$output.="</td>";
		$output.="</tr>";
	$output.="</table>";
	
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	
	if (!empty($quotation['QuotationProduct'])){
		$output.="<table  class='bordered'>";
			$output.="<tr>";
				$output.="<th style='width:10%;'>".__('Cantidad')."</th>";
				if ($quotation['Quotation']['bool_print_images']){
					$output.="<th style='width:10%;'>".__('Imagen')."</th>";
				}
				if (!$quotation['Quotation']['bool_print_images'] && !$quotation['Quotation']['bool_print_delivery_time']){
					$output.="<th style='width:50%;'>".__('Descripción del Servicio')."</th>";
				}
				else {
					if ($quotation['Quotation']['bool_print_images'] && $quotation['Quotation']['bool_print_delivery_time']){
						$output.="<th style='width:30%;'>".__('Descripción del Servicio')."</th>";
					}
					else {
						$output.="<th style='width:40%;'>".__('Descripción del Servicio')."</th>";
					}
				}
				if ($quotation['Quotation']['bool_print_delivery_time']){
					$output.="<th style='width:10%;'>".__('T.de Entrega')."</th>";
				}
				$output.="<th style='width:18%;'>".__('P.Unitario')."</th>";
				$output.="<th style='width:18%;'>".__('Sub-Total')."</th>";
			$output.="</tr>";
		$totalProductQuantity=0;	
		foreach ($quotation['QuotationProduct'] as $quotationProduct){
			$totalProductQuantity+=$quotationProduct['product_quantity'];
			if ($quotationProduct['currency_id']==CURRENCY_CS){
				$classCurrency=" class='CScurrency'";
			}
			elseif ($quotationProduct['currency_id']==CURRENCY_USD){
				$classCurrency=" class='USDcurrency'";
			}
			$output.="<tr>";
				$output.="<td class='centered' style='width:10%;'>".number_format($quotationProduct['product_quantity'],0,".",",")."</td>";
				if ($quotation['Quotation']['bool_print_images']){
					if (!empty($quotationProduct['Product']['url_image'])){
						$url=$quotationProduct['Product']['url_image'];
						$productimage=$this->App->assetUrl($url);
						$output.="<td class='centered' style='width:10%;'><img src='".$productimage."' class='smallimage'></img></td>";
					}
					else {
						$output.="<td class='centered' style='width:10%;'></td>";
					}
				}
				if (!$quotation['Quotation']['bool_print_images'] && !$quotation['Quotation']['bool_print_delivery_time']){
					$output.="<td style='width:50%;padding:3px;' >".$quotationProduct['product_description']."</th>";
				}
				else {
					if ($quotation['Quotation']['bool_print_images'] && $quotation['Quotation']['bool_print_delivery_time']){
						$output.="<td style='width:30%;padding:3px;'>".$quotationProduct['product_description']."</th>";
					}
					else {
						$output.="<td style='width:40%;padding:3px;'>".$quotationProduct['product_description']."</th>";
					}
				}
				if ($quotation['Quotation']['bool_print_delivery_time']){
					$output.="<td style='width:10%;'>".$quotationProduct['delivery_time']."</td>";
				}
				$output.="<td><span class='currency' style='width:18%;'>".$quotation['Currency']['abbreviation']."</span><span class='right'>".number_format($quotationProduct['product_unit_price'],2,".",",")."</span></td>";
				$output.="<td><span class='currency' style='width:18%;'>".$quotation['Currency']['abbreviation']."</span><span class='right'>".number_format($quotationProduct['product_total_price'],2,".",",")."</span></td>";
			$output.="</tr>";
		}
			$output.="<tr>";
				//$output.="<td class='noleftbottomborder' style='width:10%;'></td>";
				$output.="<td class='centered bold'>".$totalProductQuantity."</td>";

				if ($quotation['Quotation']['bool_print_images']){
					$output.="<td class='noleftbottomborder' style='width:10%;'></td>";
				}
				if (!$quotation['Quotation']['bool_print_images'] && !$quotation['Quotation']['bool_print_delivery_time']){
					$output.="<td class='noleftbottomborder' style='width:50%;'></td>";
				}
				else {
					if ($quotation['Quotation']['bool_print_images'] && $quotation['Quotation']['bool_print_delivery_time']){
						$output.="<td class='noleftbottomborder' style='width:30%;'></td>";
					}
					else {
						$output.="<td class='noleftbottomborder' style='width:40%;'></td>";
					}
				}
				
				if ($quotation['Quotation']['bool_print_delivery_time']){
					$output.="<td class='noleftbottomborder' style='width:10%;'></td>";
				}
				$output.="<td><span class='bold' style='width:18%;'>Subtotal</span></td>";
				$output.="<td><span class='currency' style='width:18%;'>".$quotation['Currency']['abbreviation']."</span><span class='right'>".number_format($quotation['Quotation']['price_subtotal'],2,".",",")."</span></td>";
			$output.="</tr>";
			$output.="<tr>";
				$output.="<td class='noleftbottomborder'></td>";
				if ($quotation['Quotation']['bool_print_images']){
					$output.="<td class='noleftbottomborder'></td>";
				}
				$output.="<td class='noleftbottomborder'></td>";
				if ($quotation['Quotation']['bool_print_delivery_time']){
					$output.="<td class='noleftbottomborder'></td>";
				}
				$output.="<td><span class='bold' style='width:18%;'>IVA</span></td>";
				$output.="<td><span class='currency' style='width:18%;'>".$quotation['Currency']['abbreviation']."</span><span class='right'>".number_format($quotation['Quotation']['price_iva'],2,".",",")."</span></td>";
			$output.="</tr>";
			$output.="<tr>";
				$output.="<td class='noleftbottomborder'></td>";
				if ($quotation['Quotation']['bool_print_images']){
					$output.="<td class='noleftbottomborder'></td>";
				}
				$output.="<td class='noleftbottomborder'></td>";
				if ($quotation['Quotation']['bool_print_delivery_time']){
					$output.="<td class='noleftbottomborder'></td>";
				}
				$output.="<td><span class='bold' style='width:18%;'>Total</span></td>";
				$output.="<td><span class='currency' style='width:18%;'>".$quotation['Currency']['abbreviation']."</span><span class='right'>".number_format($quotation['Quotation']['price_total'],2,".",",")."</span></td>";
			$output.="</tr>";
		$output.="</table>";
	}
	
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	
	$dueDate=new DateTime ($quotation['Quotation']['due_date']);
	
	$output.="<div style='padding:0em 2em;'>";
		$output.="<div>Observaciones</div>";
		$output.="<ul>";
			$output.="<li>".$quotation['Quotation']['remark_delivery']."</li>";
			$output.="<li>La forma de pago será ".$quotation['Quotation']['payment_form']."</li>";
			$output.="<li>".$quotation['Quotation']['remark_cheque']."</li>";
			$output.="<li>Validez de la cotización ".$validityQuotation." días (fecha de vencimiento: ".$dueDate->format('d-m-Y').")</li>";
			$output.="<li>".$quotation['Quotation']['remark_elaboration']."</li>";
		$output.="</ul>";
	$output.="</div>";
	
	$output.="<table class='pagecentered'>";
		$output.="<tr>";
			$output.="<td style='width:20%;'>Observaciones</td>";
			$output.="<td style='width:80%;'>".$quotation['Quotation']['observations']."</td>";
		$output.="</tr>";
	$output.="</table>";
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$output.="<table class='pagecentered'>";
		$output.="<tr>";
			$output.="<td style='width:40%;'>".$quotation['Quotation']['text_client_signature']."</td>";
			$output.="<td style='width:60%;border-bottom: 1px solid black;'><span> &nbsp; </span></td>";
		$output.="</tr>";
		$output.="<tr>";
			$output.="<td style='width:40%;'>".$quotation['Quotation']['text_authorization']."</td>";
			$output.="<td style='width:60%;border-bottom: 1px solid black;'><span>".$quotation['Quotation']['authorization']."</span></td>";
			//$output.="<td style='width:60%;border-bottom: 1px solid black;'><span></span></td>";
		$output.="</tr>";
		$output.="<tr>";
			$output.="<td style='width:40%;'>".$quotation['Quotation']['text_seal']."</td>";
			$output.="<td style='width:60%;'></td>";
		$output.="</tr>";
		
	$output.="</table>";
	
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$currentDateTime=new DateTime();
	$output.="Pdf generado el ".$currentDateTime->format("d/m/Y H:i:s");
	
	
	$roleName="";
	switch ($quotation['User']['role_id']){
		case ROLE_ADMIN: 
			$roleName="Gerente";
			break;
		case ROLE_ASSISTANT: 
			$roleName="Asistente Ejecutivo";
			break;
		case ROLE_SALES_EXECUTIVE: 
			$roleName="Ejecutivo de Venta";
			break;	
	}
	
	echo mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');
?>
	