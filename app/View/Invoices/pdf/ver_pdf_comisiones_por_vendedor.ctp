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
	//pr($startDate);
	//pr($endDate);
	$startDatetime=new DateTime($startDate);
	$endDatetime=new DateTime($endDate);

	$nowDate=date('Y-m-d');
	$nowDateTime=new DateTime($nowDate);
	$url="img/logo.jpg";
	$imageurl=$this->App->assetUrl($url,array(),true);
	
	$output="";
	$output.="<table>";
		$output.="<tr>";
			$output.="<td class='bold' style='width:30%;'><img src='".$imageurl."' class='resize'></img></td>";		
			$output.="<td class='centered big' style='width:40%;'>".strtoupper(COMPANY_NAME)."</td>";
			$output.="<td class='bold' style='width:30%;'>MANAGUA, ".$nowDateTime->format('d-m-Y')."</td>";
		$output.="</tr>";
	$output.="</table>";
	
	//$output.="<h2>Comisiones por Vendedor para período de ".($startDateTime->format('d-m-Y'))." hasta ".($endDateTime->format('d-m-Y'))."</h2>";
	$output.="<h2>Comisiones por Vendedor para período de ".($startDate)." hasta ".($endDate)."</h2>";
	if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
		$output.="<h3>Resumen Totales</h3>";
		$overviewTable="";
		$overviewTable.="<table id='resumen_comisiones_vendedor' style='font-size:0.8em;'>";
			$overviewTable.="<thead>";
				$overviewTable.="<tr>";
					$overviewTable.="<th>Vendedor</th>";
					$overviewTable.="<th>Subtotal Efectivo</th>";
					$overviewTable.="<th>Comisión Efectivo</th>";
					$overviewTable.="<th>Subtotal Crédito</th>";
					$overviewTable.="<th>Comisión Crédito</th>";
					$overviewTable.="<th>Subtotal Recuperado</th>";
					$overviewTable.="<th>Comisión Recuperado</th>";
					$overviewTable.="<th>Subtotal Pendiente</th>";
					$overviewTable.="<th>Comisión Pendiente</th>";
				$overviewTable.="</tr>";
			$overviewTable.="</thead>";
			$overviewTable.="<tbody>";
			foreach ($selectedUsers as $user){
				$overviewTable.="<tr>";
					$overviewTable.="<td>".$user['User']['username']."</td>";
					switch ($currencyId){
						case CURRENCY_CS:
							$overviewTable.="<td class='CScurrency'><span class='currency'>C$</span><span class='amountright'>".number_format($user['cash_subtotal_CS'],2,".",",")."</span></td>";
							$overviewTable.="<td class='CScurrency'><span class='currency'>C$</span><span class='amountright'>".number_format($user['cash_commission_CS'],2,".",",")."</span></td>";
							$overviewTable.="<td class='CScurrency'><span class='currency'>C$</span><span class='amountright'>".number_format($user['credit_subtotal_CS'],2,".",",")."</span></td>";
							$overviewTable.="<td class='CScurrency'><span class='currency'>C$</span><span class='amountright'>".number_format($user['credit_commission_CS'],2,".",",")."</span></td>";
							$overviewTable.="<td class='CScurrency'><span class='currency'>C$</span><span class='amountright'>".number_format($user['recovered_subtotal_CS'],2,".",",")."</span></td>";
							$overviewTable.="<td class='CScurrency'><span class='currency'>C$</span><span class='amountright'>".number_format($user['recovered_commission_CS'],2,".",",")."</span></td>";
							$overviewTable.="<td class='CScurrency'><span class='currency'>C$</span><span class='amountright'>".number_format($user['pending_subtotal_CS'],2,".",",")."</span></td>";
							$overviewTable.="<td class='CScurrency'><span class='currency'>C$</span><span class='amountright'>".number_format($user['pending_commission_CS'],2,".",",")."</span></td>";
							break;
						case CURRENCY_USD:
							$overviewTable.="<td class='USDcurrency'><span class='currency'>US$</span><span class='amountright'>".number_format($user['cash_subtotal_USD'],2,".",",")."</span></td>";
							$overviewTable.="<td class='USDcurrency'><span class='currency'>US$</span><span class='amountright'>".number_format($user['cash_commission_USD'],2,".",",")."</span></td>";
							$overviewTable.="<td class='USDcurrency'><span class='currency'>US$</span><span class='amountright'>".number_format($user['credit_subtotal_USD'],2,".",",")."</span></td>";
							$overviewTable.="<td class='USDcurrency'><span class='currency'>US$</span><span class='amountright'>".number_format($user['credit_commission_USD'],2,".",",")."</span></td>";
							$overviewTable.="<td class='USDcurrency'><span class='currency'>US$</span><span class='amountright'>".number_format($user['recovered_subtotal_USD'],2,".",",")."</span></td>";
							$overviewTable.="<td class='USDcurrency'><span class='currency'>US$</span><span class='amountright'>".number_format($user['recovered_commission_USD'],2,".",",")."</span></td>";
							$overviewTable.="<td class='USDcurrency'><span class='currency'>US$</span><span class='amountright'>".number_format($user['pending_subtotal_USD'],2,".",",")."</span></td>";
							$overviewTable.="<td class='USDcurrency'><span class='currency'>US$</span><span class='amountright'>".number_format($user['pending_commission_USD'],2,".",",")."</span></td>";
							break;
					}
				$overviewTable.="</tr>";
			}
			$overviewTable.="</tbody>";
		$overviewTable.="</table>";
		$output.=$overviewTable;
	}
	
	$output.="<div>";
	if ($currencyId==CURRENCY_USD){
		$currencyClass="USDcurrency";
	}
	else {
		$currencyClass="CScurrency";
	}
	foreach ($selectedUsers as $selectedUser){
		
		$showUser=true;
		if (!empty($userId)){
			if ($selectedUser['User']['id']!=$userId){
				$showUser=false;
			}
		}
		if ($showUser){
			$selectedUserId=$selectedUser['User']['id'];
			$subTotalCashCS=0;
			$subTotalCashUSD=0;
			$subTotalCreditCS=0;
			$subTotalCreditUSD=0;
			$subTotalAllCS=0;
			$subTotalAllUSD=0;
			
			// FIRST THE CASH INVOICES
			if (!empty($selectedUser['cashInvoices'])){
				$pageHeader="";
				$pageHeader.="<thead>";
					$pageHeader.="<tr>";
						$pageHeader.="<th>".__('Invoice Date')."</th>";
						$pageHeader.="<th>".__('Invoice Code')."</th>";
						$pageHeader.="<th>".__('Orden de Venta')."</th>";
						$pageHeader.="<th>".__('Cliente')."</th>";
						$pageHeader.="<th class='centered'>".__('Subtotal')."</th>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$pageHeader.="<th class='centered'>Comisión %</th>";
							$pageHeader.="<th class='centered'>Comisión Total</th>";
						}
					$pageHeader.="</tr>";
				$pageHeader.="</thead>";

				$pageBody="";
				
				$subTotalCS=0;
				$commissionCS=0;
				$subTotalUSD=0;
				$commissionUSD=0;

				foreach ($selectedUser['cashInvoices'] as $invoice){ 
					$invoiceDateTime=new DateTime($invoice['Invoice']['invoice_date']);
					if ($invoice['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$invoice['Invoice']['price_subtotal'];
						$commissionCS+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						$subTotalUSD+=round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']/$invoice['Invoice']['exchange_rate'],2);
					}
					elseif ($invoice['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$invoice['Invoice']['price_subtotal'];
						$commissionUSD+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						$subTotalCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2);
						$commissionCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']*$invoice['Invoice']['exchange_rate'],2);
					}
					
					$pageRow="";
						$pageRow.="<td>";
							$pageRow.=$invoiceDateTime->format('d-m-Y');
						$pageRow.="</td>";
						$pageRow.="<td>".$invoice['Invoice']['invoice_code']."</td>";
						if (!empty($invoice['InvoiceSalesOrder'])){
							$pageRow.="<td>";
							foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
								$pageRow.=$invoiceSalesOrder['SalesOrder']['sales_order_code'];
								$pageRow.="<br/>";
							}
							$pageRow.="</td>";
						}
						else {
							$pageRow.="<td>-</td>";
						}
						$pageRow.="<td>".$invoice['Client']['name']."</td>";
						if ($currencyId==CURRENCY_USD){
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>US$</span><span class='amountright'>".number_format(h($invoice['Invoice']['price_subtotal']),2,".",",")."</span></td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>US$</span><span class='amountright'>".number_format($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2,".",",")."</span></td>";
							}
						}
						else {
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>C$</span><span class='amountright'>".number_format($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2,".",",")."</span></td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>C$</span><span class='amountright'>".number_format(h($invoice['Invoice']['price_subtotal']),2,".",",")."</span></td>";
							}
						}
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$pageRow.="<td class='percentage centered'><span class='amountright'>".number_format($invoice['Invoice']['percentage_commission']>0?$invoice['Invoice']['percentage_commission']:(100*$selectedUser['historical_performance']),2,".",",")."</span></td>";
							$pageRow.="<td class='CScurrency commissionamount'><span class='currency'>C$</span><span class='amountright'>".number_format($invoice['Invoice']['amount_commission'],2,".",",")."</span></td>";
						}
					$pageBody.="<tr>".$pageRow."</tr>";
				}

				$pageTotalRow="";
				if ($currencyId==CURRENCY_CS){
					$currencyClass="CScurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total C$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$currencyClass."'><span class='currency'>C$</span><span class='amountright'>".number_format($subTotalCS,2,".",",")."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'>C$</span><span class='amountright'>".number_format($commissionCS,2,".",",")."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				if ($currencyId==CURRENCY_USD){
					$currencyClass="USDcurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total US$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$currencyClass."'><span class='currency'>US$</span><span class='amountright'>".number_format($subTotalUSD,2,".",",")."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'>C$</span><span class='amountright'>".number_format($commissionCS,2,".",",")."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				
				$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
				
				$subTotalCashCS=$subTotalCS;
				$subTotalCashUSD=$subTotalUSD;
				
				$pageOutput="<table cellpadding='0' cellspacing='0' class='invoice period small'>".$pageHeader.$pageBody."</table>";
				$output.="<h3>Facturas de contado para vendedor ".$selectedUser['User']['username']."</h3>";
				$output.=$pageOutput;
			}
			else {
				$output.="<h3>No hay facturas de contado para vendedor ".$selectedUser['User']['username']."</h3>";
			}
			
			// SECOND THE CREDIT INVOICES
			if (!empty($selectedUser['creditInvoices'])){
				$pageHeader="";
				$pageHeader.="<thead>";
					$pageHeader.="<tr>";
						$pageHeader.="<th>".__('Invoice Date')."</th>";
						$pageHeader.="<th>".__('Invoice Code')."</th>";
						$pageHeader.="<th>".__('Orden de Venta')."</th>";
						$pageHeader.="<th>".__('Cliente')."</th>";
						$pageHeader.="<th class='centered'>".__('Subtotal')."</th>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$pageHeader.="<th class='centered'>Comisión %</th>";
							$pageHeader.="<th class='centered'>Comisión Total</th>";
						}
					$pageHeader.="</tr>";
				$pageHeader.="</thead>";
				
				$pageBody="";
				
				$subTotalCS=0;
				$commissionCS=0;
				$subTotalUSD=0;
				$commissionUSD=0;

				foreach ($selectedUser['creditInvoices'] as $invoice){ 
					
					$invoiceDateTime=new DateTime($invoice['Invoice']['invoice_date']);
					if ($invoice['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$invoice['Invoice']['price_subtotal'];
						$commissionCS+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						$subTotalUSD+=round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']/$invoice['Invoice']['exchange_rate'],2);
					}
					elseif ($invoice['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$invoice['Invoice']['price_subtotal'];
						$commissionUSD+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						$subTotalCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2);
						$commissionCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']*$invoice['Invoice']['exchange_rate'],2);
					}
					
					$pageRow="";
					
						$pageRow.="<td>";
							$pageRow.=$invoiceDateTime->format('d-m-Y');
						$pageRow.="</td>";
						$pageRow.="<td>".$invoice['Invoice']['invoice_code']."</td>";
						if (!empty($invoice['InvoiceSalesOrder'])){
							$pageRow.="<td>";
							foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
								$pageRow.=$invoiceSalesOrder['SalesOrder']['sales_order_code'];
								$pageRow.="<br/>";
							}
							$pageRow.="</td>";
						}
						else {
							$pageRow.="<td>-</td>";
						}
						$pageRow.="<td>".$invoice['Client']['name']."</td>";
						if ($currencyId==CURRENCY_USD){
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>US$</span><span class='amountright'>".number_format(h($invoice['Invoice']['price_subtotal']),2,".",",")."</span></td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>US$</span><span class='amountright'>".number_format($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2,".",",")."</span></td>";
							}
						}
						else {
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>C$</span><span class='amountright'>".number_format($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2,".",",")."</span></td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>C$</span><span class='amountright'>".number_format(h($invoice['Invoice']['price_subtotal']),2,".",",")."</span></td>";
							}
						}
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageRow.="<td class='percentage centered'><span class='amount'>".number_format($invoice['Invoice']['percentage_commission']>0?$invoice['Invoice']['percentage_commission']:(100*$selectedUser['historical_performance']),2,".",",")."</span></td>";
							$pageRow.="<td class='CScurrency commissionamount'><span class='currency'>C$</span><span class='amountright'>".number_format($invoice['Invoice']['amount_commission'],2,".",",")."</span></td>";
						}
						
					$pageBody.="<tr>".$pageRow."</tr>";
					
					
				}

				$pageTotalRow="";
				if ($currencyId==CURRENCY_CS){
					$currencyClass="CScurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total C$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$currencyClass."'><span class='currency'>C$</span><span class='amountright'>".number_format($subTotalCS,2,".",",")."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'>C$</span><span class='amountright'>".number_format($commissionCS,2,".",",")."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				if ($currencyId==CURRENCY_USD){
					$currencyClass="USDcurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total US$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$currencyClass."'><span class='currency'>US$</span><span class='amountright'>".number_format($subTotalUSD,2,".",",")."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'>C$</span><span class='amountright'>".number_format($commissionCS,2,".",",")."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				
				$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
				
				$subTotalCreditCS=$subTotalCS;
				$subTotalCreditUSD=$subTotalUSD;
				$pageOutput="<table cellpadding='0' cellspacing='0' class='invoice period small'>".$pageHeader.$pageBody."</table>";
				$output.="<h3>Facturas de crédito para vendedor ".$selectedUser['User']['username']."</h3>";
				$output.=$pageOutput;
				
			}
			else {
				$output.="<h3>No hay facturas de crédito para vendedor ".$selectedUser['User']['username']."</h3>";
			}
			
			
			// THEN SHOW THE OVERVIEW FIRST
			$subTotalCS=$subTotalCashCS + $subTotalCreditCS;
			$subTotalUSD=$subTotalCashUSD + $subTotalCreditUSD;
			if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
				$output.="<div class='container-fluid'>";
					$output.="<div class='row'>";
						$output.="<div class='col-md-4'>";
							$output.="<table class='small'>";
								$output.="<tbody>";
								if ($currencyId==CURRENCY_USD){
									$classCurrency="USDcurrency";
									$output.="<tr>";
										$output.="<td>Facturas de Contado</td>";
										$output.="<td><span class='currency'>US$</span><span class='amountright'>".number_format($subTotalCashUSD,2,",",".")."</span></td>";
									$output.="</tr>";
									$output.="<tr>";
										$output.="<td>Facturas de Crédito</td>";
										$output.="<td><span class='currency'>US$</span><span class='amountright'>".number_format($subTotalCreditUSD,2,",",".")."</span></td>";
									$output.="</tr>";
									$output.="<tr>";
										$output.="<td>Total Ventas</td>";
										$output.="<td><span class='currency'>US$</span><span class='amountright'>".number_format($subTotalUSD,2,",",".")."</span></td>";
									$output.="</tr>";
								}
								else {
									$classCurrency="CScurrency";
									$output.="<tr>";
										$output.="<td>Facturas de Contado</td>";
										$output.="<td class='".$classCurrency."'><span class='currency'>C$</span><span class='amountright'>".number_format($subTotalCashCS,2,",",".")."</span></td>";
									$output.="</tr>";
									$output.="<tr>";
										$output.="<td>Facturas de Crédito</td>";
										$output.="<td class='".$classCurrency."'><span class='currency'>C$</span><span class='amountright'>".number_format($subTotalCreditCS,2,",",".")."</span></td>";
									$output.="</tr>";
									$output.="<tr>";
										$output.="<td>Total Ventas</td>";
										$output.="<td class='".$classCurrency."'><span class='currency'>C$</span><span class='amountright'>".number_format($subTotalCS,2,",",".")."</span></td>";
									$output.="</tr>";
								}
								$output.="</tbody>";
							$output.="</table>";
						$output.="</div>";	
						$output.="<div class='col-md-4'>";
							$output.="<table class='small'>";
								$output.="<tbody>";
								if ($currencyId==CURRENCY_USD){
									$classCurrency="USDcurrency";
									$output.="<tr>";
										$output.="<td>Venta Mínima</td>";
										if (!empty($selectedUser['SalesObjective'])){
											$output.="<td class='".$classCurrency."'><span class='currency'>US$</span><span class='amountright'>".number_format($selectedUser['SalesObjective']['minimum_objective']/$exchangeRateStartDate['ExchangeRate']['rate'],2,".",",")."</span></td>";
										}
										else {
											$output.="<td class='".$classCurrency."'><span class='currency'>US$</span><span class='amountright'>".number_format(1000000000/$exchangeRateStartDate['ExchangeRate']['rate'],2,".",",")."</span></td>";
										}
									$output.="</tr>";
									$output.="<tr>";
										$output.="<td>Meta</td>";
										if (!empty($selectedUser['SalesObjective'])){
											$output.="<td class='".$classCurrency."'><span class='currency'>US$</span><span class='amountright'>".number_format($selectedUser['SalesObjective']['maximum_objective']/$exchangeRateStartDate['ExchangeRate']['rate'],2,".",",")."</span></td>";
										}
										else {
											$output.="<td class='".$classCurrency."'><span class='currency'>US$</span><span class='amountright'>".number_format(1000000000/$exchangeRateStartDate['ExchangeRate']['rate'],2,".",",")."</span></td>";
										}
									$output.="</tr>";
									$output.="<tr>";
										$output.="<td>Cumplimiento</td>";
										//if ($subTotalUSD>$selectedUser['SalesObjective']['minimum_objective']/$exchangeRateStartDate['ExchangeRate']['rate']){
										if (!empty($selectedUser['SalesObjective'])){
											$output.="<td class='percentage centered'><span class='amount'>".number_format((100*$subTotalUSD*$exchangeRateStartDate['ExchangeRate']['rate']/$selectedUser['SalesObjective']['maximum_objective']),2,".",",")."</span></td>";
										}
										else {
											$output.="<td class='percentage centered'><span class='amount'>0</span></td>";
										}
									
										//}
										//else {
										//	$output.="<td class='percentage centered'><span class='amount'>0</span></td>";
										//}
									$output.="</tr>";
								}
								else {
									$classCurrency="CScurrency";
									$output.="<tr>";
										$output.="<td>Venta Mínima</td>";
										if (!empty($selectedUser['SalesObjective'])){
											$output.="<td class='".$classCurrency."'><span class='currency'>C$</span><span class='amountright'>".number_format($selectedUser['SalesObjective']['minimum_objective'],2,".",",")."</span></td>";
										}
										else {
											$output.="<td class='".$classCurrency."'><span class='currency'>C$</span><span class='amountright'>1000000000</span></td>";
										}
									$output.="</tr>";
									$output.="<tr>";
										$output.="<td>Meta</td>";
										if (!empty($selectedUser['SalesObjective'])){
											$output.="<td class='".$classCurrency."'><span class='currency'>C$</span><span class='amountright'>".number_format($selectedUser['SalesObjective']['maximum_objective'],2,".",",")."</span></td>";
										}
										else {
											$output.="<td class='".$classCurrency."'><span class='currency'>C$</span><span class='amountright'>1000000000</span></td>";
										}
									$output.="</tr>";
									$output.="<tr>";
										$output.="<td>Cumplimiento</td>";
										//if ($subTotalCS>$selectedUser['SalesObjective']['minimum_objective']){
										if (!empty($selectedUser['SalesObjective'])){
											$output.="<td class='percentage centered'><span class='amount'>".round((100*$subTotalCS/$selectedUser['SalesObjective']['maximum_objective']),2,".",",")."</span></td>";
										}
										else {
											$output.="<td class='percentage centered'><span class='amount'>0</span></td>";
										}
										//}
										//else {
										//	$output.="<td class='percentage centered'><span class='amount'>0</span></td>";
										//}
									$output.="</tr>";
								}
								$output.="</tbody>";
							$output.="</table>";
						$output.="</div>";	
						$output.="<div class='col-md-4'>";
							if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
								$output.="<table style='width:80%' class='small'>";
									$output.="<tr>";
										$output.="<td>Performancia Histórica</td>";
										$output.="<td><span class='amountright'>".number_format(100*$selectedUser['historical_performance'],2,".",",")."</span></td>";
									$output.="</tr>";
									$output.="<tr>";
										$output.="<td>Comisión a aplicar</td>";
										$defaultCommissionValue=0;
										if (!empty($selectedUser['SalesObjective'])){
											if ($subTotalCS>$selectedUser['SalesObjective']['minimum_objective']){
												$defaultCommissionValue=round((5*$subTotalCS/$selectedUser['SalesObjective']['maximum_objective']),2);
											}					
										}
										$output.="<td><span class='amountright'>".number_format($defaultCommissionValue,2,".",",")."</span></td>";
									$output.="</tr>";
									$output.="<tr>";
										$output.="<td><button id='apply_commission' type='button'>Aplicar comisión a facturas seleccionadas</button></td>";
									$output.="</tr>";
								$output.="</table>";
							}
						$output.="</div>";	
					$output.="</div>";	
				$output.="</div>";	
			}
			
			//THIRD SHOW THE RECOVERED INVOICES
			if (!empty($selectedUser['recoveredInvoices'])){
				$pageHeader="";
				$pageHeader.="<thead>";
					$pageHeader.="<tr>";
						
						$pageHeader.="<th>".__('Invoice Date')."</th>";
						$pageHeader.="<th>".__('Invoice Code')."</th>";
						$pageHeader.="<th>".__('Orden de Venta')."</th>";
						$pageHeader.="<th>".__('Cliente')."</th>";
						$pageHeader.="<th class='centered'>".__('Subtotal')."</th>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$pageHeader.="<th class='centered'>Comisión %</th>";
							$pageHeader.="<th class='centered'>Comisión Total</th>";
						}
					$pageHeader.="</tr>";
				$pageHeader.="</thead>";
				$pageBody="";
				
				$subTotalCS=0;
				$commissionCS=0;
				$subTotalUSD=0;
				$commissionUSD=0;

				foreach ($selectedUser['recoveredInvoices'] as $invoice){ 
					$invoiceDateTime=new DateTime($invoice['Invoice']['invoice_date']);
					if ($invoice['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$invoice['Invoice']['price_subtotal'];
						$commissionCS+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						$subTotalUSD+=round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']/$invoice['Invoice']['exchange_rate'],2);
					}
					elseif ($invoice['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$invoice['Invoice']['price_subtotal'];
						$commissionUSD+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						$subTotalCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2);
						$commissionCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']*$invoice['Invoice']['exchange_rate'],2);
					}
					
					$pageRow="";
						$pageRow.="<td>";
							$pageRow.=$invoiceDateTime->format('d-m-Y');
						$pageRow.="</td>";
						$pageRow.="<td>".$invoice['Invoice']['invoice_code']."</td>";
						if (!empty($invoice['InvoiceSalesOrder'])){
							$pageRow.="<td>";
							foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
								$pageRow.=$invoiceSalesOrder['SalesOrder']['sales_order_code'];
								$pageRow.="<br/>";
							}
							$pageRow.="</td>";
						}
						else {
							$pageRow.="<td>-</td>";
						}
						$pageRow.="<td>".$invoice['Client']['name']."</td>";
						if ($currencyId==CURRENCY_USD){
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>US$</span><span class='amountright'>".number_format(h($invoice['Invoice']['price_subtotal']),2,".",",")."</span></td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>US$</span><span class='amountright'>".number_format($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2,".",",")."</span></td>";
							}
						}
						else {
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>C$</span><span class='amountright'>".number_format($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2,".",",")."</span></td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>C$</span><span class='amountright'>".number_format(h($invoice['Invoice']['price_subtotal']),2,".",",")."</span></td>";
							}
						}
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$pageRow.="<td class='percentage centered'><span class='amount'>".$invoice['Invoice']['percentage_commission']." %</span></td>";
							$pageRow.="<td class='CScurrency commissionamount'><span class='currency'>C$</span><span class='amountright'>".number_format($invoice['Invoice']['amount_commission'],2,".",",")."</span></td>";
						}
					$pageBody.="<tr>".$pageRow."</tr>";
				}

				$pageTotalRow="";
				if ($currencyId==CURRENCY_CS){
					$classCurrency="CScurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total C$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$classCurrency."'><span class='currency'>C$</span><span class='amountright'>".number_format($subTotalCS,2,".",",")."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'>C$</span><span class='amountright'>".number_format($commissionCS,2,".",",")."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				if ($currencyId==CURRENCY_USD){
					$classCurrency="USDcurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total US$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$classCurrency."'><span class='currency'>US$</span><span class='amountright'>".number_format($subTotalUSD,2,".",",")."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'>C$</span><span class='amountright'>".number_format($commissionCS,2,".",",")."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
				$pageOutput="<table cellpadding='0' cellspacing='0' class='invoice previous small'>".$pageHeader.$pageBody."</table>";
				$output.="<h3>Facturas recuperadas para vendedor ".$selectedUser['User']['username']."</h3>";
				$output.=$pageOutput;
				
			}
			else {
				$output.="<h3>No hay facturas recuperadas  vendedor ".$selectedUser['User']['username']."</h3>";
			}
			
			//FOURTH SHOW THE PENDING INVOICES
			if (!empty($selectedUser['pendingInvoices'])){
				$pageHeader="";
				$pageHeader.="<thead>";
					$pageHeader.="<tr>";
						
						$pageHeader.="<th>".__('Invoice Date')."</th>";
						$pageHeader.="<th>".__('Invoice Code')."</th>";
						$pageHeader.="<th>".__('Orden de Venta')."</th>";
						$pageHeader.="<th>".__('Cliente')."</th>";
						$pageHeader.="<th class='centered'>".__('Subtotal')."</th>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$pageHeader.="<th class='centered'>Comisión %</th>";
							$pageHeader.="<th class='centered'>Comisión Total</th>";
						}
					$pageHeader.="</tr>";
				$pageHeader.="</thead>";
				

				$pageBody="";
				
				$subTotalCS=0;
				$commissionCS=0;
				$subTotalUSD=0;
				$commissionUSD=0;

				foreach ($selectedUser['pendingInvoices'] as $invoice){ 
					$invoiceDateTime=new DateTime($invoice['Invoice']['invoice_date']);
					if ($invoice['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$invoice['Invoice']['price_subtotal'];
						$commissionCS+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						$subTotalUSD+=round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']/$invoice['Invoice']['exchange_rate'],2);
					}
					elseif ($invoice['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$invoice['Invoice']['price_subtotal'];
						$commissionUSD+=$invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission'];
						$subTotalCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2);
						$commissionCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['percentage_commission']*$invoice['Invoice']['exchange_rate'],2);
					}
					$pageRow="";
						$pageRow.="<td>";
							$pageRow.=$invoiceDateTime->format('d-m-Y');
						$pageRow.="</td>";
						$pageRow.="<td>".$invoice['Invoice']['invoice_code']."</td>";
						if(!empty($invoice['InvoiceSalesOrder'])){
							$pageRow.="<td>";
							foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
								$pageRow.=$invoiceSalesOrder['SalesOrder']['sales_order_code'];
							}
							$pageRow.="</td>";
						}
						else {
							$pageRow.="<td>-</td>";
						}
						
						$pageRow.="<td>".$invoice['Client']['name']."</td>";
						if ($currencyId==CURRENCY_USD){
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>US$</span><span class='amountright'>".number_format(h($invoice['Invoice']['price_subtotal']),2,".",",")."</span></td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>US$</span><span class='amountright'>".number_format($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2,".",",")."</span></td>";
							}
						}
						else {
							if ($invoice['Currency']['id']==CURRENCY_USD){
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>C$</span><span class='amountright'>".number_format($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2,".",",")."</span></td>";
							}
							else {
								$pageRow.="<td class='subtotal ".$currencyClass."'><span class='currency'>C$</span><span class='amountright'>".number_format(h($invoice['Invoice']['price_subtotal']),2,".",",")."</span></td>";
							}
						}
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
							$pageRow.="<td class='percentage centered'><span class='amount'>".$invoice['Invoice']['percentage_commission']." %</span></td>";
							$pageRow.="<td class='CScurrency commissionamount'><span class='currency'>C$</span><span class='amountright'>".number_format($invoice['Invoice']['amount_commission'],2,".",",")."</span></td>";
						}
					$pageBody.="<tr>".$pageRow."</tr>";
				}

				$pageTotalRow="";
				if ($currencyId==CURRENCY_CS){
					$classCurrency="CScurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total C$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$classCurrency."'><span class='currency'>C$</span><span class='amountright'>".number_format($subTotalCS,2,".",",")."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'>C$</span><span class='amountright'>".number_format($commissionCS,2,".",",")."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				if ($currencyId==CURRENCY_USD){
					$classCurrency="USDcurrency";
					$pageTotalRow.="<tr class='totalrow'>";
						$pageTotalRow.="<td>Total US$</td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td></td>";
						$pageTotalRow.="<td class='".$classCurrency."'><span class='currency'>US$</span><span class='amountright'>".number_format($subTotalUSD,2,".",",")."</span></td>";
						if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){						
							$pageTotalRow.="<td></td>";
							$pageTotalRow.="<td class='CScurrency commissionamount'><span class='currency'>C$</span><span class='amountright'>".number_format($commissionCS,2,".",",")."</span></td>";
						}
					$pageTotalRow.="</tr>";
				}
				$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
				
				$pageOutput="<table cellpadding='0' cellspacing='0' class='invoice previous small'>".$pageHeader.$pageBody."</table>";
				$output.="<h3>Facturas pendientes de recuperar para vendedor ".$selectedUser['User']['username']."</h3>";
				$output.=$pageOutput;
				
			}
			else {
				$output.="<h3>No hay facturas pendientes de recuperar para vendedor ".$selectedUser['User']['username']."</h3>";
			}
		}
	}
	$currentDateTime=new DateTime();
	$output.="Pdf generado el ".$currentDateTime->format("d/m/Y H:i:s");
	$output.="</div>";
	echo mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');

