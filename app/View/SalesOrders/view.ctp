<script>
	function formatNumbers(){
		$("td.number span.amountright").each(function(){
			if (Math.abs(parseFloat($(this).text()))<0.001){
				$(this).text("0");
			}
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,0,'.',',');
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
		
		//var pdfUrl="<?php echo $this->Html->url(array('action'=>'viewPdf','ext'=>'pdf',$salesOrder['SalesOrder']['id'],$filename),true); ?>";
		//window.open(pdfUrl,'_blank');
	});
</script>

<div class="salesOrders view fullwidth">
<?php 
	echo "<h1>";
		echo __('Sales Order')." ".$salesOrder['SalesOrder']['sales_order_code'];
		if ($salesOrder['SalesOrder']['bool_annulled']){
			echo " (Anulada)";
		}
		else {
			if ($salesOrder['SalesOrder']['bool_completely_delivered']){
				echo " (Entregada)";
			}
			elseif ($salesOrder['SalesOrder']['bool_authorized']){
				echo " (Autorizada)";
			}
		}
		
	echo "</h1>";
  echo '<div class="container-fluid">';
    echo '<div class="rows">';
      echo '<div class="col-sm-6">';
        echo '<h2>Orden de venta</h2>';
        echo "<dl>";
          $salesOrderDateTime=new DateTime($salesOrder['SalesOrder']['sales_order_date']);
          
          echo "<dt>".__('Sales Order Date')."</dt>";
          echo "<dd>".$salesOrderDateTime->format('d-m-Y')."</dd>";
          echo "<dt>".__('Sales Order Code')."</dt>";
          echo "<dd>".h($salesOrder['SalesOrder']['sales_order_code'])."</dd>";
          
          echo "<dt>".__('Ejecutivo de Venta')."</dt>";
          echo "<dd>".$this->Html->link($salesOrder['Quotation']['User']['username'], array('controller' => 'users', 'action' => 'view', $salesOrder['Quotation']['User']['id']),array('target'=>'_blank'))."</dd>";
          echo "<dt>".__('Client')."</dt>";
          echo "<dd>".$this->Html->link($salesOrder['Quotation']['Client']['name'], array('controller' => 'clients', 'action' => 'view', $salesOrder['Quotation']['Client']['id']),array('target'=>'_blank'))."</dd>";
          echo "<dt>".__('Contact')."</dt>";
          if (!empty($salesOrder['Quotation']['Contact'])){
            echo "<dd>".$this->Html->link($salesOrder['Quotation']['Contact']['fullname'], array('controller' => 'contacts', 'action' => 'view', $salesOrder['Quotation']['Contact']['id']),array('target'=>'_blank'))."</dd>";
          }
          else {
            echo "<dd>-</dd>";
          }
          echo "<dt>".__('Bool Annulled')."</dt>";
          echo "<dd>".h($salesOrder['SalesOrder']['bool_annulled']?__("Yes"):__("No"))."</dd>";
          echo "<dt>".__('Bool Completely Delivered')."</dt>";
          echo "<dd>".h($salesOrder['SalesOrder']['bool_completely_delivered']?__("Yes"):__("No"))."</dd>";
          echo "<dt>".__('Quotation')."</dt>";
          echo "<dd>".$this->Html->link($salesOrder['Quotation']['quotation_code'], array('controller' => 'quotations', 'action' => 'view', $salesOrder['Quotation']['id']))."</dd>";
          echo "<dt>".__('Production Order')."</dt>";
          echo '<dd>';
          if (empty($salesOrder['ProductionOrder'])){            
            echo '-';
          }
          else {
            if ($bool_production_order_resumen_permission){
              echo $this->Html->link($salesOrder['ProductionOrder'][0]['production_order_code'], ['controller'=>'productionOrders','action' => 'detalle',$salesOrder['ProductionOrder'][0]['id']]);
            }
            else {
               echo $salesOrder['ProductionOrder'][0]['production_order_code'];
            }
          }
          echo '</dd>';
          echo '<dt>'.__('IVA?').'</dt>';
          echo "<dd>".h($salesOrder['SalesOrder']['bool_iva']?__("Yes"):__("No"))."</dd>";
          echo "<dt>".__('Price Subtotal')."</dt>";
          echo "<dd>".$salesOrder['Currency']['abbreviation']." ".number_format($salesOrder['SalesOrder']['price_subtotal'],2,".",",")."</dd>";
          echo "<dt>".__('Price IVA')."</dt>";
          echo "<dd>".$salesOrder['Currency']['abbreviation']." ".number_format($salesOrder['SalesOrder']['price_iva'],2,".",",")."</dd>";
          echo "<dt>".__('Price Total')."</dt>";
          echo "<dd>".$salesOrder['Currency']['abbreviation']." ".number_format($salesOrder['SalesOrder']['price_total'],2,".",",")."</dd>";
          echo "<dt>".__('Authorizada?')."</dt>";
          echo "<dd>".h($salesOrder['SalesOrder']['bool_authorized']?__("Yes"):__("No"))."</dd>";
          if ($salesOrder['SalesOrder']['bool_authorized']){
            echo "<dt>".__('Persona quien autoriza')."</dt>";
            echo "<dd>".$salesOrder['AuthorizingUser']['first_name']." ".$salesOrder['AuthorizingUser']['last_name']."</dd>";
          }
          echo "<dt>".__('Observation')."</dt>";
          if (!empty($salesOrder['SalesOrder']['observation'])){
            echo "<dd>".$salesOrder['SalesOrder']['observation']."</dd>";
          }
          else {
            echo "<dd>-</dd>";
          }
          
        echo '</dl>';
      echo '</div>';
      echo '<div class="col-sm-4">';
        echo '<h2>Remarcas</h2>';
        if (!empty($salesOrder['SalesOrderRemark'])){
          echo '<table>';
            echo "<thead>";
              echo "<tr>";
                echo "<th>Fecha</th>";
                echo "<th>Vendedor</th>";
                echo "<th>Remarca</th>";
              echo "</tr>";
            echo "</thead>";
            echo "<tbody>";							
            $boolFirstOne=true;
            foreach ($salesOrder['SalesOrderRemark'] as $salesOrderRemark){
              //pr($salesOrderRemark);
              $remarkDateTime=new DateTime($salesOrderRemark['remark_datetime']);
              echo "<tr>";
                echo "<td>".$remarkDateTime->format('d-m-Y H:i')."</td>";
                echo "<td>".$salesOrderRemark['User']['first_name']." ".$salesOrderRemark['User']['last_name']."</td>";
                echo "<td>".$salesOrderRemark['remark_text']."</td>";
              echo "</tr>";
            }
            echo "</tbody>";
          echo "</table>";  
        }  
      echo '</div>';
      echo '<div class="col-sm-2">';
        echo '<h2>Acciones</h2>';
        echo '<ul style="list-style:none;">';
          echo "<li>".$this->Html->link(__('Guardar como pdf'), ['action' => 'viewPdf','ext'=>'pdf', $salesOrder['SalesOrder']['id'],$filename],['target'=>'_blank'])."</li>";
        echo '</ul>';
        echo '<br/>';
        if ($bool_edit_permission){
          if (!empty($salesOrder['ProductionOrder'])){
            echo '<h3>No se puede editar la orden de venta porque ya hay una orden de producción asociada</h3>';
          }
          elseif (!empty($salesOrder['InvoiceSalesOrder'])){
            echo '<h3>No se puede editar la orden de venta porque ya hay facturas asociadas</h3>';
          }
          elseif ($userRoleId != ROLE_ADMIN && !$bool_autorizar_permission && $salesOrder['SalesOrder']['bool_authorized']){
            echo '<h3>No se puede editar la orden de venta porque ya ha estado autorizada.</h3>';
          }
          else {
            echo '<ul style="list-style:none;">';
              echo "<li>".$this->Html->link(__('Edit Sales Order'), ['action' => 'edit', $salesOrder['SalesOrder']['id']])."</li>";
            echo '</ul>';
          }
          echo '<br/>';
        }
        
        echo '<ul style="list-style:none;">';
          echo "<li>".$this->Html->link(__('List Sales Orders'), array('action' => 'index'))."</li>";
          echo '<li>'.$this->Html->link(__('New Sales Order'), ['action' => 'add']).'</li>';
          echo '<br/>' ;
          if (empty($salesOrder['ProductionOrder']) && $bool_production_order_crear_permission){
            echo '<li>'.$this->Html->link('Crear Orden de Producción', ['controller'=>'productionOrders','action' => 'crear',$salesOrder['SalesOrder']['id']],['class'=>'btn btn-primary']).'</li>';
            echo '<br/>' ;
          }
          if ($bool_quotation_index_permission){
            echo "<li>".$this->Html->link(__('List Quotations'), array('controller' => 'quotations', 'action' => 'index'))."</li>";
          }
          if ($bool_quotation_index_permission){
            echo "<li>".$this->Html->link(__('New Quotation'), array('controller' => 'quotations', 'action' => 'add'))."</li>";
          }
        echo "</ul>";
      echo '</div>';  
    echo '</div>';
  echo '</div>';
