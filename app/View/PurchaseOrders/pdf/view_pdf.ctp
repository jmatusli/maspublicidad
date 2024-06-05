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
	
	/*
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
	
	*/
	
	$purchaseOrderDate=date("Y-m-d",strtotime($purchaseOrder['PurchaseOrder']['purchase_order_date']));
	$purchaseOrderDateTime=new DateTime($purchaseOrderDate);
	$nowDate=date('Y-m-d');
	$nowDateTime=new DateTime($nowDate);
	
	$output.="<div class='rounded pagecentered'>";
		$output.="<div style='width:50%;clear:left;'>Fecha: <span class='underline'>".$purchaseOrderDateTime->format('d-m-Y')."</span></div>";
		$output.="<div style='width:50%;clear:right;display:inline-block;'>Orden de Compra # <span class='underline'>".$purchaseOrder['PurchaseOrder']['purchase_order_code']."</span></div>";
		
		$output.="<div style='width:50%;display:block;clear:left;'>".__('Provider').": <span>".$purchaseOrder['Provider']['name']."</span></div>";
		$output.="<div style='width:50%;display:block;clear:left;'>".__('User').": <span>".$purchaseOrder['User']['username']."</span></div>";
		$output.="<div style='width:50%;'>".__('Bool Annulled').": <span>".($purchaseOrder['PurchaseOrder']['bool_annulled']?__('Yes'):__('No'))."</span></div>";
		$output.="<div style='width:50%;display:block;clear:left;'>".__('Bool Iva').": <span>".($purchaseOrder['PurchaseOrder']['bool_iva']?__('Yes'):__('No'))."</span></div>";
		$output.="<div style='width:50%;display:block;clear:left;'>".__('Cost Subtotal').": <span>".$purchaseOrder['Currency']['abbreviation']." ".number_format($purchaseOrder['PurchaseOrder']['cost_subtotal'],2,".",",")."</span></div>";
		$output.="<div style='width:50%;'>".__('Cost IVA').": <span>".$purchaseOrder['Currency']['abbreviation']." ".number_format($purchaseOrder['PurchaseOrder']['cost_iva'],2,".",",")."</span></div>";
		$output.="<div style='width:50%;display:block;clear:left;'>".__('Cost Total').": <span>".$purchaseOrder['Currency']['abbreviation']." ".number_format($purchaseOrder['PurchaseOrder']['cost_total'],2,".",",")."</span></div>";
		$output.="<div style='width:50%;display:block;clear:left;'>".__('Cost Other Total').": <span>".$purchaseOrder['Currency']['abbreviation']." ".number_format($purchaseOrder['PurchaseOrder']['cost_other_total'],2,".",",")."</span></div>";
		$output.="<div style='width:50%;display:block;clear:left;'>".__('Payment Mode').": <span>".$purchaseOrder['PaymentMode']['name']."</span></div>";
		$output.="<div style='width:50%;'>".__('Payment Document').": <span>".$purchaseOrder['PurchaseOrder']['payment_document']."</span></div>";
		$output.="<div style='width:50%;display:block;clear:left;'>".__('Bool Received').": <span>".($purchaseOrder['PurchaseOrder']['bool_received']?__('Yes'):__('No'))."</span></div>";
		/*	
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
		*/
	$output.="</div>";
	
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	/*
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
	*/
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	
	if (!empty($purchaseOrder['PurchaseOrderProduct'])){
		$output.="<h3>".__('Productos en esta Orden de Compra')."</h3>";
		$output.="<table cellpadding = '0' cellspacing = '0'>";
			$output.="<thead>";
				$output.="<tr>";
					$output.="<th>".__('Product Id')."</th>";
					$output.="<th>".__('Product Description')."</th>";
					$output.="<th class='centered'>".__('Product Quantity')."</th>";
					$output.="<th>".__('Product Unit Cost')."</th>";
					$output.="<th>".__('Product Total Cost')."</th>";
					//$output.="<th class='actions'>".__('Actions')."</th>";
				$output.="</tr>";
			$output.="</thead>";
			$output.="<tbody>";
			$totalProductQuantity=0;	
			foreach ($purchaseOrder['PurchaseOrderProduct'] as $purchaseOrderProduct){ 
				$totalProductQuantity+=$purchaseOrderProduct['product_quantity'];
				if ($purchaseOrderProduct['currency_id']==CURRENCY_CS){
					$classCurrency=" class='CScurrency'";
				}
				elseif ($purchaseOrderProduct['currency_id']==CURRENCY_USD){
					$classCurrency=" class='USDcurrency'";
				}
				$output.="<tr>";
					$output.="<td>".$purchaseOrderProduct['Product']['name'].(empty($purchaseOrderProduct['Product']['code'])?"":" (".$purchaseOrderProduct['Product']['code'].")")."</td>";
					$output.="<td>".$purchaseOrderProduct['product_description']."</td>";
					$output.="<td class='centered'>".number_format($purchaseOrderProduct['product_quantity'],0,".",",")."</td>";
					$output.="<td><span class='currency'>".$purchaseOrder['Currency']['abbreviation']."</span><span class='right'>".number_format($purchaseOrderProduct['product_unit_cost'],2,".",",")."</span></td>";
					$output.="<td><span class='currency'>".$purchaseOrder['Currency']['abbreviation']."</span><span class='right'>".number_format($purchaseOrderProduct['product_total_cost'],2,".",",")."</span></td>";
					//$output.="<td class='actions'>";
						//$output.=$this->Html->link(__('View'), array('controller' => 'purchase_order_products', 'action' => 'view', $purchaseOrderProduct['id']));
						//$output.=$this->Html->link(__('Edit'), array('controller' => 'purchase_order_products', 'action' => 'edit', $purchaseOrderProduct['id']));
						//$output.=$this->Form->postLink(__('Delete'), array('controller' => 'purchase_order_products', 'action' => 'delete', $purchaseOrderProduct['id']), array(), __('Are you sure you want to delete # %s?', $purchaseOrderProduct['id']));
					//$output.="</td>";
				$output.="</tr>";
			}
				$output.="<tr class='totalrow'>";
					$output.="<td>Subtotal</td>";
					$output.="<td></td>";
					$output.="<td class='centered'>".number_format($totalProductQuantity,0,".",",")."</td>";
					$output.="<td></td>";
					$output.="<td><span class='currency'>".$purchaseOrder['Currency']['abbreviation']."</span><span class='amountright'>".number_format($purchaseOrder['PurchaseOrder']['cost_subtotal'],2,".",",")."</span></td>";
				$output.="</tr>";
			$output.="</tbody>";
		$output.="</table>";
	}

	if (!empty($purchaseOrder['PurchaseOrderOtherCost'])){
		$output.="<h3>".__('Otros Costos en esta Orden de Compra')."</h3>";
		$output.="<table cellpadding = '0' cellspacing = '0'>";
			$output.="<thead>";
				$output.="<tr>";
					$output.="<th>".__('Department')."</th>";
					$output.="<th>".__('Task Description')."</th>";
					$output.="<th class='centered'>".__('Task Quantity')."</th>";
					$output.="<th>".__('Task Unit Cost')."</th>";
					$output.="<th>".__('Task Total Cost')."</th>";
					echo"<th class='actions'>".__('Actions')."</th>";
				$output.="</tr>";
			$output.="</thead>";
			$output.="<tbody>";
			$totalOtherQuantity=0;
			foreach ($purchaseOrder['PurchaseOrderOtherCost'] as $purchaseOrderOtherCost){ 
				$totalOtherQuantity+=$purchaseOrderOtherCost['task_quantity'];
				$output.="<tr>";
					$output.="<td>".$purchaseOrderOtherCost['Department']['name']."</td>";
					$output.="<td>".$purchaseOrderOtherCost['task_description']."</td>";
					$output.="<td class='centered'>".number_format($purchaseOrderOtherCost['task_quantity'],0,".",",")."</td>";
					$output.="<td><span class='currency'>".$purchaseOrder['Currency']['abbreviation']."</span><span class='right'>".number_format($purchaseOrderOtherCost['task_unit_cost'],2,".",",")."</td>";
					$output.="<td><span class='currency'>".$purchaseOrder['Currency']['abbreviation']."</span><span class='right'>".number_format($purchaseOrderOtherCost['task_total_cost'],2,".",",")."</td>";
					//$output.="<td class='actions'>";
					//	$output.=$this->Html->link(__('View'), array('controller' => 'purchase_order_other_costs', 'action' => 'view', $purchaseOrderOtherCost['id']));
					//	$output.=$this->Html->link(__('Edit'), array('controller' => 'purchase_order_other_costs', 'action' => 'edit', $purchaseOrderOtherCost['id']));
					//	$output.=$this->Form->postLink(__('Delete'), array('controller' => 'purchase_order_other_costs', 'action' => 'delete', $purchaseOrderOtherCost['id']), array(), __('Are you sure you want to delete # %s?', $purchaseOrderOtherCost['id']));
					//$output.="</td>";
				$output.="</tr>";
			}
				$output.="<tr class='totalrow'>";
					$output.="<td>Subtotal</td>";
					$output.="<td></td>";
					$output.="<td class='centered'>".number_format($totalOtherQuantity,0,".",",")."</td>";
					$output.="<td></td>";
					$output.="<td><span class='currency'>".$purchaseOrder['Currency']['abbreviation']."</span><span class='amountright'>".number_format($purchaseOrder['PurchaseOrder']['cost_other_total'],2,".",",")."</span></td>";
				$output.="</tr>";
			$output.="</tbody>";
		$output.="</table>";
	}

	if (!empty($purchaseOrder['PurchaseOrderRemark'])){
		$output.="<h3>".__('Related Purchase Order Remarks')."</h3>";
		$output.="<table cellpadding = '0' cellspacing = '0'>";
			$output.="<tr>";
				
				
				$output.="<th>".__('Remark Datetime')."</th>";
				$output.="<th>".__('User')."</th>";
				$output.="<th>".__('Remark Text')."</th>";
				$output.="<th>".__('Action Type')."</th>";
				//$output "<th class='actions'>".__('Actions')."</th>";
			$output.="</tr>";
		foreach ($purchaseOrder['PurchaseOrderRemark'] as $purchaseOrderRemark){ 
			$remarkDatetime=new DateTime($purchaseOrderRemark['remark_datetime']);
			$output.="<tr>";
				$output.="<td>".$remarkDatetime->format('d-m-Y H:i:s')."</td>";
				$output.="<td>".$purchaseOrderRemark['User']['first_name']." ".$purchaseOrderRemark['User']['last_name']."</td>";
				$output.="<td>".$purchaseOrderRemark['remark_text']."</td>";
				$output.="<td>".$purchaseOrderRemark['ActionType']['name']."</td>";
				//$output.="<td class='actions'>";
				//	$output.=$this->Html->link(__('View'), array('controller' => 'purchase_order_remarks', 'action' => 'view', $purchaseOrderRemark['id']));
				//	$output.=$this->Html->link(__('Edit'), array('controller' => 'purchase_order_remarks', 'action' => 'edit', $purchaseOrderRemark['id']));
				//	$output.=$this->Form->postLink(__('Delete'), array('controller' => 'purchase_order_remarks', 'action' => 'delete', $purchaseOrderRemark['id']), array(), __('Are you sure you want to delete # %s?', $purchaseOrderRemark['id']));
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