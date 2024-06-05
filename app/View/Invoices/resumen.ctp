<script>
	function formatNumbers(){
		$("td.number span.amountright").each(function(){
			if (Math.abs(parseFloat($(this).text()))<0.001){
				$(this).text("0");
			}
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,2,'.',',');
		});
	}
	
	function formatCSCurrencies(){
		$("td.CScurrency").each(function(){
			
			if (parseFloat($(this).find('.amountright').text())<0){
				$(this).find('.amountright').prepend("-");
			}
			$(this).find('.amountright').number(true,2);
			$(this).find('.currency').text("C$");
		});
	}
	
	function formatUSDCurrencies(){
		$("td.USDcurrency").each(function(){
			if (parseFloat($(this).find('.amountright').text())<0){
				$(this).find('.amountright').prepend("-");
			}
			$(this).find('.amountright').number(true,2);
			$(this).find('.currency').text("US$");
		});
	}
	
	$(document).ready(function(){
		formatNumbers();
		formatCSCurrencies();
		formatUSDCurrencies();
	});
</script>
<div class="invoices index fullwidth">
<?php 
	$excelOutput="";
	echo "<div class='container-fluid'>";
		echo "<div class='rows'>";
			echo "<div class='col-md-5'>";
				echo "<h2>".__('Invoices')."</h2>";
				echo $this->Form->create('Report');
					echo "<fieldset>";
						echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015,'maxYear'=>date('Y')));
						echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015,'maxYear'=>date('Y')));
						echo "<br/>";
						
						if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) { 
              echo $this->Form->input('Report.user_id',['label'=>'Mostrar Usuario','options'=>$users,'default'=>$userId,'empty'=>['0'=>'Todos Usuarios']]);
            }
            else {
              echo $this->Form->input('Report.user_id',['label'=>'Mostrar Usuario','options'=>$users,'default'=>$userId,'type'=>'hidden']);
            }
						echo $this->Form->input('Report.currency_id',array('label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId));
					echo "</fieldset>";
					echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
					echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
				echo "<br/>";
				echo $this->Form->end(__('Refresh'));
				echo $this->Html->link('Guardar como Excel', ['action' => 'guardarResumen'], ['class' => 'btn btn-primary']);
			echo "</div>";
			echo "<div class='col-md-5'>";
				if ($userrole==ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) { 
          $overviewTableHeader='';
					$overviewTableHeader.="<thead>";
            $overviewTableHeader.="<tr>";
              $overviewTableHeader.="<th>Vendedor</th>";
              $overviewTableHeader.="<th>Subtotal todas Facturas Período</th>";
            $overviewTableHeader.="</tr>";
          $overviewTableHeader.="</thead>";
          
          $grandTotal=0;
          $overviewTableBodyRows='';
          foreach ($invoiceTotalsPerUser as $invoiceTotalUser){
            if ($invoiceTotalUser['invoice_total']>0){
              $overviewTableBodyRows.="<tr>";
                $overviewTableBodyRows.="<td>".$this->Html->link($invoiceTotalUser['User']['first_name']." ".$invoiceTotalUser['User']['last_name'],['controller'=>'users','action'=>'view',$invoiceTotalUser['User']['id']])."</td>";
                $overviewTableBodyRows.="<td class='number'><span class='amountright'>".$invoiceTotalUser['invoice_total']."</span></td>";
              $overviewTableBodyRows.="</tr>";
              $grandTotal+=$invoiceTotalUser['invoice_total'];
            }
          }
          $overviewTableTotalRow="<tr class='totalrow'>";
            $overviewTableTotalRow.="<td>Total</td>";
            $overviewTableTotalRow.="<td class='number'><span class='amountright'>".$grandTotal."</span></td>";
          $overviewTableTotalRow.="</tr>";
          $grandTotal+=$invoiceTotalUser['invoice_total'];						
          $overviewTableBody='<tbody>'.$overviewTableBodyRows.$overviewTableTotalRow.'</tbody>';
					
          $overviewTable='<table id="resumen_ejecutivo">'.$overviewTableHeader.$overviewTableBody.'</table>';
					echo '<h3>Resumen Ejecutivo Facturas en '.$currencies[$currencyId].'</h3>';
					echo $overviewTable;
					
          $overviewExcelHeader='';
          $overviewExcelHeader.="<thead>";
            $overviewExcelHeader.="<tr>";
              $overviewExcelHeader.="<th>Vendedor</th>";
              $overviewExcelHeader.="<th>Moneda</th>";
              $overviewExcelHeader.="<th>Subtotal todas Facturas Período</th>";
            $overviewExcelHeader.="</tr>";
          $overviewExcelHeader.="</thead>";
          
          $overviewExcelBodyRows='';
          foreach ($invoiceTotalsPerUser as $invoiceTotalUser){
            if ($invoiceTotalUser['invoice_total']>0){
              $overviewExcelBodyRows.="<tr>";
                $overviewExcelBodyRows.="<td>".$invoiceTotalUser['User']['first_name']." ".$invoiceTotalUser['User']['last_name']."</td>";
                $overviewExcelBodyRows.='<td>'.$currencies[$currencyId].'</td>';
                $overviewExcelBodyRows.="<td>".$invoiceTotalUser['invoice_total']."</span></td>";
              $overviewExcelBodyRows.="</tr>";
            }
          }
          $overviewExcelTotalRow="<tr class='totalrow'>";
            $overviewExcelTotalRow.="<td>Total</td>";
            $overviewExcelTotalRow.='<td>'.$currencies[$currencyId].'</td>';
            $overviewExcelTotalRow.="<td class='number'><span class='amountright'>".$grandTotal."</span></td>";
          $overviewExcelTotalRow.="</tr>";
          $grandTotal+=$invoiceTotalUser['invoice_total'];						
          $overviewExcelBody='<tbody>'.$overviewExcelBodyRows.$overviewExcelTotalRow.'</tbody>';
					
          $excelOverviewTable='<table id="resumen_ejecutivo">'.$overviewExcelHeader.$overviewExcelBody.'</table>';
          
          $excelOutput.=$excelOverviewTable;
				}
			echo "</div>";
			echo "<div class='col-md-2'>";
				echo "<h3>".__('Actions')."</h3>";
				echo '<ul style="list-style:none;">';
					if ($bool_add_permission){
						echo "<li>".$this->Html->link(__('New Invoice'), ['action' => 'crear'])."</li>";
					}
					echo "<br/>";
					if ($bool_salesorder_index_permission){
						echo "<li>".$this->Html->link(__('List Sales Orders'), ['controller' => 'salesOrders','action' => 'index'])."</li>";
					}
					if ($bool_salesorder_add_permission){
						echo "<li>".$this->Html->link(__('New Sales Order'), ['controller' => 'salesOrders', 'action' => 'add'])."</li>";
					}
					if ($bool_quotation_index_permission){
						echo "<li>".$this->Html->link(__('List Quotations'), ['controller' => 'quotations', 'action' => 'index'])."</li>";
					}
					if ($bool_quotation_add_permission){
						echo "<li>".$this->Html->link(__('New Quotation'), ['controller' => 'quotations', 'action' => 'add'])."</li>";
					}
					if ($bool_client_index_permission){
						echo "<li>".$this->Html->link(__('List Clients'), ['controller' => 'clients', 'action' => 'index'])."</li>";
					}
					if ($bool_client_add_permission){
						echo "<li>".$this->Html->link(__('New Client'), ['controller' => 'clients', 'action' => 'add'])."</li>";
					}
				echo "</ul>";
			echo "</div>";
		echo "</div>";
	echo "</div>";		
	
	$startDateTime=new DateTime($startDate);
	$endDateTime=new DateTime($endDate);
?> 
</div>
<div class='actions'>
<?php 
	
	
?>
</div>
<div style='clear:left;'>
<?php
	$pageHeader="";
	$excelHeader="";
	$pageHeader.="<thead>";
		$pageHeader.="<tr>";
			$pageHeader.="<th>".$this->Paginator->sort('invoice_date')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('invoice_code')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('quotation_id')."</th>";
			$pageHeader.="<th>".$this->Paginator->sort('client_id')."</th>";
			$pageHeader.="<th class='centered'>".$this->Paginator->sort('price_subtotal')."</th>";
			//$pageHeader.="<th class='centered'>".$this->Paginator->sort('price_iva')."</th>";
			//$pageHeader.="<th class='centered'>".$this->Paginator->sort('price_total')."</th>";
			if ($userId==0){
				$pageHeader.="<th>".$this->Paginator->sort('user_id','Vendedor')."</th>";
			}
			
			
			//$pageHeader.="<th class='actions'>".__('Actions')."</th>";
		$pageHeader.="</tr>";
	$pageHeader.="</thead>";
	$excelHeader.="<thead>";
		if ($userId==0){
			$excelHeader.="<tr><th colspan='7' align='center'>".COMPANY_NAME."</th></tr>";	
			$excelHeader.="<tr><th colspan='7' align='center'>".__('Resumen de Facturas')." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</th></tr>";
		}
		else {
			$excelHeader.="<tr><th colspan='5' align='center'>".COMPANY_NAME."</th></tr>";	
			$excelHeader.="<tr><th colspan='5' align='center'>".__('Resumen de Facturas')." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</th></tr>";
		}
			$excelHeader.="<tr>";
			$excelHeader.="<th>Fecha</th>";
			$excelHeader.="<th>Código</th>";
			$excelHeader.="<th>Cotización</th>";
			$excelHeader.="<th>Cliente</th>";
      $excelHeader.="<th>Moneda</th>";
			$excelHeader.="<th>Precio Subtotal</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('price_iva')."</th>";
			//$excelHeader.="<th>".$this->Paginator->sort('price_total')."</th>";
		if ($userId==0){
			$excelHeader.="<th>".$this->Paginator->sort('user_id','Vendedor')."</th>";
		}
		$excelHeader.="</tr>";
	$excelHeader.="</thead>";

	$pageBody="";
	$excelBody="";
	
	$subtotalCS=0;
	//$ivaCS=0;
	//$totalCS=0;
	$subtotalUSD=0;
	//$ivaUSD=0;
	//$totalUSD=0;

	foreach ($invoices as $invoice){ 
		$invoiceDate=new DateTime($invoice['Invoice']['invoice_date']);
		
		if ($invoice['Currency']['id'] == CURRENCY_CS){
			$currencyClass=" class='CScurrency'";
			$subtotalCS+=$invoice['Invoice']['price_subtotal'];
			//$ivaCS+=$invoice['Invoice']['price_iva'];
			//$totalCS+=$invoice['Invoice']['price_total'];
			
			//added calculation of totals in US$
			$subtotalUSD+=round($invoice['Invoice']['price_subtotal']/$invoice['Invoice']['exchange_rate'],2);
			//$ivaUSD+=round($invoice['Invoice']['price_iva']/$invoice['Invoice']['exchange_rate'],2);
			//$totalUSD+=round($invoice['Invoice']['price_total']/$invoice['Invoice']['exchange_rate'],2);
		}
		elseif ($invoice['Currency']['id'] == CURRENCY_USD){
			$currencyClass=" class='USDcurrency'";
			$subtotalUSD+=$invoice['Invoice']['price_subtotal'];
			//$ivaUSD+=$invoice['Invoice']['price_iva'];
			//$totalUSD+=$invoice['Invoice']['price_total'];
			
			//added calculation of totals in CS$
			$subtotalCS+=round($invoice['Invoice']['price_subtotal']*$invoice['Invoice']['exchange_rate'],2);
			//$ivaCS+=round($invoice['Invoice']['price_iva']*$invoice['Invoice']['exchange_rate'],2);
			//$totalCS+=round($invoice['Invoice']['price_total']*$invoice['Invoice']['exchange_rate'],2);
		}
		
		$pageRow="";
			$pageRow.="<td>".$invoiceDate->format('d-m-Y')."</td>";
			if ($invoice['Invoice']['bool_credit']){
				$pageRow.="<td>".$this->Html->link($invoice['Invoice']['invoice_code'].($invoice['Invoice']['bool_annulled']?" (Anulada)":""),['action'=>'detalle',$invoice['Invoice']['id']])."</td>";
			}
			else {
				$pageRow.="<td style='background-color:#88FF88;'>".$this->Html->link($invoice['Invoice']['invoice_code'].($invoice['Invoice']['bool_annulled']?" (Anulada)":""),['action'=>'detalle',$invoice['Invoice']['id']])."</td>";
			}
			$pageRow.="<td>";
			if (!empty($invoice['InvoiceSalesOrder'])){
				foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
					//pr($invoiceSalesOrder);
					if (!empty($invoiceSalesOrder['SalesOrder'])){
						$pageRow.=$this->Html->link($invoiceSalesOrder['SalesOrder']['Quotation']['quotation_code'], ['controller' => 'quotations', 'action' => 'view', $invoiceSalesOrder['SalesOrder']['Quotation']['id']])."<br/>";
					}
					else {
						$pageRow.="-";
					}
				}
			}
			else {
				$pageRow.="-";
			}
			$pageRow.="</td>";
			$pageRow.="<td>".($invoice['Client']['bool_generic']?($invoice['Invoice']['client_name']." (".$invoice['Client']['name'].")"):$this->Html->link($invoice['Client']['name'], ['controller' => 'clients', 'action' => 'view', $invoice['Client']['id']]))."</td>";
      
      $excelRow=$pageRow;
			$excelRow.="<td>".h($invoice['Currency']['abbreviation'])."</td>";
      $excelRow.="<td>".h($invoice['Invoice']['price_subtotal'])."</td>";
			
			if ($userId == 0){
				$excelRow.="<td>";
				foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
					$excelRow.=empty($invoiceSalesOrder['User'])?"-":($invoiceSalesOrder['User']['first_name'].' '.$invoiceSalesOrder['User']['last_name'])."<br/>";
				}
				$excelRow.="</td>";
			}
      
      if ($invoice['Invoice']['bool_annulled']){
				$excelBody.="<tr class='italic'>".$excelRow."</tr>";
			}
			else {
				$excelBody.="<tr>".$excelRow."</tr>";
			}
      
      $pageRow.="<td".$currencyClass."><span class='currency'></span><span class='amountright'>".h($invoice['Invoice']['price_subtotal'])."</span></td>";
			
			if ($userId == 0){
				$pageRow.="<td>";
				foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
					$pageRow.=empty($invoiceSalesOrder['User'])?"-":($this->Html->link($invoiceSalesOrder['User']['first_name'].' '.$invoiceSalesOrder['User']['last_name'], ['controller' => 'users', 'action' => 'view', $invoiceSalesOrder['User']['id']])."<br/>");
				}
				$pageRow.="</td>";
			}
			
			

			//$pageRow.="<td class='actions'>";
			//	$filename="Factura_".$invoice['Invoice']['invoice_code'];
			//	//$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $invoice['Invoice']['id']));
			//	if ($bool_edit_permission){
			//		$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $invoice['Invoice']['id']));
			//		//$pageRow.=->postLink(__('Delete'), array('action' => 'delete', $invoice['Invoice']['id']), array(), __('Está seguro que quiere eliminar la factura #%s?', $invoice['Invoice']['invoice_code']));
			//	}
			//	$pageRow.=$this->Form->postLink(__('Anular'), array('action' => 'annul', $invoice['Invoice']['id']), array(), __('Estás seguro que quiere anular Factura # %s?', $invoice['Invoice']['invoice_code']));
			//	$pageRow.=$this->Html->link(__('Pdf'), array('action' => 'viewPdf','ext'=>'pdf', $invoice['Invoice']['id'],$filename),array('target'=>'_blank'));
			//$pageRow.="</td>";

		if ($invoice['Invoice']['bool_annulled']){
			$pageBody.="<tr class='italic'>".$pageRow."</tr>";
		}
		else {
			$pageBody.="<tr>".$pageRow."</tr>";
		}
	}

	$pageTotalRow="";
	if ($currencyId == CURRENCY_CS){
		$pageTotalRow.="<tr class='totalrow'>";
			$pageTotalRow.="<td>Total C$</td>";
			if ($userId == 0){
				$pageTotalRow.="<td></td>";
			}
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td class='CScurrency'><span class='currency'></span><span class='amountright'>".$subtotalCS."</span></td>";
			$pageTotalRow.="<td></td>";
		$pageTotalRow.="</tr>";
	}
	if ($currencyId == CURRENCY_USD){
		$pageTotalRow.="<tr class='totalrow'>";
			$pageTotalRow.="<td>Total US$</td>";
			if ($userId == 0){
				$pageTotalRow.="<td></td>";
			}
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td class='USDcurrency'><span class='currency'></span><span class='amountright'>".$subtotalUSD."</td>";
			$pageTotalRow.="<td></td>";
		$pageTotalRow.="</tr>";
	}
	$excelTotalRow='';
	
	$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
	$excelBody="<tbody>".$excelTotalRow.$excelBody."</tbody>";
	$table_id="facturas";
	$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
	echo "<p class='comment'>Facturas de Contado aparecen en verde, Facturas de Crédito aparecen con fondo normal</p>";
	echo $pageOutput;
	$excelOutput.="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	$_SESSION['resumen'] = $excelOutput;
?>
</div>