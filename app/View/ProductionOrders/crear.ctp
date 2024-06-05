<script>
	$('body').on('change','#ProductionOrderSalesOrderId',function(){	
		loadSalesOrderInfo();
		getNewProductionOrderCode();
		loadSalesOrderProducts();
	});
	
	function loadSalesOrderInfo(){
		var sales_order_id=$('#ProductionOrderSalesOrderId').children("option").filter(":selected").val();
		if (sales_order_id>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>sales_orders/getsalesorderinfonofinance/',
				data:{"sales_order_id":sales_order_id},
				cache: false,
				type: 'POST',
				success: function (salesorderinfo) {
					$('#salesOrderInfo').html(salesorderinfo);
				},
				error: function(e){
					console.log(e);
					alert(e.ResponseText);
				}
			});
		}
		else {
			$('#salesOrderInfo').html("<div><span class='bold '>&nbsp;</span></div><div><span class='bold '>&nbsp;</span></div>");
		}
	}
	
	function getNewProductionOrderCode(){
		var sales_order_id=$('#ProductionOrderSalesOrderId').children("option").filter(":selected").val();
		if (sales_order_id>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>production_orders/getnewproductionordercode/',
				data:{"sales_order_id":sales_order_id},
				cache: false,
				type: 'POST',
				success: function (productionordercode) {
					$('#ProductionOrderProductionOrderCode').val(productionordercode);
				},
				error: function(e){
					console.log(e);
					alert(e.responseText);
				}
			});
		}
	}
	
	function loadSalesOrderProducts(){
		var sales_order_id=$('#ProductionOrderSalesOrderId').children("option").filter(":selected").val();
		if (sales_order_id>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>production_orders/getproductsforsalesorder/',
				data:{"sales_order_id":sales_order_id},
				cache: false,
				type: 'POST',
				success: function (products) {
					$('#ProductTable').html(products);
				},
				error: function(e){
					console.log(e);
					alert(e.ResponseText);
				}
			});
		}
		else {
			$('#ProductTable').empty();
		}
	}
	
	$('body').on('change','#ProductionOrderBoolAnnulled',function(){	
		hideFieldsForAnnulled();
	});
	
	function hideFieldsForAnnulled(){
		if ($('#ProductionOrderBoolAnnulled').is(':checked')){
			$('#ProductionOrderInstructions').parent().addClass('hidden');
			$('#productos').addClass('hidden');
		}
		else {
			$('#ProductionOrderInstructions').parent().removeClass('hidden');
			$('#productos').removeClass('hidden');
		}
	}
