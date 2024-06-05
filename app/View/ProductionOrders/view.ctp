<div class="productionOrders view fullwidth">
<?php 
	echo "<h2>".__('Production Order')." ".$productionOrder['ProductionOrder']['production_order_code']."</h2>";
	$productionOrderDateTime=new DateTime($productionOrder['ProductionOrder']['production_order_date']);
	echo "<div class='container-fluid'>";
		echo "<div class='row'>";
				echo "<div class='col-md-6'>";
					echo "<dl>";
						echo "<dt>".__('Sales Order')."</dt>";
						echo "<dd>".$this->Html->link($productionOrder['SalesOrder']['sales_order_code'], array('controller' => 'sales_orders', 'action' => 'view', $productionOrder['SalesOrder']['id']))."</dd>";
						//echo "<dt>".__('Previous Production Order')."</dt>";
						//if (!empty($productionOrder['PreviousProductionOrder']['id'])){
						//	echo "<dd>".$this->Html->link($productionOrder['PreviousProductionOrder']['production_order_code'], array('controller' => 'production_orders', 'action' => 'view', $productionOrder['PreviousProductionOrder']['id']))."</dd>";
						//}
						//else {
						//	echo "<dd>-</dd>";
						//}
						echo "<dt>".__('Production Order Date')."</dt>";
						echo "<dd>".$productionOrderDateTime->format('d-m-Y')."</dd>";
						echo "<dt>".__('Production Order Code')."</dt>";
						echo "<dd>".h($productionOrder['ProductionOrder']['production_order_code'])."</dd>";
						echo "<dt>".__('Bool Annulled')."</dt>";
						echo "<dd>".h($productionOrder['ProductionOrder']['bool_annulled']?__('Yes'):__('No'))."</dd>";
						echo "<dt>".__('Instructions')."</dt>";
						if (!empty($productionOrder['ProductionOrder']['instructions'])){
							echo "<dd>".h($productionOrder['ProductionOrder']['instructions'])."</dd>";
						}
					else {
						echo "<dd>-</dd>";
					}
					//echo "<dt>".__('Url Doc')."</dt>";
					//echo "<dd>".h($productionOrder['ProductionOrder']['url_doc'])."</dd>";
				echo "</dl>";
			echo "</div>";
			echo "<div class='col-md-3'>";
				if (!empty($productionOrder['ProductionOrder']['url_doc'])){
					$url=$productionOrder['ProductionOrder']['url_doc'];
					
					//echo $url;
					switch (substr($url,-4)){
						case ".jpg":
						case "jepg":
						case ".png":
							echo "<img src='".$this->Html->url('/').$url."' alt='Producto' class='resize'></img>";
							break;
						case ".pdf":
							break;
					}
				}
			echo "</div>";
			echo "<div class='col-md-3 actions'>";
				echo "<h3>".__('Actions')."</h3>";
				echo "<ul>";
					echo "<li>".$this->Html->link(__('Guardar como pdf'), array('action' => 'viewPdf','ext'=>'pdf', $productionOrder['ProductionOrder']['id'],$filename),array('target'=>'_blank'))."</li>";
					if ($bool_edit_permission){
						echo "<li>".$this->Html->link(__('Edit Production Order'), array('action' => 'edit', $productionOrder['ProductionOrder']['id']))."</li>";
						echo "<br/>";
					}
					if ($bool_delete_permission){
						echo "<li>".$this->Form->postLink(__('Eliminar Orden de Producción'), array('action' => 'delete', $productionOrder['ProductionOrder']['id']), array(), __('Está¡ seguro que quiere eliminar orden de producción %s?', $productionOrder['ProductionOrder']['production_order_code']))."</li>";
					}
					if ($bool_annul_permission){
						echo "<li>".$this->Form->postLink(__('Anular'), array('action' => 'annul', $productionOrder['ProductionOrder']['id']), array(), __('Está seguro que quiere anular orden de producción # %s?', $productionOrder['ProductionOrder']['production_order_code']))."</li>";
					}
					if ($bool_delete_permission||$bool_annul_permission){
						echo "<br/>";
					}
					echo "<li>".$this->Html->link(__('List Production Orders'), array('action' => 'index'))."</li>";
					echo "<li>".$this->Html->link(__('New Production Order'), array('action' => 'add'))."</li>";
					echo "<br/>";
					if ($bool_salesorder_index_permission){
						echo "<li>".$this->Html->link(__('List Sales Orders'), array('controller' => 'sales_orders', 'action' => 'index'))." </li>";
					}
					if ($bool_salesorder_add_permission){
						echo "<li>".$this->Html->link(__('New Sales Order'), array('controller' => 'sales_orders', 'action' => 'add'))." </li>";
					}
				echo "</ul>";			
			echo "</div>";				
		echo "</div>";				
	echo "</div>";				
