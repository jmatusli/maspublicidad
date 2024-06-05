<div class="productionOrders view fullwidth">
<?php 
	echo "<h1>".__('Production Order')." ".$productionOrder['ProductionOrder']['production_order_code']."</h1>";
  
  
  
	$productionOrderDateTime=new DateTime($productionOrder['ProductionOrder']['production_order_date']);
	echo "<div class='container-fluid'>";
		echo "<div class='row'>";
      echo "<div class='col-sm-6'>";
        echo $this->Form->create('Report');
          echo "<fieldset>";
            echo $this->Form->input('Report.display_option_id',['label'=>'Mostrar','default'=>$displayOptionId]);
          echo "</fieldset>";
        echo "<br/>";
        echo $this->Form->Submit(__('Refresh'));
        echo $this->Form->end();
      
        echo '<dl class="dl50">';
          echo "<dt>".__('Sales Order')."</dt>";
          echo "<dd>".$this->Html->link($productionOrder['SalesOrder']['sales_order_code'], array('controller' => 'salesOrders', 'action' => 'detalle', $productionOrder['SalesOrder']['id']))."</dd>";
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
      echo "<div class='col-sm-3'>";
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
      echo "<div class='col-sm-3 actions'>";
        echo "<h2>".__('Actions')."</h2>";
        echo "<ul>";
          echo "<li>".$this->Html->link('Guardar como pdf', ['action' => 'detallePdf','ext'=>'pdf', $productionOrder['ProductionOrder']['id'],$filename],['target'=>'_blank'])."</li>";
          if ($bool_edit_permission){
            echo "<li>".$this->Html->link(__('Edit Production Order'), ['action' => 'editar', $productionOrder['ProductionOrder']['id']])."</li>";
            echo "<br/>";
          }
          if ($bool_annul_permission){
            echo "<li>".$this->Form->postLink(__('Anular'), ['action' => 'annul', $productionOrder['ProductionOrder']['id']], [], __('Está seguro que quiere anular orden de producción # %s?', $productionOrder['ProductionOrder']['production_order_code']))."</li>";
            echo "<br/>";
          }
          echo "<li>".$this->Html->link(__('List Production Orders'), ['action' => 'resumen'])."</li>";
          echo "<br/>";
          if ($bool_salesorder_index_permission){
            echo "<li>".$this->Html->link('Producción Pendiente', ['controller' => 'salesOrders', 'action' => 'reporteProduccionPendiente'])." </li>";
          }
        echo "</ul>";			
      echo "</div>";				
		echo "</div>";				
	echo "</div>";				
