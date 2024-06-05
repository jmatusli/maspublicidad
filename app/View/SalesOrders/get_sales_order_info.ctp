<?php
	
	if (!empty($salesOrders)){
		//pr($salesOrder);
    $boolInvoicesPresent=false;
		foreach ($salesOrders as $salesOrder){
			$salesOrderDateTime=new DateTime($salesOrder['SalesOrder']['sales_order_date']);
      if (!empty($salesOrder['InvoiceSalesOrder'])){
        $boolInvoicesPresent=true;
      }
      
			echo '<h2 style="clear:left;">Resumen Orden Venta '.$salesOrder['SalesOrder']['sales_order_code'].'</h2>';
			echo "<dl>";
				echo "<dt>Ejecutivo</dt>";
				echo "<dd>".$this->Html->link($salesOrder['Quotation']['quotation_code'],array('controller'=>'quotations','action'=>'view',$salesOrder['Quotation']['id']))."</dd>";
				echo "<dt>Cliente</dt>";
				echo "<dd>".$this->Html->link($salesOrder['Quotation']['Client']['name'],array('controller'=>'clients','action'=>'view',$salesOrder['Quotation']['Client']['id']))."</dd>";
				echo "<dt>Contacto</dt>";
				echo "<dd>".$this->Html->link($salesOrder['Quotation']['Contact']['first_name']." ".$salesOrder['Quotation']['Contact']['last_name'],array('controller'=>'contacts','action'=>'view',$salesOrder['Quotation']['Contact']['id']))."</dd>";
				echo "<dt>Teléfono</dt>";
				echo "<dd>".$salesOrder['Quotation']['Contact']['phone']."</a></dd>";
				echo "<dt>Correo</dt>";
				echo "<dd><a href='mailto:".$salesOrder['Quotation']['Contact']['email']."'>".$salesOrder['Quotation']['Contact']['email']."</a></dd>";
				echo "<dt>Fecha Orden</dt>";
				echo "<dd>".$salesOrderDateTime->format('d-m-Y')."</dd>";
			echo "</dl>";
		}
    
    if ($boolInvoicesPresent){    
      echo '<h2 style="clear:left;">Facturas ya emitidas para orden de venta</h2>';
      $invoiceTableHead='';
      $invoiceTableHead.='<thead>';
        $invoiceTableHead.='<tr>';
          $invoiceTableHead.='<th># OV</th>';
          $invoiceTableHead.='<th>Fecha</th>';
          $invoiceTableHead.='<th># Factura</th>';
        $invoiceTableHead.='</tr>';
      $invoiceTableHead.='</thead>';
      $invoiceTableRows='';
      foreach ($salesOrders as $salesOrder){
        foreach ($salesOrder['InvoiceSalesOrder'] as $invoiceSalesOrder){
          //pr($invoiceSalesOrder);  
          $invoice=$invoiceSalesOrder['Invoice'];
          $invoiceDateTime=new DateTime($invoice['invoice_date']);
          
          $invoiceTableRows.='<tr>';
            $invoiceTableRows.='<td>'.$this->Html->link($salesOrder['SalesOrder']['sales_order_code'],['controller'=>'salesOrders','action'=>'view',$salesOrder['SalesOrder']['id']]).'</td>';  
            $invoiceTableRows.='<td>'.$invoiceDateTime->format('d-m-Y').'</td>';
            $invoiceTableRows.='<td'.($invoice['bool_annulled']?' style="font-style:italic;"':'').'>'.$this->Html->link($invoice['invoice_code'],['controller'=>'invoices','action'=>'detalle',$invoice['id']]).($invoice['bool_annulled']?' (Anulada)':'').'</td>';  
          
          $invoiceTableRows.='</tr>';
        }
      }
      $invoiceTableBody='<tbody>'.$invoiceTableRows.'</tbody>';
      $invoiceTable='<table>'.$invoiceTableHead.$invoiceTableBody.'</table>';
      echo $invoiceTable;
    }
    else {
      if ($currentInvoiceId == 0){
        echo '<h2>Aun no hay facturas para esta orden de venta</h2>';
      }
      else {
        echo '<h2>Solo existe esta factura para esta orden de venta</h2>';
      }
    }    
  }
?>
<script>
	$(document).ajaxComplete(function() {	
	});
</script>