?> 
</div>
<!--div class='actions'>
</div-->
<div class="related">
<?php 
	if (!empty($productionOrder['ProductionOrderProduct'])){
		echo "<h3>".__('Productos en esta orden de producción')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Product')."</th>";
				echo "<th>".__('Description')."</th>";
				echo "<th>".__('Instructions')."</th>";
				echo "<th>".__('Product Quantity')."</th>";
				echo "<th>".__('Operation Location')."</th>";
				echo "<th>".__('Department')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($productionOrder['ProductionOrderProduct'] as $productionOrderProduct){ 
			echo "<tr>";
				echo "<td>".$productionOrderProduct['Product']['name']."</td>";
				echo "<td>".$productionOrderProduct['product_description']."</td>";
				echo "<td>".$productionOrderProduct['product_instruction']."</td>";
				echo "<td>".$productionOrderProduct['product_quantity']."</td>";
				echo "<td>";
				foreach ($productionOrderProduct['ProductionOrderProductOperationLocation'] as $productOperationLocation){
					if (!empty($productOperationLocation['OperationLocation'])){
						//pr($productOperationLocation['OperationLocation']);
						echo $productOperationLocation['OperationLocation']['name']."<br/>";
					}
				}
				echo "</td>";
				echo "<td>";
				foreach ($productionOrderProduct['ProductionOrderProductDepartment'] as $productDepartment){
					if (!empty($productDepartment['Department'])){
						//pr($productDepartment['Department']);
						echo ($productDepartment['rank']+1).". ".$productDepartment['Department']['name']."<br/>";
					}
				}
				echo "</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'production_order_products', 'action' => 'view', $productionOrderProduct['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'production_order_products', 'action' => 'edit', $productionOrderProduct['id']));
					//echo $this->Form->postLink(__('Delete'), array('controller' => 'production_order_products', 'action' => 'delete', $productionOrderProduct['id']), array(), __('Are you sure you want to delete # %s?', $productionOrderProduct['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
<div class="related">
<?php 
	if (!empty($productionOrder['ProductionOrderRemark'])){
		echo "<h3>".__('Remarcas para esta orden de producción')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<tr>";
				echo "<th>".__('Username')."</th>";
				echo "<th>".__('Remark Datetime')."</th>";
				echo "<th>".__('Remark Text')."</th>";
				echo"<th class='actions'>".__('Actions')."</th>";
			echo "</tr>";
		foreach ($productionOrder['ProductionOrderRemark'] as $productionOrderRemark){ 
			$productionOrderRemarkDateTime=new DateTime($productionOrderRemark['remark_datetime']);
			echo "<tr>";
				echo "<td>".$productionOrderRemark['User']['username']."</td>";
				echo "<td>".$productionOrderRemarkDateTime->format('d-m-Y H:i')."</td>";
				echo "<td>".$productionOrderRemark['remark_text']."</td>";
				echo "<td class='actions'>";
					echo $this->Html->link(__('View'), array('controller' => 'production_order_remarks', 'action' => 'view', $productionOrderRemark['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'production_order_remarks', 'action' => 'edit', $productionOrderRemark['id']));
					//echo $this->Form->postLink(__('Delete'), array('controller' => 'production_order_remarks', 'action' => 'delete', $productionOrderRemark['id']), array(), __('Are you sure you want to delete # %s?', $productionOrderRemark['id']));
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>
</div>
