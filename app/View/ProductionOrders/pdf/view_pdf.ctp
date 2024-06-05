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
	$imageurl=$this->App->assetUrl($url,array(),true);
	
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
	$imageurl=$this->App->assetUrl($url,array(),true);
	//$output.="<div>".$imageurl."</div>";
	$output.="<div class='background'>";
		//$output.="<img src='".$url."'/>";
	$output.="</div>";
	
	$productionOrderDate=date("Y-m-d",strtotime($productionOrder['ProductionOrder']['production_order_date']));
	$productionOrderDateTime=new DateTime($productionOrderDate);
	$nowDate=date('Y-m-d');
	$nowDateTime=new DateTime($nowDate);
	
	$output.="<div class='rounded pagecentered'>";
		$output.="<div style='width:50%;clear:left;'>Fecha: <span class='underline'>".$productionOrderDateTime->format('d-m-Y')."</span></div>";
		$output.="<div style='width:50%;clear:right;display:inline-block;'>Orden de Producción # <span class='underline'>".$productionOrder['ProductionOrder']['production_order_code']."</span></div>";
		
		$output.="<div style='width:50%;display:block;clear:left;'>".__('SalesOrder').": <span>".$productionOrder['SalesOrder']['sales_order_code']."</span></div>";		
		if (!empty($productionOrder['PreviousProductionOrder']['id'])){
			$output.="<div style='width:50%;display:block;clear:left;'>".__('Previous Production Order').": <span>".$productionOrder['PreviousProductionOrder']['production_order_code']."</span></div>";
		}
		$output.="<div style='width:50%;'>".__('Bool Annulled').": <span>".($productionOrder['ProductionOrder']['bool_annulled']?__('Yes'):__('No'))."</span></div>";
		$output.="<div style='width:50%;display:block;clear:left;'>".__('Instructions').": <span>".($productionOrder['ProductionOrder']['instructions'])."</span></div>";
	$output.="</div>";
	
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	
	if (!empty($productionOrder['ProductionOrderProduct'])){
		$output.="<h3>".__('Productos en esta orden de producción')."</h3>";
		$output.="<table cellpadding = '0' cellspacing = '0'>";
			$output.="<tr>";
				$output.="<th>".__('Product')."</th>";
				$output.="<th>".__('Description')."</th>";
				$output.="<th>".__('Instructions')."</th>";
				$output.="<th>".__('Product Quantity')."</th>";
				$output.="<th>".__('Operation Location')."</th>";
				$output.="<th>".__('Department')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			$output.="</tr>";
		foreach ($productionOrder['ProductionOrderProduct'] as $productionOrderProduct){ 
			$output.="<tr>";
				$output.="<td>".$productionOrderProduct['Product']['name']."</td>";
				$output.="<td>".$productionOrderProduct['product_description']."</td>";
				$output.="<td>".$productionOrderProduct['product_instruction']."</td>";
				$output.="<td>".$productionOrderProduct['product_quantity']."</td>";
				$output.="<td>";
				foreach ($productionOrderProduct['ProductionOrderProductOperationLocation'] as $productOperationLocation){
					if (!empty($productOperationLocation['OperationLocation'])){
						//pr($productOperationLocation['OperationLocation']);
						$output.=$productOperationLocation['OperationLocation']['name'];
					}
				}
				$output.="</td>";
				$output.="<td>";
				foreach ($productionOrderProduct['ProductionOrderProductDepartment'] as $productDepartment){
					if (!empty($productDepartment['Department'])){
						$output.=($productDepartment['rank']+1).". ".$productDepartment['Department']['name']."<br/>";
					}
				}
				$output.="</td>";
			$output.="</tr>";
		}
		$output.="</table>";
	} 
	if (!empty($productionOrder['ProductionOrderRemark'])){
		$output.="<h3>".__('Remarcas para esta orden de producción')."</h3>";
		$output.="<table cellpadding = '0' cellspacing = '0'>";
			$output.="<tr>";
				$output.="<th>".__('Username')."</th>";
				$output.="<th>".__('Remark Datetime')."</th>";
				$output.="<th>".__('Remark Text')."</th>";
			$output.="</tr>";
		foreach ($productionOrder['ProductionOrderRemark'] as $productionOrderRemark){ 
			$productionOrderRemarkDateTime=new DateTime($productionOrderRemark['remark_datetime']);
			$output.="<tr>";
				$output.="<td>".$productionOrderRemark['User']['username']."</td>";
				$output.="<td>".$productionOrderRemarkDateTime->format('d-m-Y H:i')."</td>";
				$output.="<td>".$productionOrderRemark['remark_text']."</td>";
				
			$output.="</tr>";
		}
		$output.="</table>";
	}
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$currentDateTime=new DateTime();
	$output.="Pdf generado el ".$currentDateTime->format("d/m/Y H:i:s");
	
	/*
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
	*/
	echo mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');
?>