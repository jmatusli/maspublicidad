<script>
	$('body').on('change','.productdepartmentstate',function(){
    if ($(this).val() == <?php echo PRODUCTION_ORDER_STATE_SENT_NEXT_DEPARTMENT; ?>){
      $('.nextdepartment').removeClass("hidden");
      $('.nextdepartment').find('.instruction').attr("readonly",false);
      $('.nextdepartment').find('.productdepartmentstate option:not(:selected)').attr('disabled', false);
      $('.nextdepartment').find('.productdepartmentstate').val(<?php echo PRODUCTION_ORDER_STATE_AWAITING_PRODUCTION; ?>);
      $('.nextdepartment').find('.productdepartmentstate option:not(:selected)').attr('disabled', true);
    }
    else {
      $('.nextdepartment').addClass("hidden");
      $('.nextdepartment').find('.instruction').attr("readonly",true);
      $('.nextdepartment').find('.productdepartmentstate option:not(:selected)').attr('disabled', false);
      $('.nextdepartment').find('.productdepartmentstate').val(<?php echo PRODUCTION_ORDER_STATE_AWAITING_PREVIOUS; ?>);
      $('.nextdepartment').find('.productdepartmentstate option:not(:selected)').attr('disabled', true);
    }
	});
	
  $('body').on('click','.addInstruction',function(){
    $(this).closest('div').find('div.instruction.hidden:first').removeClass("hidden");
	});
	
	$('form').submit(function( e ) {          
		if ($('#DocumentUrlDocument0').val().length>0){
			if($('#DocumentUrlDocument0')[0].files[0].size > 5242880){
				alert("El documento excede 5MB!");        
				e.preventDefault();    
			}
			else {
				var file_extension=get_extension($('#DocumentUrlDocument0').val());
				var bool_valid_extension=check_validity_extension(file_extension);
				if (!bool_valid_extension){
					alert("Solamente se permiten archivos jpg, jpeg, png y pdf!");        
					e.preventDefault();    
				}
			}
		}
	});
	
	function get_extension(filename) {    
		var parts = filename.split('.');    
		return parts[parts.length - 1].toLowerCase();
	}
	
	function check_validity_extension(file_extension) {    
		if (file_extension=="jpg" ||file_extension=="jpeg" ||file_extension=="png" ||file_extension=="pdf"){
			return true;
		} 
		else {
			return false;
		}
	}
	
	$(document).ready(function(){
		$('select.fixed option:not(:selected)').attr('disabled', true);
	});
	
	$( document).ajaxComplete(function() {
		$('select.fixed option:not(:selected)').attr('disabled', true);
	});
</script>