/*  
  $('body').on('change','.productionorderdepartmentid div select',function(){
    var rank=$(this).closest('tr').attr('rank');
    $('#productos').find('tr[rank="'+rank+'"]').removeClass("hidden");
    $('#productos').find('tr[rank="'+rank+'"] td.departmentid div select').val($(this).val());
	});
  $('body').on('change','.productionorderdepartmentinstruction div textarea',function(){
    var instructionnumber=$(this).attr('instructionnumber');
    var rank=$(this).closest('tr').attr('rank');
    var value=$(this).val();
    $('#productos').find('tr[rank="'+rank+'"] td.productionorderproductdepartmentinstruction div textarea[instructionnumber="'+instructionnumber+'"]').val($(this).val());
	});
  
	$('body').on('click','.addDepartment',function(){
    $('#departamentos tbody tr.hidden:first').removeClass("hidden");
	});
	$('body').on('click','.removeDepartment',function(){
		var tableRow=$(this).closest('tr').remove();
	});
*/  
  $('body').on('click','.addInstruction',function(){
    $(this).closest('tr').find('td.productionorderinstruction div.hidden:first').removeClass("hidden");
	});
  
	$('body').on('click','.addProductDepartment',function(){
    var productid=$(this).closest('tr').attr('product');
    $('#productos tbody tr.department.hidden[product="'+productid+'"]:first').removeClass("hidden");
	});
	$('body').on('click','.removeProductDepartment',function(){
		var tableRow=$(this).closest('tr').remove();
	});
  
  $('body').on('click','.addproductionorderproductdepartmentinstruction',function(){
    $(this).closest('tr').find('td.productionorderproductdepartmentinstruction div.hidden:first').removeClass("hidden");
	});
	
	$('body').on('change','.departmentid div select',function(){
		if ($(this).val()){
			var oneEmptyRowVisible=false;
			$(this).closest('td').find('div select:not(".hidden")').each(function(){
				if ($(this).val()==0){
					oneEmptyRowVisible=true;
				}
			});
			if (!oneEmptyRowVisible){
				var selectnextdepartment=$(this).closest('td').find('div select.hidden:first');
				var spannumber=$(this).closest('td').find('span.hidden:first');
				var spanplusbutton=$(this).closest('td').find('span.plusbutton.hidden:first');			
				selectnextdepartment.removeClass("hidden");
				spannumber.removeClass("hidden");
				spanplusbutton.removeClass("hidden");
			}
		}
		else {
			/* do nothing, because it should hold a value */
		}
	});	
	
	function calculateTotal(){
		var totalProductQuantity=0;
		$("#productos tbody tr:not(.hidden) td.productquantity div input").each(function() {
			if (!isNaN($(this).val())){
				var currentQuantity = parseFloat($(this).val());
				totalProductQuantity += currentQuantity;
			}
		});
		$('tr.totalrow td.productquantity span').text(totalProductQuantity.toFixed(0));
		
	}
	
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
		if ($('#ProductionOrderProductionOrderCode').val()==""){
			getNewProductionOrderCode();
		}
		
		hideFieldsForAnnulled();
		
		$('#ProductionOrderRemarkUserId').addClass('fixed');
		$('#ProductionOrderRemarkRemarkDatetimeDay').addClass('fixed');
		$('#ProductionOrderRemarkRemarkDatetimeMonth').addClass('fixed');
		$('#ProductionOrderRemarkRemarkDatetimeYear').addClass('fixed');
		$('#ProductionOrderRemarkRemarkDatetimeHour').addClass('fixed');
		$('#ProductionOrderRemarkRemarkDatetimeMin').addClass('fixed');
		$('#ProductionOrderRemarkRemarkDatetimeMeridian').addClass('fixed');
		
		$('select.fixed option:not(:selected)').attr('disabled', true);
	});
	
	$( document ).ajaxComplete(function() {
		$('select.fixed option:not(:selected)').attr('disabled', true);
	});
</script>

