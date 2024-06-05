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
	
	$productionProcessDate=date("Y-m-d",strtotime($productionProcess['ProductionProcess']['production_process_date']));
	$productionProcessDateTime=new DateTime($productionProcessDate);
	$nowDate=date('Y-m-d');
	$nowDateTime=new DateTime($nowDate);
	
	$output.="<div class='rounded pagecentered'>";
		$output.="<div style='width:50%;clear:left;'>Fecha: <span class='underline'>".$productionProcessDateTime->format('d-m-Y')."</span></div>";
		$output.="<div style='width:50%;clear:right;display:inline-block;'>Proceso de Producción # <span class='underline'>".$productionProcess['ProductionProcess']['production_process_code']."</span></div>";
		$output.="<div style='width:50%;'>".__('Bool Annulled').": <span>".($productionProcess['ProductionProcess']['bool_annulled']?__('Yes'):__('No'))."</span></div>";
	$output.="</div>";
	
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	
	if (!empty($productionProcess['ProductionProcessProduct'])){
		$output.="<h3>".__('Productos en el el Proceso de Producción')."</h3>";
		$output.="<table cellpadding = '0' cellspacing = '0'>";
			$output.="<tr>";
				$output.="<th>".__('Product')."</th>";
				$output.="<th>".__('Product Description')."</th>";
				$output.="<th>".__('Product Quantity')."</th>";
				$output.="<th>".__('Operator')."</th>";
				$output.="<th>".__('Machine')."</th>";
				$output.="<th>".__('Operation Location')."</th>";
			$output.="</tr>";
		foreach ($productionProcess['ProductionProcessProduct'] as $productionProcessProduct){ 
			$output.="<tr>";
				$output.="<td>".$productionProcessProduct['Product']['name']."</td>";
				$output.="<td>".$productionProcessProduct['product_description']."</td>";
				$output.="<td>".$productionProcessProduct['product_quantity']."</td>";
				$output.="<td>".$productionProcessProduct['Operator']['first_name']." ".$productionProcessProduct['Operator']['last_name']."</td>";
				$output.="<td>".$productionProcessProduct['Machine']['name']."</td>";
				$output.="<td>";
				foreach ($productionProcessProduct['ProductionProcessProductOperationLocation'] as $productOperationLocation){
					if (!empty($productOperationLocation['OperationLocation'])){
						//pr($productOperationLocation['OperationLocation']);
						$output.=$productOperationLocation['OperationLocation']['name']."<br/>";
					}
				}
				$output.="</td>";
				
			$output.="</tr>";
		}
		$output.="</table>";
	} 
	if (!empty($productionProcess['ProductionProcessRemark'])){
		$output.="<h3>".__('Remarcas para este Proceso de Producción')."</h3>";
		$output.="<table cellpadding = '0' cellspacing = '0'>";
			$output.="<tr>";
				$output.="<th>".__('Vendedor')."</th>";
				$output.="<th>".__('Remark Datetime')."</th>";
				$output.="<th>".__('Remark Text')."</th>";
				$output.="<th>".__('Action Type')."</th>";
				//echo"<th class='actions'>".__('Actions')."</th>";
			$output.="</tr>";
		foreach ($productionProcess['ProductionProcessRemark'] as $productionProcessRemark){ 
			$output.="<tr>";
				$remarkDateTime=new Datetime($productionProcessRemark['remark_datetime']);
				$output.="<td>".$productionProcessRemark['User']['first_name']."_".$productionProcessRemark['User']['last_name']."</td>";
				$output.="<td>".$remarkDateTime->format('d-m-Y')."</td>";
				$output.="<td>".$productionProcessRemark['remark_text']."</td>";
				$output.="<td>".$productionProcessRemark['ActionType']['name']."</td>";
				//$output.="<td class='actions'>";
				//	$output.=$this->Html->link(__('View'), array('controller' => 'production_process_remarks', 'action' => 'view', $productionProcessRemark['id']));
				//	$output.=$this->Html->link(__('Edit'), array('controller' => 'production_process_remarks', 'action' => 'edit', $productionProcessRemark['id']));
				//$output.="</td>";
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