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
		width:200px; /* you can use % */
		height: auto;
	}
</style>
<?php
	$salesOrderDate=$salesOrder['SalesOrder']['sales_order_date'];
	$salesOrderDateTime=new DateTime($salesOrderDate);
	$nowDate=date('Y-m-d');
	$nowDateTime=new DateTime($nowDate);
	$url="img/logo.jpg";
	$imageurl=$this->App->assetUrl($url);
	
	$output="";
	$output.="<table>";
		$output.="<tr>";
			$output.="<td class='bold' style='width:30%;'><img src='".$imageurl."' class='resize'></img></td>";		
			$output.="<td class='centered big' style='width:40%;'>".strtoupper(COMPANY_NAME)."<br/>ORDEN DE VENTA<br/>".$salesOrder['SalesOrder']['sales_order_code'].($salesOrder['SalesOrder']['bool_annulled']?" (Anulada)":"")."</td>";
			$output.="<td class='bold' style='width:30%;'>MANAGUA, ".$salesOrderDateTime->format('d-m-Y')."</td>";
		$output.="</tr>";
	$output.="</table>";
	
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
	
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	$output.="<div><span class='bold '>&nbsp;</span></div>";
	
	if (!empty($salesOrder['SalesOrderProduct'])){
		$output.="<h3>".__('Productos de esta Orden de Venta')."</h3>";
		$output.="<table cellpadding = '0' cellspacing = '0'>";
			$output.="<tr>";
				$output.="<th>".__('Product Id')."</th>";
				$output.="<th>".__('Product Description')."</th>";
				$output.="<th>".__('Product Unit Price')."</th>";
				$output.="<th>".__('Product Quantity')."</th>";
				$output.="<th>".__('Product Total Price')."</th>";
				$output.="<th>".__('Status')."</th>";
				//echo"<th class='actions'>".__('Actions')."</th>";
			$output.="</tr>";
		foreach ($salesOrder['SalesOrderProduct'] as $salesOrderProduct){
			if ($salesOrderProduct['currency_id']==CURRENCY_CS){
				$classCurrency=" class='CScurrency'";
			}
			elseif ($salesOrderProduct['currency_id']==CURRENCY_USD){
				$classCurrency=" class='USDcurrency'";
			}
			$output.="<tr>";
				$output.="<td>".$this->Html->link($salesOrderProduct['Product']['name'],array('controller'=>'products','action'=>'view',$salesOrderProduct['Product']['id']),array('target'=>'_blank'))."</td>";
				$output.="<td>".$salesOrderProduct['product_description']."</td>";
				$output.="<td".$classCurrency."><span class='currency'>".$salesOrder['Currency']['abbreviation']."</span><span class='amount right'>".number_format($salesOrderProduct['product_unit_price'],2,".",",")."</span></td>";
				$output.="<td>".number_format($salesOrderProduct['product_quantity'],0,".",",")."</td>";
				$output.="<td".$classCurrency."><span class='currency'>".$salesOrder['Currency']['abbreviation']."</span><span class='amount right'>".number_format($salesOrderProduct['product_total_price'],2,".",",")."</span></td>";
				$output.="<td>".$salesOrderProduct['SalesOrderProductStatus']['status']."</td>";
				//$output.="<td class='actions'>";
				//	$output.=$this->Html->link(__('View'), array('controller' => 'sales_order_products', 'action' => 'view', $salesOrderProduct['id']));
				//	$output.=$this->Html->link(__('Edit'), array('controller' => 'sales_order_products', 'action' => 'edit', $salesOrderProduct['id']));
				//	$output.=$this->Form->postLink(__('Delete'), array('controller' => 'sales_order_products', 'action' => 'delete', $salesOrderProduct['id']), array(), __('Are you sure you want to delete # %s?', $salesOrderProduct['id']));
				//$output.="</td>";
			$output.="</tr>";
		}
		$output.="</table>";
	}
	
	echo mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');
?>
	