?>
</div>
<div class='related'>
<?php
	if (!empty($salesOrder['SalesOrderProduct'])){
		echo "<h3>".__('Productos de esta Orden de Venta')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Product Id')."</th>";
					echo "<th>".__('Product Description')."</th>";
					echo "<th class='centered'>".__('Product Quantity')."</th>";
					echo "<th class='centered'>".__('Product Unit Price')."</th>";
					echo "<th class='centered'>".__('Product Total Price')."</th>";
					echo "<th>".__('IVA?')."</th>";
					echo "<th>".__('Producción ausente?')."</th>";
					echo "<th>".__('Status')."</th>";
					//echo"<th class='actions'>".__('Actions')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			$totalProductQuantity=0;
			foreach ($salesOrder['SalesOrderProduct'] as $salesOrderProduct){
				//pr($salesOrderProduct['SalesOrderProductDepartment']);
				$totalProductQuantity+=$salesOrderProduct['product_quantity'];
				if ($salesOrderProduct['currency_id']==CURRENCY_CS){
					$classCurrency=" class='CScurrency'";
				}
				elseif ($salesOrderProduct['currency_id']==CURRENCY_USD){
					$classCurrency=" class='USDcurrency'";
				}
				echo "<tr>";
					echo "<td>".$this->Html->link($salesOrderProduct['Product']['name'],array('controller'=>'products','action'=>'view',$salesOrderProduct['Product']['id']),array('target'=>'_blank'))."</td>";
					echo "<td>".str_replace("\n","<br/>",$salesOrderProduct['product_description'])."</td>";
					echo "<td class='centered'>".$salesOrderProduct['product_quantity']."</td>";
					echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$salesOrderProduct['product_unit_price']."</span></td>";
					echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$salesOrderProduct['product_total_price']."</span></td>";
					echo "<td class='centered'>".($salesOrderProduct['bool_iva']?__('Yes'):__('No'))."</td>";
					echo "<td class='centered'>".($salesOrderProduct['bool_no_production']?__('Yes'):__('No'))."</td>";
					echo "<td>".$salesOrderProduct['SalesOrderProductStatus']['status']."</td>";
				echo "</tr>";
			}
				echo "<tr class='totalrow'>";
					echo "<td>Subtotal</td>";
					echo "<td></td>";
					echo "<td class='centered'>".$totalProductQuantity."</td>";
					echo "<td></td>";
					echo "<td".$classCurrency."><span class='currency'>".$salesOrder['Currency']['abbreviation']."</span><span class='amountright'>".$salesOrder['SalesOrder']['price_subtotal']."</span></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
				echo "</tr>";
				echo "<tr class='totalrow'>";
					echo "<td>IVA</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td".$classCurrency."><span class='currency'>".$salesOrder['Currency']['abbreviation']."</span><span class='amountright'>".$salesOrder['SalesOrder']['price_iva']."</span></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
				echo "</tr>";
				echo "<tr class='totalrow'>";
					echo "<td>Total</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td".$classCurrency."><span class='currency'>".$salesOrder['Currency']['abbreviation']."</span><span class='amountright'>".$salesOrder['SalesOrder']['price_total']."</span></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
				echo "</tr>";
			echo "</tbody>";
		echo "</table>";
	}
