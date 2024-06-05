<style>
	table {
		width:100%;
	}
	
	div, span {
		font-size:1em;
	}
	.small {
		font-size:0.9em;
	}
	.big{
		font-size:1.5em;
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
	.totalrow td{
		font-weight:bold;
		background-color:#BFE4FF;
	}
	
	.bordered tr th, 
	.bordered tr td
	{
		font-size:0.7em;
		border-width:1px;
		border-style:solid;
		border-color:#000000;
		vertical-align:top;
	}
	td span.right
	{
		font-size:1em;
		display:inline-block;
		width:65%;
		float:right;
		margin:0em;
	}
	
	img.resize {
		width:150px; /* you can use % */
		height: auto;
	}
</style>
<?php
	$invoiceDate=$invoice['Invoice']['invoice_date'];
	$invoiceDateTime=new DateTime($invoiceDate);
	$nowDate=date('Y-m-d');
	$nowDateTime=new DateTime($nowDate);
	$url="img/logo.jpg";
	$imageurl=$this->App->assetUrl($url,array(),true);
	
	$output="";
	$output.="<table>";
		$output.="<tr>";
			$output.="<td class='bold' style='width:30%;'><img src='".$imageurl."' class='resize'></img></td>";		
			$output.="<td class='centered big' style='width:40%;'>".strtoupper(COMPANY_NAME)."<br/>FACTURA<br/>".$invoice['Invoice']['invoice_code'].($invoice['Invoice']['bool_annulled']?" (Anulada)":"")."</td>";
			$output.="<td class='bold' style='width:30%;'>MANAGUA, ".$invoiceDateTime->format('d-m-Y')."</td>";
		$output.="</tr>";
	$output.="</table>";
	
	$output.="<table>";
		$output.="<tr>";
			$output.="<td style='width:50%'>";
        $output.="<div>Cliente: <span class='underline'>".($invoice['Client']['bool_generic']?$invoice['Invoice']['client_name']:$invoice['Client']['name'])."</span></div>";
			
			$output.="</td>";
			$output.="<td style='width:50%'>";
			if (!empty($invoice['InvoiceSalesOrder'])){
				//pr($invoice['InvoiceSalesOrder'][0]['SalesOrder']);
				$output.="<div>Contacto: <span class='underline'>".$invoice['InvoiceSalesOrder'][0]['SalesOrder']['Quotation']['Contact']['fullname']."</span></div>";
			}
			//if (!empty($invoice['Quotation']['Contact']['fullname'])){
			//	$output.="<div>Contacto: <span class='underline'>".$invoice['Quotation']['Contact']['fullname']."</span></div>";
			//}
			else {
				$output.="<div>Contacto: <span class='underline'>-</span></div>";
			}
			$output.="</td>";
		$output.="</tr>";
			
		$output.="<tr>";
			$output.="<td style='width:30%'>";
			
			$userArray=array();
			foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){	
				if (!in_array($invoiceSalesOrder['User']['id'],$userArray)){
					$userArray[]=$invoiceSalesOrder['User']['id'];
					$output.="<div>";
						$output.="Vendedor: <span class='underline'>".$invoiceSalesOrder['User']['first_name']." ".$invoiceSalesOrder['User']['last_name']."</span>";
					$output.="</div>";
				}				
			}
			$output.="</td>";
			if (!empty($invoice['Invoice']['User']['phone'])){
				$output.="<td style='width:20%'>";
				$output.="<div>Teléfono: <span class='underline'>".$invoice['User']['phone']."</span></div>";
				$output.="</td>";
			}
			if (!empty($invoice['Invoice']['User']['email'])){
				$output.="<td style='width:50%'>";
				$output.="<div>Correo: <span class='underline'>".$invoice['User']['email']."</span></div>";
				$output.="</td>";
			}
		$output.="</tr>";
		
	$output.="</table>";
	
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	
	if (!empty($invoice['InvoiceProduct'])){
		$output.="<h3>".__('Productos en esta Factura')."</h3>";
		$output.="<table cellpadding = '0' cellspacing = '0'>";
			$output.="<tr>";
				$output.="<th>".__('Product Quantity')."</th>";
				$output.="<th>".__('Product')."</th>";
				$output.="<th>".__('Product Description')."</th>";
				$output.="<th>".__('Product Unit Price')."</th>";
				$output.="<th>".__('Product Total Price')."</th>";
			$output.="</tr>";
			
		$currencyClass="";
		if ($invoice['Currency']['id']==CURRENCY_CS){
			$currencyClass="CScurrency";
		}
		elseif ($invoice['Currency']['id']==CURRENCY_USD){
			$currencyClass="USDcurrency";
		}
		$totalProductQuantity=0;
		foreach ($invoice['InvoiceProduct'] as $invoiceProduct){
			$output.="<tr>";
				$output.="<td class='amount'><span class='amount right'>".$invoiceProduct['product_quantity']."</span></td>";
				//$output.="<td>".$this->Html->link($invoiceProduct['Product']['name'],array('controller'=>'products','action'=>'view',$invoiceProduct['Product']['name']),array('target'=>'_blank'))."</td>";
				$output.="<td>".$invoiceProduct['Product']['name']."</td>";
				$output.="<td>".str_replace("\n","<br/>",$invoiceProduct['product_description'])."</td>";
				$output.="<td class='amount ".$currencyClass."'><span class='currency'>".$invoice['Currency']['abbreviation']."</span><span class='amount right'>".number_format($invoiceProduct['product_unit_price'],2,".",",")."</td>";
				$output.="<td class='amount ".$currencyClass."'><span class='currency'>".$invoice['Currency']['abbreviation']."</span><span class='amount right'>".number_format($invoiceProduct['product_total_price'],2,".",",")."</span></td>";
			$output.="</tr>";
		}
			$output.="<tr class='totalrow'>";
				$output.="<td class='centered bold'>".$totalProductQuantity."</td>";
				$output.="<td></td>";
				$output.="<td></td>";
				$output.="<td></td>";
				$output.="<td class='amount ".$currencyClass."'><span class='currency'>".$invoice['Currency']['abbreviation']."</span><span class='amount right'>".number_format($invoice['Invoice']['price_subtotal'],2,".",",")."</span></td>";
				$output.="<td></td>";
			$output.="</tr>";
			$output.="<tr class='totalrow'>";
				$output.="<td>IVA</td>";
				$output.="<td></td>";
				$output.="<td></td>";
				$output.="<td></td>";
				$output.="<td class='amount ".$currencyClass."'><span class='currency'>".$invoice['Currency']['abbreviation']."</span><span class='amount right'>".number_format($invoice['Invoice']['price_iva'],2,".",",")."</span></td>";
				$output.="<td></td>";
			$output.="</tr>";
			$output.="<tr class='totalrow'>";
				$output.="<td>Total</td>";
				$output.="<td></td>";
				$output.="<td></td>";
				$output.="<td></td>";
				$output.="<td class='amount ".$currencyClass."'><span class='currency'>".$invoice['Currency']['abbreviation']."</span><span class='amount right'>".number_format($invoice['Invoice']['price_total'],2,".",",")."</span></td>";
				$output.="<td></td>";
			$output.="</tr>";
		$output.="</table>";
		
		$output.="<div><span class='bold '>&nbsp;</span></div>";
		$output.="<div><span class='bold '>&nbsp;</span></div>";
		$currentDateTime=new DateTime();
		$output.="Pdf generado el ".$currentDateTime->format("d/m/Y H:i:s");
	}
	
	echo mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');
?>
	