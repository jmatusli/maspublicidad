<style>
	div, span {
		font-size:0.9em;
	}
	.small {
		font-size:0.9em;
	}
	.big{
		font-size:1.5em;
	}
	
	table {
		font-size:0.8em;
	}
	
	pre {
		font-size:0.5em;
	}
	
	div.centered,
	td.centered,
	th.centered
	{
		text-align:center;
	}
	
	table.grid th, table.grid td{
		border:1px solid black;
	}
	
	div.right{
		text-align:right;
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
	.bordered tr td,
	.bordered tr.totalrow td,
	{
		border-width:1px;
		border-style:solid;
		border-color:#000000;
	}
	
	.bordered tr td{
		border-width:0 1px;
	}
	
	table {
		width:100%;
	}
	
	img.resize {
		width:200px; /* you can use % */
		height: auto;
	}
</style>
<?php
	$inventoryDate=date("Y-m-d",strtotime($inventoryDate));
	$inventoryDateTime=new DateTime($inventoryDate);
	$nowDate=date('Y-m-d');
	$nowDateTime=new DateTime($nowDate);
	
	$output="";
	
	$url="img/logo_pdf.jpg";
	$imageurl=$this->App->assetUrl($url,array(),true);
	//$output.="<div>".$imageurl."</div";
	$output.="<table>";
		$output.="<tr>";
			$output.="<td class='bold' style='width:30%;'><img src='".$imageurl."' class='resize'></img></td>";		
			$output.="<td class='centered big' style='width:40%;'>".strtoupper(COMPANY_NAME)."<br/>CONTROL DE INVENTARIO<br/>".$inventoryDateTime->format('d-m-Y')."</td>";
			$output.="<td class='bold' style='width:30%;'>MANAGUA, ".$nowDateTime->format('d-m-Y')."</td>";
		$output.="</tr>";
	$output.="</table>";
	
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	/*
	$output.="<table>";
		$output.="<tr>";
			$output.="<td class='bold' style='width:70%;'>CONTROL DE INVENTARIO DE ".$inventoryDateTime->format('d-m-Y')."</td>";		
			$output.="<td class='bold' style='width:30%;'>MANAGUA, ".$nowDateTime->format('d-m-Y')."</td>";
		$output.="</tr>";
	$output.="</table>";
	*/
	
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$output.="<div>";
	
	$output.="<h2>Productos</h2>";
	//pr($inventoryProducts);
	$inventoryProductTable="";
	if (!empty($inventoryProducts)){
		$inventoryProductTable.="<table id='inventario' cellpadding='0' cellspacing='0'>";
			$inventoryProductTable.="<thead>";
				$inventoryProductTable.="<tr>";
					$inventoryProductTable.="<th>Familia de Producto</th>";
					$inventoryProductTable.="<th>Producto</th>";
					if($userrole!=ROLE_FOREMAN) {
						$inventoryProductTable.="<th class='centered'>".__('Average Unit Price')."</th>";
					}
					$inventoryProductTable.="<th class='centered'>".__('Remaining')."</th>";
					if($userrole!=ROLE_FOREMAN) {
						$inventoryProductTable.="<th class='centered'>".__('Total Value')."</th>";
					}
				$inventoryProductTable.="</tr>";
			$inventoryProductTable.="</thead>";
			$inventoryProductTable.="<tbody>";

			$valueInventoryProducts=0;
			$quantityInventoryProducts=0; 
			$tableRows="";
			foreach ($inventoryProducts as $stockItem){
				//pr($stockItem);
				$remaining="";
				$average="";
				$totalvalue="";
				if ($stockItem['0']['Remaining']!=""){
					$remaining= number_format($stockItem['0']['Remaining'],0,".",","); 
					$average=$stockItem['0']['Remaining']>0?number_format($stockItem['0']['Saldo']/$stockItem['0']['Remaining'],4,".",","):0;
					$totalValue=$stockItem['0']['Saldo'];
					$valueInventoryProducts+=$stockItem['0']['Saldo'];
					$quantityInventoryProducts+=$stockItem['0']['Remaining'];
				}
				else {
					$remaining= "0";
					$average="0";
					$totalValue="0";
				}
				$tableRows.="<tr>";
					$tableRows.="<td>".$stockItem['InventoryProduct']['InventoryProductLine']['name']."</td>";
					$tableRows.="<td>".$stockItem['InventoryProduct']['name']."</td>";
					if($userrole!=ROLE_FOREMAN) {
						$tableRows.="<td class='centered currency'><span class='currency'></span><span class='amountright'>".$average."</span></td>";
					}
					$tableRows.="<td class='centered'>".$remaining."</td>";
					if($userrole!=ROLE_FOREMAN) {
						$tableRows.="<td class='centered currency'><span class='currency'></span><span class='amountright'>".$totalValue."</span></td>";
					}
				$tableRows.="</tr>";
			}
				$totalRow="";
				$totalRow.="<tr class='totalrow'>";
					$totalRow.="<td>Total</td>";
					$totalRow.="<td></td>";
					if($quantityInventoryProducts>0){
						$avg=$valueInventoryProducts/$quantityInventoryProducts;
					}
					else {
						$avg=0;
					}
					if($userrole!=ROLE_FOREMAN) {
						$totalRow.="<td class='centered currency'><span class='currency'></span><span class='amountright'>".number_format($avg,2,".",",")."</span></td>";
					}
					$totalRow.="<td class='centered number'>".$quantityInventoryProducts."</td>";
					if($userrole!=ROLE_FOREMAN) {
						$totalRow.="<td class='centered currency'><span class='currency'></span><span class='amountright'>".number_format($valueInventoryProducts,2,".",",")."</span></td>";
					}
				$totalRow.="</tr>";
				$inventoryProductTable.=$totalRow.$tableRows.$totalRow;
			$inventoryProductTable.="</tbody>";
		$inventoryProductTable.="</table>";
	}
	$output.=$inventoryProductTable;
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	/*
	$footer="";
	$footer.="<table style='width:100%'>";
		$footer.="<tr style='border:0px;'>";
			$footer.="<td align='center' class='underline' style='border:0px;width:33.3%'>Elaborado</td>";
			$footer.="<td align='center' class='underline' style='border:0px;width:33.3%'>Firma Empleado</td>";
			$footer.="<td align='center' class='underline' style='border:0px;width:33.3%'>Autorizado</td>";
		$footer.="</tr>";
	$footer.="</table>";
	$output.=$footer;
	*/
	$output.="</div>";
	
	echo mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');
?>