?>
</div>
<div class="related">
<?php 
	if (!empty($salesOrder['InvoiceSalesOrder'])){
		echo "<h3>".__('Facturas para esta Orden de Venta')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Invoice Date')."</th>";
					echo "<th>".__('Invoice Code')."</th>";
					echo "<th>".__('IVA')."</th>";
					echo "<th>".__('Price Subtotal')."</th>";
					echo "<th>".__('Price Iva')."</th>";
					echo "<th>".__('Price Total')."</th>";
					//echo "<th>".__('Bool Annulled')."</th>";
					//echo "<th>".__('Client Id')."</th>";
					//echo "<th>".__('User Id')."</th>";
					echo"<th class='actions'>".__('Actions')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
				$totalSubtotalCS=0;
				$totalIvaCS=0;
				$totalTotalCS=0;
				
				$totalSubtotalUSD=0;
				$totalIvaUSD=0;
				$totalTotalUSD=0;
				foreach ($salesOrder['InvoiceSalesOrder'] as $invoiceSalesOrder){
					$invoice=$invoiceSalesOrder['Invoice'];
					$invoiceDateTime=new DateTime($invoice['invoice_date']);
					
					if ($invoice['currency_id']==CURRENCY_CS){
						$classCurrency=" class='CScurrency'";
						$totalSubtotalCS+=$invoice['price_subtotal'];
						$totalIvaCS+=$invoice['price_iva'];
						$totalTotalCS+=$invoice['price_total'];
					}
					elseif ($invoice['currency_id']==CURRENCY_USD){
						$classCurrency=" class='USDcurrency'";
						$totalSubtotalUSD+=$invoice['price_subtotal'];
						$totalIvaUSD+=$invoice['price_iva'];
						$totalTotalUSD+=$invoice['price_total'];
					}
					if ($invoice['bool_annulled']){
						echo "<tr class='italic'>";
					}
					else {
						echo "<tr>";
					}
						echo "<td>".$invoiceDateTime->format('d-m-Y')."</td>";
						echo "<td>".$this->Html->link($invoice['invoice_code'].($invoice['bool_annulled']?' (Anulada)':''),['controller'=>'invoices','action'=>'detalle',$invoice['id']])."</td>";
						echo "<td>".($invoice['bool_iva']?__('Yes'):__('No'))."</td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$invoice['price_subtotal']."</td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$invoice['price_iva']."</td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".$invoice['price_total']."</td>";
						//echo "<td>".$invoice['bool_annulled']."</td>";
						//echo "<td>".$invoice['client_id']."</td>";
						//echo "<td>".$invoice['user_id']."</td>";
						
						
						echo "<td class='actions'>";
							echo $this->Html->link(__('View'), ['controller' => 'invoices', 'action' => 'detalle', $invoice['id']]);
							if ($bool_invoice_edit_permission){
								echo $this->Html->link(__('Edit'), ['controller' => 'invoices', 'action' => 'editar', $invoice['id']]);
							}
						echo "</td>";
					echo "</tr>";
				}
				if ($totalSubtotalCS>0){
					echo "<tr class='totalrow'>";
						echo "<td>Subtotal C$</td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".number_format($totalSubtotalCS,2,".",",")."</span></td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".number_format($totalIvaCS,2,".",",")."</span></td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".number_format($totalTotalCS,2,".",",")."</span></td>";
						echo "<td></td>";
					echo "</tr>";
				}
				if ($totalSubtotalUSD>0){
					echo "<tr class='totalrow'>";
						echo "<td>Totales US$</td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".number_format($totalSubtotalUSD,2,".",",")."</span></td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".number_format($totalIvaUSD,2,".",",")."</span></td>";
						echo "<td".$classCurrency."><span class='currency'></span><span class='amountright'>".number_format($totalTotalUSD,2,".",",")."</span></td>";
						echo "<td></td>";
					echo "</tr>";
				}
				
			echo "</tbody>";
		echo "</table>";
		
		
		foreach ($salesOrder['InvoiceSalesOrder'] as $invoiceSalesOrder){
			//pr($invoiceSalesOrder);
			$invoice=$invoiceSalesOrder['Invoice'];
			
			echo "<h3>".__('Productos para Facturas')." ".$invoice['invoice_code']."</h3>";
			if (!empty($invoice['InvoiceProduct'])){
				echo "<table cellpadding = '0' cellspacing = '0'>";
					echo "<tr>";
						echo "<th>".__('Product Id')."</th>";
						echo "<th>".__('Product Description')."</th>";
						echo "<th class='centered'>".__('Product Quantity')."</th>";
						echo "<th>".__('Product Unit Price')."</th>";
						echo "<th>".__('Product Total Price')."</th>";
						//echo "<th class='actions'>".__('Actions')."</th>";
					echo "</tr>";
					
				$currencyClass="";
				if ($invoice['Currency']['id']==CURRENCY_CS){
					$currencyClass="CScurrency";
				}
				elseif ($invoice['Currency']['id']==CURRENCY_USD){
					$currencyClass="USDcurrency";
				}
				$totalProductQuantity=0;
				foreach ($invoice['InvoiceProduct'] as $invoiceProduct){
					$totalProductQuantity+=$invoiceProduct['product_quantity'];
					echo "<tr>";
						echo "<td>".$this->Html->link($invoiceProduct['Product']['name'],array('controller'=>'products','action'=>'view',$invoiceProduct['Product']['name']),array('target'=>'_blank'))."</td>";
						echo "<td>".str_replace("\n","<br/>",$invoiceProduct['product_description'])."</td>";
						echo "<td class='amount centered'><span class='amount'>".$invoiceProduct['product_quantity']."</span></td>";
						echo "<td class='amount ".$currencyClass."'><span class='currency'></span><span class='amountright'>".$invoiceProduct['product_unit_price']."</td>";
						echo "<td class='amount ".$currencyClass."'><span class='currency'></span><span class='amountright'>".$invoiceProduct['product_total_price']."</span></td>";
					echo "</tr>";
				}
					echo "<tr class='totalrow'>";
						echo "<td>Subtotal</td>";
						echo "<td></td>";
						echo "<td class='centered'>".$totalProductQuantity."</td>";
						echo "<td></td>";
						echo "<td class='amount ".$currencyClass."'><span class='currency'></span><span class='amountright'>".$invoice['price_subtotal']."</span></td>";
						echo "<td></td>";
					echo "</tr>";
					echo "<tr class='totalrow'>";
						echo "<td>IVA</td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td class='amount ".$currencyClass."'><span class='currency'></span><span class='amountright'>".$invoice['price_iva']."</span></td>";
						echo "<td></td>";
					echo "</tr>";
					echo "<tr class='totalrow'>";
						echo "<td>Total</td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td class='amount ".$currencyClass."'><span class='currency'></span><span class='amountright'>".$invoice['price_total']."</span></td>";
						echo "<td></td>";
					echo "</tr>";
				echo "</table>";
			}
			else {
				echo "<h3>No hay productos en esta factura</h3>";
			}
		}
	}
