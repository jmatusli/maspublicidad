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
	$remissionDateTime= new DateTime($remission['Remission']['remission_date']);
	$dueDateTime= new DateTime($remission['Remission']['due_date']);
	
	$output="";
	echo "<div class='viewPDF'>";
	
	$output.="<div class='centered big'>".strtoupper(COMPANY_NAME)."</div>";
	$output.="<div class='centered small'>TELÉFONOS OFICINA: <span class='bold'>".strtoupper(COMPANY_PHONE)."</span></div>";

	$output.="<div class='centered big bold'>SALIDA ".$remission['Remission']['remission_code'].(($remission['Remission']['bool_annulled'])?" (Anulada)":"")."</div>";
	
	$output.="<table style='width:100%'>";
		$output.="<tr>";
			$output.="<td style='width:70%'>";
				$output.="<div>Proveedor:<span class='underline'>".$remission['InventoryClient']['name']."</span></div>";
			$output.="</td>";
			$output.="<td style='width:30%'>";
				$output.="<div>Fecha:<span class='underline'>".$remissionDateTime->format('d-m-Y')."</span></div>";
			$output.="</td>";
		$output.="</tr>";
	$output.="</table>";
	if (!empty($remission['Remission']['observation'])){
		$output.="<div>Observación:".$remission['Remission']['observation']."</div>";
	}
	$output.="<br/>";
	
	if (!empty($remission['StockMovement'])){
		if ($remission['Currency']['id']==CURRENCY_USD){
			$currencyClass="USDcurrency";
		}
		else {
			$currencyClass="CScurrency";
		}
		$output.="<h3>".__('Related Stock Movements')."</h3>";
		$output.="<table class='bordered'>";
			$output.="<thead>";
				$output.="<tr>";
					$output.="<th>".__('Movement Date')."</th>";
					$output.="<th>".__('Inventory Product')."</th>";
					$output.="<th class='centered'>".__('Product Quantity')."</th>";
					$output.="<th class='centered'>".__('Product Unit Price')."</th>";
					$output.="<th class='centered'>".__('Subtotal')."</th>";
				$output.="</tr>";
			$output.="</thead>";
			$output.="<tbody>";
			foreach ($remission['StockMovement'] as $stockMovement){ 
				$movementDateTime=new DateTime($stockMovement['movement_date']);
				$output.="<tr>";
					$output.="<td>".$movementDateTime->format('d-m-Y')."</td>";
					$output.="<td>".$stockMovement['InventoryProduct']['name']."</td>";
					$output.="<td class='centered'>".$stockMovement['product_quantity']." ".$stockMovement['MeasuringUnit']['abbreviation']."</td>";
					$output.="<td class='".$currencyClass."'><span class='currency'>".$remission['Currency']['abbreviation']."</span><span class='amountright'>".number_format($stockMovement['product_unit_price'],2,".",",")."</span></td>";
					$output.="<td class='".$currencyClass."'><span class='currency'>".$remission['Currency']['abbreviation']."</span><span class='amountright'>".number_format($stockMovement['product_unit_price']*$stockMovement['product_quantity'],2,".",",")."</span></td>";
				$output.="</tr>";
			}
				$output.="<tr class='totalrow'>";
					$output.="<td>SubTotal</td>";
					$output.="<td></td>";
					$output.="<td></td>";
					$output.="<td></td>";
					$output.="<td class='".$currencyClass."'><span class='currency'>".$remission['Currency']['abbreviation']."</span><span class='amountright'>".$remission['Remission']['price_subtotal']."</span></td>";
				$output.="</tr>";	
			$output.="</tbody>";
		$output.="</table>";
	}
	
	echo mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');
?>

	