<div class="productionOrderProducts form fullwidth">
<?php 
	if (empty($productionOrderProductId)){
		echo '<h1>No se especifó el producto de la orden de producción</h1>';
	}
	else {
    $productionOrderDateTime=new DateTime($productionOrderProduct['ProductionOrder']['production_order_date']);
		
    echo '<h1>Procesar producto '.$productionOrderProduct['Product']['name'].' de la orden de producción '.$productionOrderProduct['ProductionOrder']['production_order_code'].'</h1>';
    echo $this->Form->create('ProductionOrder',['enctype' => 'multipart/form-data']); 
		echo '<fieldset>';
			//echo '<legend>'.__('Add Production Order').'</legend>';
			echo '<div class="container-fluid">';
				echo '<div class="row">';
					echo '<div class="col-sm-10">';
            echo '<h2>Datos de producto</h2>';
						echo '<dl>';
              echo '<dt>'.__('Product').'</dt>';
              echo '<dd>'.$productionOrderProduct['ProductionOrderProduct']['product_id'].'</dd>';
              echo '<dt>'.__('Descripción').'</dt>';
              echo '<dd>'.$productionOrderProduct['ProductionOrderProduct']['product_description'].'</dd>';
              echo '<dt>'.__('Cantidad de Producto').'</dt>';
              echo '<dd>'.$productionOrderProduct['ProductionOrderProduct']['product_quantity'].'</dd>';
              echo '<dt>'.__('Operation Location').'</dt>';
              echo '<dd>';
              if (empty($productionOrderProduct['ProductionOrderProduct']['ProductionOrderProductOperationLocation'])){
                echo "-";
              }
              else {
                foreach ($productionOrderProduct['ProductionOrderProduct']['ProductionOrderProductOperationLocation'] as $operationLocation){
                  echo $operationLocation['name'].'<br/>';
                }
              }
              echo '</dd>';
            echo '</dl>';
					  echo '<h2>Datos de orden de producción</h2>';
            echo '<dl>';
              echo '<dt>'.__('Production Order Date').'</dt>';
              echo '<dd>'.$productionOrderDateTime->format('d-m-Y').'</dd>';
              echo '<dt>'.__('Production Order Code').'</dt>';
              echo '<dd>'.h($productionOrderProduct['ProductionOrder']['production_order_code']).'</dd>';
              echo '<dt>'.__('Sales Order').'</dt>';
              //echo '<dd>'.$this->Html->link($productionOrderProduct['ProductionOrder']['SalesOrder']['sales_order_code'],['controller' => 'sales_orders', 'action' => 'view', $productionOrderProduct['ProductionOrder']['SalesOrder']['id']]).'</dd>';
              echo '<dd>'.$productionOrderProduct['ProductionOrder']['SalesOrder']['sales_order_code'].'</dd>';
            echo '</dl>';
            
						//echo $this->Form->input('Document.url_doc.0',['label'=>'Cargar Documento de Diseño (5 MB max)','type'=>'file']);
            
            //echo $this->Form->input('ProductionOrderRemark.user_id',['label'=>'Vendedor','value'=>$loggedUserId,'type'=>'hidden']);
						//echo $this->Form->input('ProductionOrderRemark.remark_text',['rows'=>'2','required'=>false]);	
					echo '</div>';
					
					echo '<div class="col-sm-2">';
						echo '<h3>'.__('Actions').'</h3>';
						echo '<ul>';
              echo '<li>'.$this->Html->link(__('Reporte Producción Pendiente'), ['controller'=>'salesOrders','action' => 'reporteProduccionPendiente']).'</li>';
							echo '<li>'.$this->Html->link(__('List Production Orders'), ['action' => 'index']).'</li>';
							//echo '<br/>';
							//if ($bool_salesorder_index_permission){
							//	echo '<li>'.$this->Html->link(__('List Sales Orders'), ['controller' => 'sales_orders', 'action' => 'index']).' </li>';
							//}
							//if ($bool_salesorder_add_permission){
							//	echo '<li>'.$this->Html->link(__('New Sales Order'), ['controller' => 'sales_orders', 'action' => 'add']).' </li>';
							//}
						echo '</ul>';
					echo '</div>';
				echo '</div>';				
				echo '<div class="row">';
        //pr($productionOrderProduct);
        $currentDepartmentCounter=0;
        for ($j=0;$j <= count($productionOrderProduct['ProductionOrderProductDepartment']);$j++){
          if ($productionOrderProduct['ProductionOrderProductDepartment'][$j]['id'] == $productionOrderProduct['ProductionOrderProduct']['current_production_order_product_department_id']){
            $currentDepartmentCounter=$j;
            break;
          }
        }
        //echo 'currentdepartmentcounter '.$currentDepartmentCounter.'<br/>';
        for ($j=0;$j <= count($productionOrderProduct['ProductionOrderProductDepartment']);$j++){
          if ($j <  count($productionOrderProduct['ProductionOrderProductDepartment'])){
            $productionOrderProductDepartment=$productionOrderProduct['ProductionOrderProductDepartment'][$j];
            //pr($productionOrderProductDepartment);
          }
          else {
            $productionOrderProductDepartment=null;
          }
					echo '<div 
            class="col-sm-12'.
              ($j == count($productionOrderProduct['ProductionOrderProductDepartment'])?' hidden newdepartment':'').
              ($j == $currentDepartmentCounter + 1 ?' nextdepartment':'').
            '" 
            departmentid="'.(empty($productionOrderProductDepartment)?0:$productionOrderProductDepartment['department_id']).'"
            style="border:1px solid black;margin:2px;background-color:'.($j == $currentDepartmentCounter?"#90ee90":"#add8e6" ).';"
          >';
          
            echo $this->Form->input('ProductionOrderProductDepartment.'.$j.'.id',[
              'type'=>'hidden',
              'value'=>(empty($productionOrderProductDepartment)?0:$productionOrderProductDepartment['id']),
            ]);
						echo $this->Form->input('ProductionOrderProductDepartment.'.$j.'.department_id',[
              'label'=>'Departamento',
              'default'=>($j == count($productionOrderProduct['ProductionOrderProductDepartment'])?0:$productionOrderProductDepartment['department_id']),
              'class'=>($j == count($productionOrderProduct['ProductionOrderProductDepartment'])?"":"fixed"),
              'empty'=>[0=>'-- Departamento --'],
            ]);
            echo $this->Form->input('ProductionOrderProductDepartment.'.$j.'.state_id',[
              'label'=>'Estado de Producto en Departamento',
              'default'=>($j == count($productionOrderProduct['ProductionOrderProductDepartment'])?0:end($productionOrderProductDepartment['ProductionOrderProductDepartmentState'])['production_order_state_id']),
              'class'=>($j == $currentDepartmentCounter?"productdepartmentstate":"productdepartmentstate fixed"),
              'options'=>$productionOrderStates,
            ]);
            echo '<h4>Instrucciones para departamento</h4>';
          
            for ($k=0;$k<INSTRUCTIONS_MAX;$k++){
              $instructionUser=($j == count($productionOrderProduct['ProductionOrderProductDepartment'])?"":(
                  array_key_exists($k,$productionOrderProductDepartment['ProductionOrderProductDepartmentInstruction'])?
                  ($users[$productionOrderProductDepartment['ProductionOrderProductDepartmentInstruction'][$k]['user_id']]):
                  ""
                )
              );
              $instructionDateTime=($j == count($productionOrderProduct['ProductionOrderProductDepartment'])?null:(
                  array_key_exists($k,$productionOrderProductDepartment['ProductionOrderProductDepartmentInstruction'])?
                  (new DateTime($productionOrderProductDepartment['ProductionOrderProductDepartmentInstruction'][$k]['instruction_datetime'])):
                  null
                )
              );
              $instructionDateTimeText=(empty($instructionDateTime)?"":($instructionDateTime->format('d-m-Y H:i')));
              $instructionLabel=($k+1).". ".$instructionUser." <br/>".$instructionDateTimeText;
              $instructionDefaultValue=($j == count($productionOrderProduct['ProductionOrderProductDepartment'])?"":(array_key_exists($k,$productionOrderProductDepartment['ProductionOrderProductDepartmentInstruction'])?
                $productionOrderProductDepartment['ProductionOrderProductDepartmentInstruction'][$k]['instruction_text']:""));
              $instructionCount=$j == count($productionOrderProduct['ProductionOrderProductDepartment'])?0:(array_key_exists($k,$productionOrderProductDepartment['ProductionOrderProductDepartmentInstruction'])?
                count($productionOrderProductDepartment['ProductionOrderProductDepartmentInstruction']):0);
              
              $instructionId=($j == count($productionOrderProduct['ProductionOrderProductDepartment'])?0:(array_key_exists($k,$productionOrderProductDepartment['ProductionOrderProductDepartmentInstruction'])?
                $productionOrderProductDepartment['ProductionOrderProductDepartmentInstruction'][$k]['id']:0));
              
              echo $this->Form->input('ProductionOrderProductDepartment.'.$j.'.ProductionOrderProductDepartmentInstruction.'.$k.'.id',[
                'type'=>'hidden',
                'value'=>$instructionId,
              ]);
              echo $this->Form->input('ProductionOrderProductDepartment.'.$j.'.ProductionOrderProductDepartmentInstruction.'.$k.'.instruction_text',[
                'type'=>'text',
                'rows'=>2,
                'default'=>$instructionDefaultValue,
                'style'=>'width:70%',
                'readonly'=>$k < $instructionCount?true:false,
                'div'=>[
                    'class'=> ($j == $currentDepartmentCounter || $j == $currentDepartmentCounter +1)?
                      ($k <= $instructionCount?'instruction input text ':'instruction input text hidden'):
                      ($k < $instructionCount?'instruction input text ':'instruction input text hidden')
                  ],
                'label'=>[
                  'text'=>$instructionLabel,
                  'style'=>'width:25%;'
                ],
                'placeholder'=>'Escriba sus instrucciones ...',
                'instructionnumber'=>$k,
              ]);
            }
            if ($j>=$currentDepartmentCounter){
              echo '<button class="addInstruction" type="button">Añadir Instrucción</button>';
            }
            
          echo '</div>';  
        }    
				echo '</div>';				
			echo '</div>';
		echo '</fieldset>';
		echo $this->Form->submit('Guardar'); 
    echo $this->Form->end(); 
	}
?>
</div>