?>
</div>
<?php 
/*
  echo '<div class="related">';
	if (!empty($salesOrder['ProductionOrder'])){
		echo "<h3>".__('Ordenes de Producción para esta Orden de Venta')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Production Order Date')."</th>";
					echo "<th>".__('Department')."</th>";
					echo "<th>".__('Production Order Code')."</th>";
					echo"<th class='actions'>".__('Actions')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
				foreach ($salesOrder['ProductionOrder'] as $productionOrder){
					$productionOrderDateTime=new DateTime($productionOrder['production_order_date']);
          $departmentArray=[];
          foreach ($productionOrder['ProductionOrderProduct'] as $productionOrderProduct){
            foreach ($productionOrderProduct['ProductionOrderProductDepartment'] as $productionOrderProductDepartment){
              if (!array_key_exists($productionOrderProductDepartment['Department']['id'],$departmentArray)){
                $departmentArray[$productionOrderProductDepartment['Department']['id']]=$productionOrderProductDepartment['Department']['name'];
              }
            }  
          }
          asort($departmentArray);
					if ($productionOrder['bool_annulled']){
						echo "<tr class='italic'>";
					}
					else {
						echo "<tr>";
					}
						echo "<td>".$productionOrderDateTime->format('d-m-Y')."</td>";
            echo "<td>";
            foreach ($departmentArray as $id=>$name){
              echo $this->Html->link($name,array('controller'=>'departments','action'=>'view',$id))."<br>";
            }
            echo "</td>";
						echo "<td>".$this->Html->link($productionOrder['production_order_code'].($productionOrder['bool_annulled']?' (Anulada)':''),array('controller'=>'production_orders','action'=>'view',$productionOrder['id']))."</td>";
						echo "<td class='actions'>";
							echo $this->Html->link(__('View'), array('controller' => 'production_orders', 'action' => 'view', $productionOrder['id']));
							//if ($bool_productionorder_edit_permission){
							//	echo $this->Html->link(__('Edit'), array('controller' => 'production_orders', 'action' => 'edit', $productionOrder['id']));
							//}
							//echo $this->Form->postLink(__('Delete'), array('controller' => 'production_orders', 'action' => 'delete', $productionOrder['id']), array(), __('Are you sure you want to delete # %s?', $productionOrder['id']));
						echo "</td>";
					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table>";
	}
  echo '</div>';
*/  
?>

<link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet" type="text/css">
<div style="float:left;width:100%;">
<?php 
  if ($bool_delete_permission){
    if (!empty($salesOrder['ProductionOrder'])){
      echo '<h3>No se puede eliminar la orden de venta porque ya hay una orden de producción asociada</h3>';
    }
    elseif (!empty($salesOrder['InvoiceSalesOrder'])){
      echo '<h3>No se puede eliminar la orden de venta porque ya hay facturas asociadas</h3>';
    }
    else {
      echo $this->Form->postLink(__($this->Html->tag('i', '', ['class' => 'glyphicon glyphicon-fire']).' '.'Eliminar Orden de Venta'), ['action' => 'delete', $salesOrder['SalesOrder']['id']], ['class' => 'btn btn-danger btn-sm','style'=>'text-decoration:none;','escape'=>false], __('Está seguro que quiere eliminar la orden de venta # %s?  PELIGRO, NO SE PUEDE DESHACER ESTA OPERACIÓN.  LOS DATOS DESPARECERÁN DE LA BASE DE DATOS!!!', $salesOrder['SalesOrder']['sales_order_code']));
      echo '<br/>';
    }
	}
?>
</div>