<div class="productionOrders form fullwidth">
<?php 
	if (empty($salesOrders)){
		echo "<h2>No hay ordenes de venta sin orden de producción</h2>";
	}
	else {
		echo $this->Form->create('ProductionOrder',['enctype' => 'multipart/form-data']); 
		echo "<fieldset>";
			echo "<legend>".__('Add Production Order')."</legend>";
			echo "<div class='container-fluid'>";
				echo "<div class='row'>";
					echo "<div class='col-sm-10'>";
						echo $this->Form->input('sales_order_id',['default'=>'0','value'=>$salesOrderId,'empty'=>[0=>'-- Orden de Venta --']]);
						echo $this->Form->input('production_order_date',['dateFormat'=>'DMY','minYear'=>2015,'maxYear'=>date('Y')]);
						echo $this->Form->input('production_order_code');
						if ($bool_annul_permission){
							echo $this->Form->input('bool_annulled',['default'=>false]);
						}
						else {
							echo $this->Form->input('bool_annulled',['default'=>'false','onclick'=>'return false']);
						}
						echo $this->Form->input('Document.url_doc.0',['label'=>'Cargar Documento de Diseño (5 MB max)','type'=>'file']);
						echo "<div id='salesOrderInfo'>";
							echo "<div><span class='bold '>&nbsp;</span></div>";
						echo "</div>";
		
						echo $this->Form->input('ProductionOrderRemark.user_id',['label'=>'Vendedor','value'=>$loggedUserId,'type'=>'hidden']);
						echo $this->Form->input('ProductionOrderRemark.remark_text',['label'=>'Observaciones generales','rows'=>'2','required'=>false]);	
					echo "</div>";
					
					echo '<div class="col-sm-2">';
						echo '<h2>'.__('Actions').'</h2>';
						echo '<ul style="list-style:none">';
							echo '<li>'.$this->Html->link(__('List Production Orders'),['action' => 'resumen'],['class'=>'btn btn-primary']).'</li>';
							
							if ($bool_salesorder_index_permission){
                echo '<br/>';
								echo '<li>'.$this->Html->link(__('List Sales Orders'),['controller' => 'salesOrders', 'action' => 'resumen'],['class'=>'btn btn-primary']).'</li>';
							}
						echo '</ul>';
					echo '</div>';
				echo '</div>';		
      /*    
        echo '<div class="row">';
					echo '<div class="col-sm-12" id="DepartmentInstructionsTable">';
						echo '<table id="departamentos">';
							echo '<thead>';
								echo '<tr>';
                  echo '<th>Departamento</th>';
                  echo '<th>Instrucciones</th>';
                  echo '<th>Estado</th>';
                  echo '<th></th>';
								echo '</tr>';
              echo '</thead>';    
              echo '<tbody>';
              for ($i=0;$i<DEPARTMENTS_MAX;$i++){
                $departmentDefaultValue=(array_key_exists($i,$requestDepartments)?$requestDepartments[$i]['department_id']:0);
                echo '<tr 
                  class="productionorderdepartment '.($i <= count($requestDepartments)?"":" hidden").'"
                  rank="'.$i.'"
                >';
								  echo '<td class="productionorderdepartmentid">'.$this->Form->input('ProductionOrder.Department.'.$i.'.department_id',[
                    'label'=>false,
                    'default'=>0,
                    'empty'=>[0=>'-- Departamento --'],
                  ]).'</td>';
                  echo '<td class="productionorderdepartmentinstruction">'.$this->Form->input('ProductionOrder.Department.'.$i.'.ProductionOrderDepartmentInstruction.0.instruction_text',[
                    'label'=>false,
                    'type'=>'textarea',
                    'rows'=>2,
                    'instructionnumber'=>0,
                  ]).'</td>';
                  echo '<td class="productionorderdepartmentstate">'.$this->Form->input('ProductionOrder.Department.'.$i.'.ProductionOrderDepartmentState.0.production_order_state_id',[
                    'label'=>false,
                    'default'=>($i == 0?PRODUCTION_ORDER_STATE_AWAITING_PRODUCTION:PRODUCTION_ORDER_STATE_AWAITING_PREVIOUS),
                    'class'=>($i == 0?"":"fixed"),
                  ]).'</td>';
                  echo '<td><button class="removeDepartment" type="button">Remover Departamento</button></td>';
                echo '</tr>';  
              }
              echo '</tbody>';  
            echo '</table>';
            echo '<button class="addDepartment" type="button">Añadir Departamento</button>';
          echo '</div>';
        echo '</div>';  
      */  
				echo '<div class="row">';
					echo '<div class="col-sm-12" id="ProductTable">';
						echo '<table id="productos">';
							echo '<thead>';
								echo '<tr>';
									echo '<th>'.__('Product').'</th>';
									echo '<th>'.__('Description').'</th>';
									echo '<th>'.__('Operation Location').'</th>';
									echo '<th class="centered" style="width:60px;max-width:60px;"># Prod.</th>';
								echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
							$i=0;
							$amountProducts=0;
							if (!empty($requestProducts)){
								foreach ($requestProducts as $product){
									$amountProducts+=$product['ProductionOrderProduct']['product_quantity'];
                  //pr($product);
									echo '<tr product="'.$product['ProductionOrderProduct']['product_id'].'">';
										echo '<td class="productid">';
											echo $this->Form->input('ProductionOrderProduct.'.$i.'.product_id',[
                        'label'=>false,
                        'value'=>$product['ProductionOrderProduct']['product_id'],
                        'class'=>'fixed',
                        'style'=>'font-size:1.1rem;',
                      ]);
											echo $this->Form->input('ProductionOrderProduct.'.$i.'.sales_order_product_id',[
                        'label'=>false,
                        'type'=>'hidden',
                        'value'=>$product['ProductionOrderProduct']['sales_order_product_id'],
                      ]);
										echo '</td>';
										echo '<td class="productdescription">'.$this->Form->input('ProductionOrderProduct.'.$i.'.product_description',[
                      'label'=>false,
                      'value'=>$product['ProductionOrderProduct']['product_description'],
                      'readonly'=>'readonly',
                    ]).'</td>';
                    echo '<td class="operationlocationid">'.$this->Form->input('ProductionOrderProduct.'.$i.'.operation_location_id',[
                      'multiple'=>true,
                      'label'=>false,
                      'value'=>$product['ProductionOrderProduct']['operation_location_id'],
                      'empty'=>['0'=>'-- Lugar --'],
                    ]).'</td>';
										echo '<td class="productquantity amount">'.$this->Form->input('ProductionOrderProduct.'.$i.'.product_quantity',[
                      'label'=>false,
                      'value'=>$product['ProductionOrderProduct']['product_quantity'],
                      'type'=>'numeric',
                      'readonly'=>true,
                    ]).'</td>';
									echo '</tr>';	
                  for ($j=0;$j<DEPARTMENTS_MAX;$j++){
                    $departmentDefaultValue=(array_key_exists($j,$product['ProductionOrderProduct']['Department'])?$product['ProductionOrderProduct']['Department'][$j]['department_id']:0);
                    echo '<tr 
                      class="department'.($j <= count($product['ProductionOrderProduct']['Department'])?"":" hidden").'" 
                      product="'.$product['ProductionOrderProduct']['product_id'].'"
                      rank="'.$j.'"
                    >';	
                      echo '<td>';
                        echo '<button class="addProductDepartment" type="button">Añadir Departamento</button>';
                        echo '<button class="removeProductDepartment" type="button">Remover Departamento</button>';
                      echo '</td>';
                      echo '<td class="departmentid">';
                        echo $this->Form->input('ProductionOrderProduct.'.$i.'.Department.'.$j.'.department_id',[
                          'label'=>false,
                          'default'=>$departmentDefaultValue,
                          'empty'=>[0=>'-- Departamento --'],
                        ]);
                        echo '<button class="addproductionorderproductdepartmentinstruction" type="button">Añadir Instrucciones</button>';
                      echo '</td>';
                      echo '<td class="productionorderproductdepartmentinstruction">';
                        echo '<h4>Instrucciones para departamento</h4>';
                      for ($k=0;$k<INSTRUCTIONS_MAX;$k++){
                        $instructionDefaultValue=(array_key_exists($j,$product['ProductionOrderProduct']['Department']) &&
                          array_key_exists('Instruction',$product['ProductionOrderProduct']['Department'][$j]) &&
                          array_key_exists($k,$product['ProductionOrderProduct']['Department'][$j]['Instruction'])?
                          $product['ProductionOrderProduct']['Department'][$j]['Instruction'][$k]['instruction_text']:"");
                          $instructionCount=(array_key_exists($j,$product['ProductionOrderProduct']['Department']) &&
                          array_key_exists('Instruction',$product['ProductionOrderProduct']['Department'][$j]) &&
                          array_key_exists($k,$product['ProductionOrderProduct']['Department'][$j]['Instruction'])?
                          count($product['ProductionOrderProduct']['Department'][$j]['Instruction']):0);
                        echo $this->Form->input('ProductionOrderProduct.'.$i.'.Department.'.$j.'.Instruction.'.$k.'.instruction_text',[
                          'type'=>'text',
                          'rows'=>2,
                          'default'=>$instructionDefaultValue,
                          'style'=>'width:90%',
                          'div'=>['class'=>($k <= $instructionCount?'':'hidden')],
                          'label'=>[
                            'text'=>$k+1,
                            'style'=>'width:5%;'
                          ],
                          'placeholder'=>'Escriba sus instrucciones ...',
                          'instructionnumber'=>$k,
                        ]).'<br/>';
                      }
                      echo '</td>';
                      echo '<td class="departmentstatusid">';
                        echo $this->Form->input('ProductionOrderProduct.'.$i.'.Department.'.$j.'.state_id',[
                          'label'=>false,
                          'default'=>($j==0?PRODUCTION_ORDER_STATE_AWAITING_PRODUCTION:PRODUCTION_ORDER_STATE_AWAITING_PREVIOUS),
                          'class'=>($j==0?"":"fixed"),
                          'options'=>$productionOrderStates,
                        ]);
                      echo '</td>';
                    echo '</tr>';
                  }
									$i++;
									
								}
							}
                echo '<tr class="totalrow">';
									echo '<td>Total</td>';
									echo '<td></td>';
									echo '<td></td>';
									echo '<td class="productquantity amount centered"><span></span></td>';
									echo '<td></td>';
									echo '<td></td>';
								echo '</tr>';	
							echo '</tbody>';
						echo '</table>';
						
					echo '</div>';
				echo '</div>';				
				
			echo '</div>';
			
		echo '</fieldset>';
		echo $this->Form->submit('Guardar'); 
    echo $this->Form->end(); 
	}
?>
</div>