?> 
</div>
<div class="related">
<?php 
	if (!empty($productionOrder['ProductionOrderProduct'])){
		echo '<h3>'.__('Productos en esta orden de producción').'</h3>';
		echo '<table>';
			echo '<tr>';
				echo '<th>Cant.</th>';
        echo '<th>Producto</th>';
        echo '<th>Descripción</th>';
				echo '<th>'.__('Operation Location').'</th>';
			echo '</tr>';
		foreach ($productionOrder['ProductionOrderProduct'] as $productionOrderProduct){ 
			echo '<tr>';
				echo '<td>'.$productionOrderProduct['product_quantity'].'</td>';
        echo '<td>'.$productionOrderProduct['Product']['name'].'</td>';
				echo '<td style="background-color:#bbbbff">'.$productionOrderProduct['product_description'].'</td>';
				echo '<td>';
				foreach ($productionOrderProduct['ProductionOrderProductOperationLocation'] as $productOperationLocation){
					if (!empty($productOperationLocation['OperationLocation'])){
						//pr($productOperationLocation['OperationLocation']);
						echo $productOperationLocation['OperationLocation']['name'].'<br/>';
					}
				}
				echo '</td>';
      echo '</tr>';
      foreach ($productionOrderProduct['ProductionOrderProductDepartment'] as $productionOrderProductDepartment){
        //pr($productionOrderProductDepartment['ProductionOrderProductDepartmentState']));
        //pr(end($productionOrderProductDepartment['ProductionOrderProductDepartmentState'])['production_order_state_id']);
        switch (end($productionOrderProductDepartment['ProductionOrderProductDepartmentState'])['production_order_state_id']){
          case PRODUCTION_ORDER_STATE_AWAITING_PREVIOUS:
            echo '<tr style="background-color:red;">';  
            break;
          case PRODUCTION_ORDER_STATE_SENT_NEXT_DEPARTMENT:
          case PRODUCTION_ORDER_STATE_READY_FOR_DELIVERY:
          case PRODUCTION_ORDER_STATE_DELIVERED:
            echo '<tr style="background-color:green;color:white;">';  
            break;
          
          default:
            echo '<tr>';  
        }
          echo '<td>Dept.</td>';
          echo '<td style="font-weight:700;">'.$productionOrderProductDepartment['Department']['name'].'</td>';
          echo '<td>';
          if (empty($productionOrderProductDepartment['ProductionOrderProductDepartmentInstruction'])){
            echo "-";
          }
          else {
            foreach ($productionOrderProductDepartment['ProductionOrderProductDepartmentInstruction'] as $productionOrderProductDepartmentInstruction){
              $instructionDateTime=new DateTime($productionOrderProductDepartmentInstruction['instruction_datetime']);
              echo $productionOrderProductDepartmentInstruction['instruction_text'].' ('.$users[$productionOrderProductDepartmentInstruction['user_id']].' el '.$instructionDateTime->format('d-m-Y H:i').')<br/>';
            }
          }
          echo '</td>';
          echo '<td>';
          foreach ($productionOrderProductDepartment['ProductionOrderProductDepartmentState'] as $productionOrderProductDepartmentState){
            $stateDateTime=new DateTime($productionOrderProductDepartmentState['state_datetime']);
            echo $productionOrderProductDepartmentState['ProductionOrderState']['name'].' ('.$users[$productionOrderProductDepartmentState['user_id']].' el '.$stateDateTime->format('d-m-Y H:i').')<br/>';
          }
          echo '</td>';
        echo '</tr>';
      }
      
		}
		echo '</table>';
	}
?>
</div>
<div class="related">
<?php 
	if (!empty($productionOrder['ProductionOrderRemark'])){
		echo '<h2>'.__('Remarcas para esta orden de producción').'</h2>';
		echo '<table>';
			echo '<tr>';
				echo '<th>Fecha</th>';
				echo '<th>'.__('Username').'</th>';
				echo '<th>Remarca</th>';
			echo '</tr>';
		foreach ($productionOrder['ProductionOrderRemark'] as $productionOrderRemark){ 
			$productionOrderRemarkDateTime=new DateTime($productionOrderRemark['remark_datetime']);
			echo '<tr>';
				echo '<td>'.$productionOrderRemarkDateTime->format('d-m-Y H:i').'</td>';
				echo '<td>'.$productionOrderRemark['User']['username'].'</td>';
				echo '<td>'.$this->Html->link($productionOrderRemark['remark_text'], ['controller' => 'production_order_remarks', 'action' => 'view', $productionOrderRemark['id']]).'</td>';
				
			echo '</tr>';
		}
		echo '</table>';
	}
?>
</div>
<link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet" type="text/css">
<div style="float:left;width:100%;">
<?php 
		if ($bool_delete_permission){
			echo $this->Form->postLink(__($this->Html->tag('i', '', ['class' => 'glyphicon glyphicon-fire']).' '.'Eliminar Orden de Producción'), ['action' => 'delete', $productionOrder['ProductionOrder']['id']], ['class' => 'btn btn-danger btn-sm','style'=>'text-decoration:none;','escape'=>false], __('Está seguro que quiere eliminar la orden de producción # %s?  PELIGRO, NO SE PUEDE DESHACER ESTA OPERACIÓN.  LOS DATOS DESPARECERÁN DE LA BASE DE DATOS!!!', $productionOrder['ProductionOrder']['production_order_code']));
	echo '<br/>';
		}
?>
</div>