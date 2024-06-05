<style>
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
	
	#ordercode {
		position:absolute;
		left:28em;
		top:5em;
	}
	
	table {
		width:100%;
	}
	
	table.bordered {
		border-collapse:collapse; 
	}	
	.bordered tr th, 
	.bordered tr td
	{
		font-size:1em;
		border-width:1px;
		border-style:solid;
		border-color:#000000;
		vertical-align:top;
	}
	td span.right{
		font-size:1em;
		display:inline-block;
		width:65%;
		float:right;
		margin:0em;
	}
</style>
<?php
	$entryDateTime= new DateTime($entry['Entry']['entry_date']);
	$dueDateTime= new DateTime($entry['Entry']['due_date']);
	
	$output="";
	echo "<div class='viewPDF'>";
	
	$output.="<div class='centered big'>".strtoupper(COMPANY_NAME)."</div>";
	$output.="<div class='centered small'>TELÉFONOS OFICINA: <span class='bold'>".strtoupper(COMPANY_PHONE)."</span></div>";

	$output.="<div class='centered big bold'>ENTRADA ".$entry['Entry']['entry_code'].(($entry['Entry']['bool_annulled'])?" (Anulada)":"")."</div>";
	
	$output.="<table style='width:100%'>";
		$output.="<tr>";
			$output.="<td style='width:70%'>";
				$output.="<div>Proveedor:<span class='underline'>".$entry['InventoryProvider']['name']."</span></div>";
			$output.="</td>";
			$output.="<td style='width:30%'>";
				$output.="<div>Fecha:<span class='underline'>".$entryDateTime->format('d-m-Y')."</span></div>";
			$output.="</td>";
		$output.="</tr>";
	$output.="</table>";
	
	$output.="<div>Observación:".$entry['Entry']['observation']."</div>";
	$output.="<br/>";
	
	if (!empty($entry['StockMovement'])){
		if ($entry['Currency']['id']==CURRENCY_USD){
			$currencyClass="USDcurrency";
		}
		else {
			$currencyClass="CScurrency";
		}
		$output.="<h3>".__('Related Stock Movements')."</h3>";
		$output.="<table class='bordered'>";
			$output.="<tr>";
				$output.="<th>".__('Product Id')."</th>";
				$output.="<th class='centered'>".__('Product Quantity')."</th>";
				$output.="<th>".__('Product Unit Cost')."</th>";
				$output.="<th>".__('Product Total Cost')."</th>";
			$output.="</tr>";
		foreach ($entry['StockMovement'] as $stockMovement){ 
			$output.="<tr>";
				$output.="<td>".$stockMovement['InventoryProduct']['name']."</td>";
				$output.="<td class='centered'>".$stockMovement['product_quantity']."</td>";
				$output.="<td<span class='currency'>".$entry['Currency']['abbreviation']."</span><span class='amountright'>".number_format($stockMovement['product_unit_cost'],2,".",",")."</span></td>";
				$output.="<td<span class='currency'>".$entry['Currency']['abbreviation']."</span><span class='amountright'>".number_format($stockMovement['product_quantity']*$stockMovement['product_unit_cost'],2,".",",")."</span></td>";
			$output.="</tr>";
		}
			$output.="<tr class='totalrow'>";
				$output.="<td>Total</td>";
				$output.="<td></td>";
				$output.="<td></td>";
				$output.="<td class='".$currencyClass."'><span class='currency'>".$entry['Currency']['abbreviation']."</span><span class='amountright'>".number_format($entry['Entry']['cost_subtotal'],2,".",",")."</span></td>";
			$output.="</tr>";	
		$output.="</table>";
	}
	
	echo mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');
?>

	